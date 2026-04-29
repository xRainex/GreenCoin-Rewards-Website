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
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error uploading file: " . $e->getMessage();
        return false;
    }
}

// Handle driver edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editbutton'])) {
    $driver_id = mysqli_real_escape_string($conn, $_POST['driver_id']);
    $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $number_plate = mysqli_real_escape_string($conn, $_POST['number_plate']);
    
    $driver_image = null;
    $driver_license = null;
    
    // Handle driver image upload if provided
    if ($_FILES["driver_image"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES["driver_image"]["tmp_name"];
        $fileName = $_FILES["driver_image"]["name"];
        $driver_image = uploadToGoogleDrive($fileTmpName, $fileName);
        if (!$driver_image) {
            echo "<script>alert('Error uploading driver image.');</script>";
            exit;
        }
    }
    
    // Handle license image upload if provided
    if ($_FILES["driver_license_image"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES["driver_license_image"]["tmp_name"];
        $fileName = $_FILES["driver_license_image"]["name"];
        $driver_license = uploadToGoogleDrive($fileTmpName, $fileName);
        if (!$driver_license) {
            echo "<script>alert('Error uploading license image.');</script>";
            exit;
        }
    }

    $sql = "UPDATE driver SET 
            driver_name='$driver_name', 
            contact_no='$contact', 
            email='$email', 
            age='$age', 
            gender='$gender', 
            address='$address', 
            number_plate='$number_plate'";
    
    // Add image updates if files were uploaded
    if ($driver_image) {
        $sql .= ", driver_image='$driver_image'";
    }
    if ($driver_license) {
        $sql .= ", driver_license_image='$driver_license'";
    }
    
    $sql .= " WHERE driver_id='$driver_id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Driver details updated successfully!');
                window.location.href='admin_driver_detail.php?driver_id=$driver_id'; 
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($conn) . "');</script>";
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
        margin:15px 0 10px 10px;
    }

    .background{
        display:flex;
        background-color:transparent;
        padding: 15px 8px 15px 8px;
        box-sizing: border-box;
        gap:15px;
    }

    .driver_frame{
        background-color:white;
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        width:60%;
        padding:15px;
        border-radius:20px;
    }

    .driver_information{
        background-color:white;
        display:flex;
        flex-direction:column;
    }

    .driver_frame h3 {
        border-bottom: 2px solid #ddd; 
        padding-bottom: 10px; /* Space between text and line */
        margin-bottom: 15px; /* Space between line and next content */
    }


    .driver-image{
        background-color: white;
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        width: 25%;
        display: flex;
        flex-direction: column;
        justify-content:center;
        align-items: center;  /* Center horizontally */
        justify-content: flex-start; /* Align to the top */
        padding:20px; /* Reduce padding for more upward movement */
        border-radius:20px;
        position: relative;
    }

    .image-background {
        width: 175px;
        height: 175px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f4f3ff;
        position: relative;
        margin-bottom: 20px; /* Add spacing between image and button */
    }

    .image-background2 {
        width: 200px;
        height: 175px;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f4f3ff;
        position: relative;
        margin-bottom: 20px; /* Add spacing between image and button */
    }

    

    .driver-action{
        margin-top:100px;
        text-align:center;
        width:100%;
    }

    .editbutton{
        background-color:#FFB703;
        padding:15px 15px;
        border:none;
        border-radius:5px;
        cursor:pointer;
        width:70%;
    }

    .editbutton i {
        margin-right: 8px; /* Adjust spacing between icon and text */
    }

    .deletebutton{
        background-color:#ff6b6b;
        padding:15px 15px;
        border:none;
        border-radius:5px;
        cursor:pointer;
        width:70%;
        margin-top:10px;
    }

    .deletebutton i {
        margin-right: 8px; /* Adjust spacing between icon and text */
    }

    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 50;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.4); 
        padding-top:15px;
        
    }

    .modal-content {
        background-color: #fefefe;
        margin: 1% auto 15% auto; 
        border: 1px solid #888;
        width: 50%;
    }

    .imgcontainer {
        text-align: center;
        margin: 15px 15px 40px 40px;
        position: relative;
    }

    .container {
        padding-left: 25px;
        padding-right: 25px;
        padding-bottom: 25px;
        padding-top: 0;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    input, select {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
        background-color:#d9d9d9;
        font-family:Arial,sans-serif;
    }

    .animate {
        -webkit-animation: animatezoom 0.6s;
        animation: animatezoom 0.6s
    }

    @-webkit-keyframes animatezoom {
        from {-webkit-transform: scale(0)} 
        to {-webkit-transform: scale(1)}
    }
        
    @keyframes animatezoom {
        from {transform: scale(0)} 
        to {transform: scale(1)}
    }

    .savebutton {
        background-color:#FFDB58;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
    }

    .image-preview-container {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .image-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 1px solid #ddd;
        object-fit: cover;
        display: block;
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

    <?php
        $con = mysqli_connect("localhost", "root", "", "cp_assignment");

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }

        if (isset($_GET['driver_id'])) {
            $driver_id = mysqli_real_escape_string($con, $_GET['driver_id']);
            $sql = "SELECT driver_id, driver_name, contact_no, email, age, gender, address, number_plate, driver_image,driver_license_image FROM driver WHERE driver_id='$driver_id'";
            $result = mysqli_query($con, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $driver_id = $row['driver_id'];
                $driver_name = $row['driver_name'];
                $contact = $row['contact_no'];
                $email = $row['email'];
                $age = $row['age'];
                $gender = $row['gender'];
                $address = $row['address'];
                $number_plate = $row['number_plate'];
                $driver_image=$row['driver_image'];
                $driver_license=$row['driver_license_image'];
            } else {
                echo "<script>alert('No driver details.'); window.location.href='admin_driver.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('No driver selected.'); window.location.href='admin_driver.php';</script>";
            exit();
        }
    ?>
    <div class="content">
        <div class="title" >
            <h2>Drivers Information Details</h2>
        </div>

        <hr style="width: 92%; margin-left:45px;">
        <button class="back" type=submit name="back" onclick="window.location.href='admin_driver.php'"><i class="fa-solid fa-arrow-left"></i> Back</button>
        <div class="background">
            <div class="driver_frame">
                <h3>Basic details</h3>
                
                <div class="driver_information">
                    <label><b>Driver Name: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($driver_name); ?>
                    </span>

                    <label><b>Driver Contact Number:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($contact); ?>
                    </span>

                    <label><b>Email:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($email); ?>
                    </span>

                    <label><b>Age:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($age); ?>
                    </span>

                    <label><b>Gender:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($gender); ?>
                    </span>

                    <label><b>Address:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($address); ?>
                    </span>

                    <label><b>Number Plate:</b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo htmlspecialchars($number_plate); ?>
                    </span>
                </div>
            </div>
            <div class="driver-image">
                <h3>Driver Profile Picture:</h3>
                <div class="image-background">
                    <div>
                        <iframe src="https://drive.google.com/file/d/<?= $driver_image ?>/preview" width="150" height="150" style="border-radius:50%; border:none;"></iframe>              
                    </div>
                </div>
                <h3>Driver License Picture:</h3>
                <div class="image-background2">
                    <div>
                        <iframe src="https://drive.google.com/file/d/<?= $driver_license ?>/preview" width="175" height="150" style="border:none;"></iframe>              
                    </div>
                </div>
                
                <div class="driver-action">
                <button class="editbutton" type="button" onclick="openEditModal('<?php echo $driver_id; ?>','<?php echo htmlspecialchars($driver_name); ?>','<?php echo htmlspecialchars($contact); ?>','<?php echo htmlspecialchars($email); ?>','<?php echo htmlspecialchars($age); ?>','<?php echo htmlspecialchars($gender); ?>','<?php echo htmlspecialchars($address); ?>','<?php echo htmlspecialchars($number_plate); ?>')">
                    <b><i class="fa-solid fa-pen"></i> Edit</b>
                </button>
                    <button class="deletebutton" type="submit" name="deletebutton"><b><i class="fa-solid fa-trash"></i> Delete</b></button>
                </div> 
                
            </div>
        </div>
    </div>
    <div id="editdriver" class="modal">
    <form class="modal-content animate" action="#" method="post" enctype="multipart/form-data">
        <div class="imgcontainer">
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="container">
            <h2>Edit Driver Details</h2>
            <p>Please update the driver details below.</p>
            <br>
            <input type="hidden" id="edit_driver_id" name="driver_id">

            <label><b>Driver Name:</b></label>
            <input type="text" id="edit_driver_name" name="driver_name" placeholder="Enter driver name" required><br><br>

            <label><b>Contact Number:</b></label>
            <input type="text" id="edit_contact" name="contact_no" placeholder="Enter driver contact number" required><br><br>

            <label><b>Email:</b></label>
            <input type="email" id="edit_email" name="email" placeholder="Enter driver email address" required><br><br>

            <label><b>Age:</b></label>
            <input type="number" id="edit_age" name="age" placeholder="Enter driver age" required><br><br>

            <label><b>Gender:</b></label>
            <input type="text" id="edit_gender" name="gender" placeholder="Enter driver gender" required><br><br>

            <label><b>Address:</b></label>
            <input type="text" id="edit_address" name="address" placeholder="Enter driver home address" required><br><br>

            <label><b>Number Plate:</b></label>
            <input type="text" id="edit_number_plate" name="number_plate" placeholder="Enter driver's number plate" required><br><br>

            <label><b>Driver Image:</b></label>
            <input type="file" id="edit_driver_image" name="driver_image" accept="image/*" onchange="previewImage(event, 'driver-image-preview-edit')">
                <div style="margin-top: 10px;">
                    <img id="driver-image-preview-edit" 
                        src="https://drive.google.com/thumbnail?id=<?= $driver_image ?>" 
                        style="width:100px; height:100px; border-radius:50%; border: 1px solid #ddd;">
                </div>

            <label><b>Driver License Image:</b></label>
            <input type="file" id="edit_driver_license_image" name="driver_license_image" accept="image/*" onchange="previewImage(event, 'license-image-preview-edit')">
                <div style="margin-top: 10px;">
                    <img id="license-image-preview-edit" 
                        src="https://drive.google.com/thumbnail?id=<?= $driver_license ?>" 
                        style="width:100px; height:100px; border-radius:50%; border: 1px solid #ddd;">
                </div>

            <button type="submit" class="savebutton" name="editbutton"><b>Save Changes</b></button>
        </div>
    </form>
    </div>
</div>
    <?php
        if (isset($_POST['editbutton'])) {
            $driver_id = mysqli_real_escape_string($con, $_POST['driver_id']);
            $driver_name = mysqli_real_escape_string($con, $_POST['driver_name']);
            $contact = mysqli_real_escape_string($con, $_POST['contact_no']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $age = mysqli_real_escape_string($con, $_POST['age']);
            $gender = mysqli_real_escape_string($con, $_POST['gender']);
            $address = mysqli_real_escape_string($con, $_POST['address']);
            $number_plate = mysqli_real_escape_string($con, $_POST['number_plate']);

            $sql = "UPDATE driver SET driver_name='$driver_name', contact_no='$contact', email='$email', age='$age', gender='$gender', address='$address', number_plate='$number_plate' WHERE driver_id='$driver_id'";

            if (mysqli_query($con, $sql)) {
                echo "<script>
                        alert('Driver details updated successfully!');
                        window.location.href='admin_driver_detail.php'; 
                    </script>";
            } else {
                echo "<script>alert('Error updating record: " . mysqli_error($con) . "');</script>";
            }
        }
    ?>
    

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
</script>
<script>
    function openEditModal(driver_id, driver_name, contact, email, age, gender, address, number_plate) {
        document.getElementById("edit_driver_id").value = driver_id;
        document.getElementById("edit_driver_name").value = driver_name;
        document.getElementById("edit_contact").value = contact;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_age").value = age;
        document.getElementById("edit_gender").value = gender;
        document.getElementById("edit_address").value = address;
        document.getElementById("edit_number_plate").value = number_plate;

        // Add event listeners for image preview
        document.getElementById("edit_driver_image").addEventListener("change", function(event) {
            previewImage(event, 'driver-image-preview-edit');
        });
        
        document.getElementById("edit_driver_license_image").addEventListener("change", function(event) {
            previewImage(event, 'license-image-preview-edit');
        });

        document.getElementById("editdriver").style.display = "block";
    }

    function closeModal() {
            document.getElementById("editdriver").style.display = "none";
    }

    function previewImage(event, previewId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImage(inputId, previewId) {
        document.getElementById(inputId).value = '';
        const preview = document.getElementById(previewId);
        preview.src = preview.dataset.originalSrc || '#'; // Fallback to original image or placeholder
        preview.style.display = "block";
    }
</script>


</body>
</html>