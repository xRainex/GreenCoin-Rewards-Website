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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $name = mysqli_real_escape_string($conn, $_POST['driver_name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $age = mysqli_real_escape_string($conn, $_POST['age']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $number_plate = mysqli_real_escape_string($conn, $_POST['number_plate']);
        $fileTmpName=$_FILES["driver_image"]["tmp_name"];
        $fileName=$_FILES["driver_image"]["name"];
        $imageTmpName=$_FILES["driver_license_image"]["tmp_name"];
        $imageName=$_FILES["driver_license_image"]["name"];

        if ($_FILES["driver_image"]["error"] !== UPLOAD_ERR_OK || $_FILES["driver_license_image"]["error"] !== UPLOAD_ERR_OK) {
            echo "<script>alert('File upload error. Please try again.');</script>";
            exit;
        }

        if (empty($name) || empty($contact) || empty($email) || empty($age) || empty($gender) || empty($address) || empty($number_plate)) {
            echo "<script>alert('Please fill in all required fields.');</script>";
            exit;
        }

        $fileID=uploadToGoogleDrive($fileTmpName, $fileName);
        $fileID2=uploadToGoogleDrive($imageTmpName, $imageName);

        if (!$fileID || !$fileID2) {
            echo "<script>alert('File upload to Google Drive failed. Please check your file permissions and try again.');</script>";
            exit;
        }
    
        $sql="INSERT INTO driver (driver_name, contact_no, email, age, gender, address, number_plate, driver_image, driver_license_image)
                VALUES ('$name', '$contact', '$email', '$age', '$gender', '$address', '$number_plate', '$fileID', '$fileID2')";
    
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Driver added successfully!');window.location.href='admin_driver.php';</script>";
        } else {
            echo "<script>alert('Error adding driver: " . mysqli_error($conn) . "');</script>";
        }
    
    }
    $con = mysqli_connect("localhost", "root", "", "cp_assignment");
    $result = mysqli_query($con, "SELECT * FROM driver");
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

    .menu li.active{
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

    .upper-content{
        display:flex;
        gap:39.5%;
        
    }

    .addDriver{
        background-color: rgb(106, 150, 30);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid rgb(106, 150, 30);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        justify-content:flex-end;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        margin-top:9px;
    }

    .addDriver:hover {
        background-color:transparent;
        color:rgb(73, 110, 9);
    }

    .search-container {
        position: relative;
        width: 500px;
        left:75px;
    }

    .search-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7d8fa1;
    }

    #search_driver {
        width: 100%;
        padding: 12px 20px 12px 45px;
        border-radius: 30px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: all 0.3s;
        background-color: white;
    }

    #search_driver:focus {
        outline: none;
        border-color: #78A24C;
        box-shadow: 0 0 0 3px rgba(120, 162, 76, 0.2);
    }

    .detail-card{
        display:flex;
        flex-direction:column;
        width:83%;
        margin:15px auto;
        background-color:white;
        padding:10px;
        border-radius:12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 25px;
    }

    #driver_table img {
        display: block;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 16px;
    }

    table.center {
        margin-left: auto; 
        margin-right: auto;
    }

    th {
        padding: 15px;
        text-align: left;
        border-bottom: 2px solid #7d7979;
    }
    
    td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    .row_hover:hover {background-color: #FBF8F5; cursor: pointer} //here

    .show{
        display:block;
    }

    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.4); 
    }

    .modal-content {
        background-color: #fefefe;
        margin: 1% auto 15% auto; 
        border: 1px solid #888;
        width: 50%;
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

    .imgcontainer {
        text-align: center;
        margin: 15px 15px 15px 15px;
        position: relative;
    }

    .container {
        padding-left: 25px;
        padding-right: 25px;
        padding-bottom: 25px;
        padding-top: 0;
    }

    input[type=text] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
        background-color:white;
        font-family:Arial,sans-serif;
    }

    #addimage {
        display: flex;
        flex-direction: column;
    }

    #addimage label {
        margin-top: 10px;
        font-weight: bold;
    }

    #addimage input[type="text"],
    #addimage input[type="file"] {
        padding: 10px;
        margin: 5px 0 15px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
    }

    .savechanges{
        background-color:#F9FFA4;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
    }

    .delete-button{
        background-color: #ff6b6b;        
        border:none;
        padding: 15px;
        border-radius:20px;
        color:white;
        cursor: pointer;
        font-size: 14px;
        align-items: center;
        gap: 5px;
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
            <h2>Drivers Information</h2>
        </div>
        <hr style="width: 92%; margin-left:45px;">
        <div class="upper-content">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search_driver" onkeyup="filterDriver()" placeholder="Search driver name...">
            </div>
            <div>
                <button class="addDriver" name="addbutton" onclick="openAddModal()"><i class="fa-solid fa-plus"></i>Add Driver</button>
            </div>
        </div>
        <div class="detail-card">
        <?php
            // Database connection
            $con = mysqli_connect("localhost", "root", "", "cp_assignment");

            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            // Fetch driver details
            $sql = "SELECT driver_id, driver_name, contact_no, email, age, gender, address, number_plate, driver_image, driver_license_image FROM driver";
            $result = mysqli_query($con, $sql);

            if (!$result) {
                echo "Error fetching data: " . mysqli_error($con);
            }
            ?>

            <div>
                <table id="driver_table">
                    <tr>
                        <th style="width:10%;"></th>
                        <th style="width:10%;">Name</th>
                        <th style="width:10%;"></th>
                        <th style="width:15%;">Car Plate</th>
                        <th style="width:15%;">Email</th>
                        <th style="width:15%;">Phone</th>
                        <th style="width:15%;"></th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $fileID = $row['driver_image'];                     
                    ?>  
                    
                    <tr class="row_hover" onclick="window.location.href='admin_driver_detail.php?driver_id=<?php echo $row['driver_id'];?>'">
                        <td>
                            <?php if (!empty($row['driver_image'])): ?>
                                <iframe src="https://drive.google.com/file/d/<?= $fileID ?>/preview" width="65" height="60" style="border-radius:50%; border:none;"></iframe></td>
                            <?php else: ?> 
                                <i class="fa-solid fa-user-tie" style="font-size: 50px; color: gray; padding-left:12px"></i>
                                <?php endif; ?>
                        </td>         
                        <td><?php echo $row['driver_name'];?></td>
                        <td></td>
                        <td><?php echo $row['number_plate']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['contact_no']; ?></td>
                        <td>
                        <form method="POST" onsubmit="return confirmDelete()" style="display:inline;" onclick="event.stopPropagation();">
                        <input type="hidden" name="delete_driver_id" value="<?php echo $row['driver_id']; ?>">
                                <button type="submit" class="delete-button"><i class="fa-solid fa-trash"></i> Delete</button>
                            </form>
                        </td>

                    </tr>
                <?php
                }
                ?>    
                </table>
            </div>
           
        </div>
    </div>
    <!-- start add driver here -->
    <div id="adddriver" class="modal">
        <form id="addimage" enctype="multipart/form-data" method="POST" action="admin_driver.php" class="modal-content animate">
            <div class="imgcontainer">
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <div class="container">
                <h2>Add Driver</h2>
                <p>Fill in the details below to add a driver.</p>
                <br>
                <label><b>Name:</b></label>
                <input type="text" id="add-name" name="driver_name" placeholder="Enter driver name" required><br><br>

                <label><b>Contact Number:</b></label>
                <input type="text" id="add-contactno" name="contact_no" placeholder="Enter driver contact number" required><br><br>

                <label><b>Email Address:</b></label>
                <input type="text" id="add-email" name="email" placeholder="Enter driver email address"required><br><br>

                <label><b>Age:</b></label>
                <input type="text" id="add-age" name="age" placeholder="Enter driver age" required><br><br>

                <label><b>Gender:</b></label>
                <input type="text" id="add-gender" name="gender" placeholder="Enter driver gender" required><br><br>

                <label><b>Address:</b></label>
                <input type="text" id="add-address" name="address" placeholder="Enter driver house address" required><br><br>

                <label><b>Driver's Car Number Plate:</b></label>
                <input type="text" id="add-number-plate" name="number_plate" placeholder="Enter driver car number plate" required><br><br>

                <label><b>Driver Image:</b></label>
                <input type="file" id="add-driver-image" name="driver_image" accept="image/*" onchange="previewImage(event)">
                <img id="driver-image-preview" src="#" alt="Selected Image" style="display:none; width:100px; height:100px; border-radius:50%; margin-top:10px;">

                
                <label><b>Driver License Image:</b></label>
                <input type="file" id="add-driver-license-image" name="driver_license_image" accept="image/*" required><br><br>
            
                <button class="savechanges" type="submit" name="submit">Add Driver</button>
            </div>
         </form>
    </div>
  
        

   
    </div>
    <!-- delete function -->
    <?php
        $con = mysqli_connect("localhost", "root", "", "cp_assignment");

        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_driver_id'])) {
            $driver_id = mysqli_real_escape_string($con, $_POST['delete_driver_id']);

            $delete_query = "DELETE FROM driver WHERE driver_id = '$driver_id'";
            if (mysqli_query($con, $delete_query)) {
                echo "<script>alert('Driver deleted successfully.'); window.location.href = 'admin_driver.php';</script>";
            } else {
                echo "<script>alert('Error deleting driver. Please try again.');</script>";
            }
        }

        $result = mysqli_query($con, "SELECT * FROM driver");
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


        function openAddModal(){
            document.getElementById("adddriver").style.display = "block";
        }

        function closeAddModal() {
            document.getElementById("adddriver").style.display = "none";
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this driver?");
        }

        function filterDriver(){
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search_driver");
            filter = input.value.toUpperCase();
            table = document.getElementById("driver_table");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('driver-image-preview'); // Change image in the detail card
                output.src = reader.result;
                output.style.display = "block"; // Show the image
            };
            reader.readAsDataURL(event.target.files[0]);
        }


        

        
            
    </script>

</body>
</html>