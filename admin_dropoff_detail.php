<?php
require 'google-api-php-client/vendor/autoload.php'; 
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "cp_assignment";  

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function uploadToGoogleDrive($fileTmpName, $fileName) {
    $client = new Google\Client();
    $client->setHttpClient(new GuzzleHttp\Client(['verify' => false])); 
    $client->setAuthConfig('keen-diode-454703-r9-847455d54fc8.json');
    $client->addScope(Google\Service\Drive::DRIVE_FILE);

    $service = new Google\Service\Drive($client);
    $fileMetadata = new Google\Service\Drive\DriveFile([
        'name' => $fileName,
        'parents' => ['1m1vF4txoCgpJsLV1zX6k87HS0URyEIh9']
    ]);
    $content = file_get_contents($fileTmpName);
    if (!$content) {
        die("Error: Unable to read file.");
    }
    
    $mimeType = mime_content_type($fileTmpName);
    try {
        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        if($file && isset($file->id)){
            $fileID = $file->id;
            
            error_log("Uploaded file ID: $fileID");
            error_log("View URL: https://drive.google.com/file/d/$fileID/preview");
    
            $permission = new Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
                'withLink' => true
            ]);
            $service->permissions->create($fileID, $permission);
            return $fileID;
        }else{
            return false;
        }
    } catch (Exception $e) {
        echo "Error uploading file: " . $e->getMessage();
        return false;
    }
}

$dropoff_id = mysqli_real_escape_string($conn, $_REQUEST['dropoff_id']);
if(!$dropoff_id) {
    echo "<script>alert('No dropoff request selected.'); window.location.href='admin_dropoff.php';</script>";
    exit();
}


    $sql = "SELECT 
        dropoff.dropoff_id,
        dropoff.dropoff_date,
        dropoff.status,
        dropoff.total_point_earned,
        dropoff.item_image,
        user.username,
        user.email,
        user.phone_number,
        user.user_id,  
        location.location_name,
        location.address,
        item.item_name,
        item.item_id,
        item.point_given,  
        item_dropoff.quantity,
        item_dropoff.item_dropoff_id as item_dropoff_id
    FROM dropoff
    LEFT JOIN user ON dropoff.user_id = user.user_id
    LEFT JOIN location ON dropoff.location_id = location.location_id
    LEFT JOIN item_dropoff ON item_dropoff.dropoff_id = dropoff.dropoff_id
    LEFT JOIN item ON item.item_id = item_dropoff.item_id    
    WHERE dropoff.dropoff_id = '$dropoff_id'";
    
    $result = $conn->query($sql);


if (!$result || $result->num_rows === 0) {
    echo "<script>alert('No dropoff request details found.'); window.location.href='admin_dropoff.php';</script>";
    exit();
}

$request = $result->fetch_assoc();


// Handle form submission for completing dropoff
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_dropoff'])){
    try {
        $quantity = (int)$_POST['quantity'];
        $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
        
        
        if (empty($dropoff_id) || empty($quantity) || empty($item_id)) {
            throw new Exception("All fields are required");
        }
        
        if ($quantity <= 0) {
            throw new Exception("Quantity must be greater than 0");
        }

        $item_query = "SELECT point_given FROM item WHERE item_id = '$item_id'";
        $item_result = mysqli_query($conn, $item_query);
        if (!$item_result || mysqli_num_rows($item_result) === 0) {
            throw new Exception("Invalid item selected");
        }
        $item_data = mysqli_fetch_assoc($item_result);
        $point_given = $item_data['point_given'];
        $points = $point_given * $quantity;

        // Handle file upload
        $fileID = null;

        // Handle file upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {

            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            $mime = mime_content_type($_FILES['item_image']['tmp_name']);
            if (!in_array($mime, $allowed)) {
                die("Error: Only JPG, PNG, and GIF images are allowed");
            }
            $fileTmpName = $_FILES['item_image']['tmp_name'];
            $fileName = $_FILES['item_image']['name'];
            $fileID = uploadToGoogleDrive($fileTmpName, $fileName);
        
            if (!$fileID) {
                throw new Exception("Failed to upload image to Google Drive");
            }
        }

        $user_query = "SELECT user_id FROM dropoff WHERE dropoff_id = '$dropoff_id'";
        $user_result = mysqli_query($conn, $user_query);
        $user_data = mysqli_fetch_assoc($user_result);
        $user_id = $user_data['user_id'];
            // Update the dropoff record with image and status
            try {
                // Update the dropoff record
                $update_query = "UPDATE dropoff 
                                SET item_image = " . ($fileID ? "'$fileID'" : "item_image") . ", 
                                status = 'Complete',
                                total_point_earned = $points
                                WHERE dropoff_id = '$dropoff_id'";
                
                if (!mysqli_query($conn, $update_query)) {
                    throw new Exception("Failed to update dropoff request");
                } 
            
                // Update user's points
                $update_points = "UPDATE user SET points = points + $points WHERE user_id = '$user_id'";
                        
                if (!mysqli_query($conn, $update_points)) {
                    throw new Exception("Failed to update user points");
                }
            
                // Create notification
                $announcement = "Your dropoff request has been processed! You have earned {$points} points for your recycling effort. Thank you for making a difference!";                     
                $notificationQuery = "INSERT INTO user_notification(user_id, datetime, title, announcement, status) 
                                    VALUES ('$user_id', NOW(), 'Dropoff Request Completed', '$announcement', 'unread')";
                
                if (!mysqli_query($conn, $notificationQuery)) {
                    throw new Exception("Failed to create notification");
                }
                
                $update_item_dropoff = "UPDATE item_dropoff 
                                        SET quantity = $quantity 
                                        WHERE dropoff_id = '$dropoff_id' AND item_id = '$item_id'";

                if (!mysqli_query($conn, $update_item_dropoff)) {
                    throw new Exception("Failed to update item quantity");
                }
                // Commit transaction
                mysqli_commit($conn);
                
                echo "<script>
                        alert('Dropoff completed successfully!');
                        window.location.href='admin_dropoff.php?dropoff_id=$dropoff_id'; 
                      </script>";
                exit();
                
            } catch (Exception $e) {
                // Rollback on error
                mysqli_rollback($conn);
                throw $e;
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }




?>
        
    


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rewards Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color:rgba(238, 238, 238, 0.7);
    }

    .sidebar {
        width: 250px;
        height: 100vh; 
        background: #f8f9fa;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        position: fixed;
        overflow-y: auto;
        z-index: 100;
        display: flex;
        flex-direction: column;
    }

    .profile-container{
        width:100%;
        margin-top:130px;
        bottom:12px;
        margin-top:0px;
    }

    .profile {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f8f9fa ;
        border-radius: 10px;
        border:2px solid rgba(116, 116, 116, 0.76);
        padding: 15px; 
        padding-left:20px;
        width: 93%;
        position: relative;
        margin: 15px;
        box-sizing: border-box;
    }
    .profileicon {
        font-size: 30px;
        color: #333;
    } 

    .profile-info {
        font-size: 14px;
        flex-grow: 1;
        padding-left: 15px;
    }

    .profile-info p {
        margin: 0;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin-left:15px;
    }

    .menu li {
        border-radius: 5px;
    }

    .menu li a {
        text-decoration: none;
        color: black;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 10px;
        border-radius: 10px;
    }

    .menu li i{
        color:rgb(134, 134, 134);
        width: 5px;
        padding-right:18px;
    }

    .menu li.active
    {
        background-color: #E4EBE6;
        border-radius: 10px;
        color:rgb(11, 91, 19);
    }

    .menu a:hover,
    .menu a.active{
        background:#E4EBE6;
        color:rgb(11, 91, 19);
    }

    .menu li.active i,
    .menu li:hover i{
        color:green;
        background-color: #E4EBE6;
    }

    .menu li.active a,
    .menu li:hover a{
        color:rgb(11, 91, 19);
        background-color: #E4EBE6;
    }

    .notificationProfile {
        border: none; 
        background-color: transparent;
        cursor: pointer;        
        position: relative;
        display: flex; 
        align-items: center; 
        justify-content: center;
        width: 40px; 
        height: 40px;  
        border-radius: 50%; 
        font-size: 25px;
        transition: background-color 0.2s ease-in-out;
    }

    .notificationProfile:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 100%; 
        background: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        width: 150px; 
        text-align: left;
        z-index: 10;
        padding: 5px 0;
    }


    .dropdown-btn {
        border: none;
        background-color: transparent;
        cursor: pointer;
        font-size: 16px;
    }

    .dropdown a {
        display: block;
        padding: 10px;
        color: black;
        text-decoration: none;
        text-align: center;
    }

    .dropdown a:hover {
        background: #E4EBE6;
        color: rgb(11, 91, 19);
    }

    .content{
        padding:20px;
        margin-left:300px;
        width:calc(100%-270px);
        overflow-y:auto;
    }

    .title{
        display:flex;
        flex-direction: column;
        align-items:left;
        justify-content: center;  
        margin-left:73px;
    }

    .back{
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap:8px;
    }

    .user-detail{
        background-color: white;
        border-radius: 12px; /* Slightly larger radius */
        padding: 25px; /* More padding */
        margin: 20px 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Softer shadow */
        border: 1px solid #f0f0f0; /* Subtle border */
    }

    .user-detail-information{
        display:grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap:20px;
    }

    .user-detail h3 {
        color: #78A24C;
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
    }

    .detail-item span {
        background-color: #f8f9fa; /* Lighter gray */
        padding: 12px 15px;
        margin-top: 6px;
        border-radius: 8px;
        border-left: 4px solid #78A24C; /* Accent border */
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-left: auto; 
        width: fit-content; 
        justify-content:flex-end;
    }

    .assign-button{
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #78A24C;
        color: white;
        border: none;
    }

    .assign-button:hover {
        background-color: #689040;
    }

    input[type=text] {
        width: 100%;
        padding: 12px 15px;
        margin-top: 6px;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
        border-radius: 8px;
        background-color:#d9d9d9;
        font-family:Arial,sans-serif;
        border-left: 4px solid #78A24C; /* Accent border */
    }

    .file-upload-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        background-color: #78A24C;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s;
        width:62%;
    }

    .file-upload-label:hover {
        background-color: #689040;
    }

    .file-upload-input {
        display: none; /* Hide the default file input */
    }

    .image-upload-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 6px;
    }

    /* Image preview styling */
    .image-preview {
        width: 250px;
        height: 150px;
        border-radius: 8px;
        border-left: 4px solid #78A24C;
        background-color: #d9d9d9;
        margin-bottom: 10px;
        border: none;
    }

    .image-placeholder {
        width: 250px;
        height: 150px;
        border-radius: 8px;
        border-left: 4px solid #78A24C;
        background-color: #d9d9d9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #7d7979;
        margin-bottom: 10px;
    }

    .placeholder-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .item-select {
        width: 100%;
        padding: 12px 15px;
        margin-top: 6px;
        margin-bottom:15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        background-color: #d9d9d9;
        border-left: 4px solid #78A24C; /* Accent border */
    }

    .item-select:focus {
        outline: none;
        border-color: #78A24C;
        box-shadow: 0 0 5px rgba(120, 162, 76, 0.3);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow:auto;
        background-color: rgba(0,0,0,0.4);
        padding-top:5%;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        border-radius: 8px;
    }

    .container h2{
        gap:5px;
    }

    .imgcontainer {
        text-align: right;
        margin: 0 0 15px 0;
        position: relative;
    }

    .close {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        font-weight: bold;
        cursor:pointer;
    }

    .close:hover {
        color: black;
    }

    .modal-content p {
        margin-bottom: 20px;
        color: #555;
    }

    .modal-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }

    .modal-details label {
        font-weight: 600;
        color: #555;
    }

    .container span {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        display: block;
    }

    .total-points-container {
        grid-column: span 2; 
        margin-top: 15px;
    }

    .total-points-value {
        background: #e6d8b5;;
        color:black;
        padding: 15px 20px;
        margin-top: 8px;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        display: inline-block;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-align: center;
        width: 43%;
        border: 1px solid #d0c4a5; 
    }

    .confirm-button{
        text-align: center;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #78A24C;
        color: white;
        border: none;
        margin-left: auto; /* This will push the button to the right */
        display: block; /* Change from inline to block for margin to work */
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-complete {
        background-color: #FFF3CD;
        color: #856404;
    }
    
</style>
</head>
<body>
    <div class="sidebar">
        <div>
            <img src="User-Logo.png" style="width: 200px; margin-bottom: 20px; background-color: #78A24C; padding: 10px; border-radius: 10px; cursor: pointer; margin-left: 13px;" onclick="AdminHomePage()">
        </div>
        <ul class="menu">
            <li><a href="#"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
            <li><a href="#"><i class="fa-solid fa-bell"></i>Notifications</a></li>
            <li><a href="#"><i class="fa-solid fa-truck-pickup"></i>Pickup Requests</a></li>
            <li><a href="#"><i class="fa-solid fa-calendar-check"></i>Pickup Availability</a></li>
            <li><a href="#"><i class="fa-solid fa-id-card"></i>Drivers</a></li>
            <li class="active"><a href="#"><i class="fa-solid fa-truck-ramp-box"></i>Drop-Off Requests</a></li> 
            <li><a href="admin_collection_centre.php"><i class="fa-solid fa-location-dot"></i>Drop-Off Points</a></li>
            <li><a href="#"><i class="fa-solid fa-recycle"></i>Recyclable items</a></li>
            <li><a href="#"><i class="fa-solid fa-gift"></i>Rewards</a></li>
            <li><a href="#"><i class="fa-solid fa-comment"></i>Review</a></li>
            <li><a href="#"><i class="fa-solid fa-chart-column"></i>Report</a></li>
            <li><a href="#"><i class="fa-solid fa-circle-question"></i>FAQ</a></li>
        </ul>
        <div class="profile-container" style="position: relative; display: inline-block;">
            <div class="profile">
                <i class="profileicon fa-solid fa-circle-user"></i>
                <div class="profile-info">
                    <p><strong>Adeline Liow</strong></p>
                </div>
                <button class="dropdown-btn" onclick="toggleDropdown(event)">
                    <i class="fa-solid fa-chevron-down"></i>
                </button> 
            </div>
            <div class="dropdown" id="profileDropdown">
                <a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="title">
            <h2>Dropoff Details</h2>
        </div>

        <hr style="width: 92%; margin-left:45px;">
        <button class="back" type=submit name="back" onclick="window.location.href='admin_dropoff.php'"><i class="fa-solid fa-arrow-left"></i> Back to List</button>
        <div class="user-detail">
            <h3><i class="fas fa-user"></i> User Information</h3>
            <div class="user-detail-information">
                <div class="detail-item">
                    <label><b>Name: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['username']; ?>
                    </span>
                </div>
                <div class="detail-item">
                    <label><b>Contact: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['phone_number']; ?>
                    </span>
                </div>
                <div class="detail-item">
                    <label><b>Email: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['email']; ?>                    
                    </span>
                </div>
                <div class="detail-item">
                    <label><b>Dropoff Date: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo date('d M Y', strtotime($request['dropoff_date'])); ?>
                    </span>
                </div>
                <div class="detail-item">
                    <label><b>Dropoff Address: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['location_name']; ?>                    
                    </span>
                </div>
                
            </div>
        </div>
        <div class="user-detail">
            <h3><i class="fas fa-box-open"></i> Dropoff Item</h3>
            <form method="post" enctype="multipart/form-data" class="dropoff-form" id="dropoffForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">                <div class="user-detail-information">
                    <div class="detail-item">
                        <label><b>Items Dropoff: </b></label>
                        <div class="image-upload-container">
                        <?php if (!empty($request['item_image'])): ?>
                            <img src="https://drive.google.com/uc?export=view&id=<?php echo $request['item_image']; ?>" 
                                class="image-preview" id="imagePreview">
                        <?php else: ?>
                            <div class="image-placeholder" id="imagePreview">
                                <i class="fa-solid fa-image placeholder-icon"></i>
                            </div>
                        <?php endif; ?>
                            <label for="item_image" class="file-upload-label">
                                <i class="fa-solid fa-upload"></i> Upload Image
                                <input type="file" id="item_image" name="item_image" accept="image/*" class="file-upload-input" onchange="previewImage(event)">
                            </label>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <label><b>Item Name: </b></label>
                        <div>
                            <?php
                                $items_query = "SELECT item_id, item_name,point_given FROM item";
                                $items_result = mysqli_query($conn, $items_query);
                                $items = [];
                                while ($row = mysqli_fetch_assoc($items_result)) {
                                    $items[] = $row;
                                }
                            ?>
                            <select name="item_id" class="item-select" required>
                                <option value="">Select an item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?php echo $item['item_id']; ?>" data-points="<?php echo $item['point_given']; ?>">
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <label><b>Item Quantity: </b></label>
                        <div>
                            <input type="text" name="quantity" placeholder="Enter quantity" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="dropoff_id" value="<?php echo htmlspecialchars($_GET['dropoff_id']); ?>">
                </div>
                
                <div class="action-buttons">
                <button type="button" name="assign_points" class="assign-button" onclick="openAssignPointModal()">
                    <i class="fa-solid fa-coins"></i> Assign Point
                </button>
                </div>
            </form>
        </div>
    
        
        <div id="modal-details" class="modal">
    <form class="modal-content" action="#" method="post">
        <div class="imgcontainer">
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="container">
            <h2><i class="fas fa-coins"></i> Assign Points</h2>
            <p>Confirm points assignment for this dropoff request</p>
            
            <div class="modal-details">
                <div>
                    <label><b>Item Category: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;" id="modalItemName"></span>
                </div>
                <div>
                    <label><b>Quantity: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;" id="modalQuantity"></span>
                </div>
                <div>
                    <label><b>Points Per Item: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;" id="modalPointsPerItem"></span>
                </div>
            </div>
            <div class="total-points-container">
                <label><b>Total Points: </b></label>
                <span class="total-points-value" id="modalTotalPoints"></span>
            </div>
            
            <input type="hidden" name="dropoff_id" value="<?php echo $dropoff_id; ?>">
            <input type="hidden" name="action" value="assign_point">
        </div>  
        <button type="button" class="confirm-button" onclick="confirmAssignment()">
            <i class="fa-solid fa-check"></i> Confirm Assignment
        </button>            
    </form>
</div>

    <script>
        function toggleDropdown(event) {
            event.stopPropagation(); 
            let dropdown = document.getElementById("profileDropdown");
            dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
        }
        
        document.addEventListener("click", function(event) {
            let dropdown = document.getElementById("profileDropdown");
            let button = document.querySelector(".dropdown-btn");
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });

        function previewImage(event) {
            const input = event.target;
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (previewContainer.tagName === 'DIV') {
                        const img = document.createElement('img');
                        img.id = 'imagePreview';
                        img.className = 'image-preview';
                        img.src = e.target.result;
                        previewContainer.parentNode.replaceChild(img, previewContainer);
                    } else {
                        previewContainer.src = e.target.result;
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openAssignPointModal() {
            // Get form values
            const itemSelect = document.querySelector('select[name="item_id"]');
            const quantityInput = document.querySelector('input[name="quantity"]');
            
            // Validate inputs
            if (!itemSelect.value || !quantityInput.value) {
                alert('Please select an item and enter quantity');
                return false;
            }
            
            const selectedItem = itemSelect.options[itemSelect.selectedIndex].text;
            const pointsPerItem = itemSelect.options[itemSelect.selectedIndex].getAttribute('data-points');
            const totalPoints = pointsPerItem * quantityInput.value;
            
            // Update modal content dynamically
            document.getElementById('modalItemName').textContent = selectedItem;
            document.getElementById('modalQuantity').textContent = quantityInput.value;
            document.getElementById('modalPointsPerItem').textContent = pointsPerItem;
            document.getElementById('modalTotalPoints').textContent = totalPoints;
            
            // Show modal
            document.getElementById('modal-details').style.display = 'block';
            return false;
        }

    function confirmAssignment() {
        const form = document.getElementById('dropoffForm');
        
        // Create hidden input for submission
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'submit_dropoff';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        
        // Validate required fields
        if (!document.getElementById('dropoffForm').reportValidity()) {
            return;
        }
        
        // Close modal
        document.getElementById('dropoffForm').submit();        
       
    }

        function closeModal() {
            document.getElementById("modal-details").style.display = "none";
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        

        
    </script>
</body>
</html>