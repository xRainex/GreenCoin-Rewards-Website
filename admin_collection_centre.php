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

    .map-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 87.5%; 
        height: 400px; 
        margin: 30px auto; 
        border: 2px solid #ccc; 
        border-radius: 10px; 
    }

    .detail{
        display:flex;
        justify-content:flex-end;
        width:88.9%;
        margin:30px auto;
    }

    .addcollectionbutton{
        background-color:rgb(106, 150, 30);
        padding:10px 20px;
        font-size:16px;
        float:right;         
        cursor:pointer;
        border-radius:8px;
        border:2px solid rgb(106, 150, 30);
        color:white;
    }

    .addcollectionbutton:hover{
        background-color:transparent;
        color:rgb(73, 110, 9);
    }

    .detail-card{
        display:flex;
        flex-direction:column;
        width:85.5%;
        margin:15px auto;
        background-color: #F9FFA4;
        padding:20px;
        border-radius:8px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); 
        transition: box-shadow 0.3s ease-in-out;
    }

    .detail-card:hover{
        box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.2);
    }

    .card-header{
        font-size:18px;
        font-weight:bold;
    }

    .editbutton{
        background-color:rgb(173, 197, 224);
        padding:5px 10px;
        font-size:16px;
        text-align:right;
        cursor:pointer;
        border:none;
        border-radius:8px;
    }

    .deletebutton{
        background-color:rgb(205, 220, 237);
        padding:5px 10px;
        text-align:center;
        cursor:pointer;
        border:none;
        font-size:16px;
        border-radius:8px;
    }

    .no-content {
        display:flex;
        flex-direction:column;
        width:70%;
        margin:15px auto;
        background-color:#d9d9d9;
        text-align:center;
        border-radius:8px;
        align-items:center;
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
        padding-top:2%;
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

    .modal-details span {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        display: block;
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

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .imgcontainer {
        text-align: right;
        margin: 0 0 15px 0;
        position: relative;
    }

    input[type=text],textarea {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
        background-color:#d9d9d9;
        font-family:Arial,sans-serif;
        border-radius:5px;
    }

    .savechanges{
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
        display: block; 
    }

    .info{
        display:none;
        margin-top:10px;
    }

</style>
</head>
<body>
    <div class="sidebar">
        <div>
            <img src="User-Logo.png" style="width: 200px; margin-bottom: 25px; background-color: #78A24C; padding: 10px; border-radius: 10px; cursor: pointer; margin-left: 13px;" onclick="AdminHomePage()">
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
        if (!$con) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }

        $sql = "SELECT location_id, location_name, address, contact_no, description FROM location ORDER BY location_name";
        $result = mysqli_query($con, $sql);
        $locations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $locations[] = $row;
        }
        
        $locationsJSON = json_encode($locations);
    ?>
    <div class="content">
        <div class="title">
            <h2> Collection Centre Location </h2>
        </div>
        
        <hr style="width: 92%; margin-left:45px;">
        <div class="map-container" id="map"></div>
        <div class="detail">
            <button class="addcollectionbutton" onclick="openAddModal()" name="addbutton"><i class="fa-solid fa-plus" style="padding-right:10px;"></i>Add Collection Center</button>
        </div>

        <?php if (!empty($locations)) { ?>
        <?php foreach ($locations as $location) { ?>
            <div class="detail-card" 
                onclick="toggleInfo('<?php echo $location['location_id']; ?>')" 
                style="cursor: pointer; padding: 20px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 5px; background: #f9f9f9;">

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <b><?php echo htmlspecialchars($location['location_name']); ?></b>
                    <div style="display: flex; gap: 5px;">
                        <!-- Prevent event propagation for buttons -->
                        <button class="editbutton" onclick="event.stopPropagation(); openEditModal(
                            '<?php echo $location['location_id']; ?>', 
                            '<?php echo htmlspecialchars($location['location_name']); ?>', 
                            '<?php echo htmlspecialchars($location['address']); ?>', 
                            '<?php echo htmlspecialchars($location['contact_no']); ?>', 
                            '<?php echo htmlspecialchars($location['description']); ?>')"><i class="fa-solid fa-pen"></i></button>
                        
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this collection centre?');" style="margin: 0;">
                            <input type="hidden" name="delete_id" value="<?php echo $location['location_id']; ?>">
                            <button type="submit" name="deletebutton" class="deletebutton" onclick="event.stopPropagation();"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </div>

                <!-- Hidden Detail Section -->
                <div id="info-<?php echo $location['location_id']; ?>" class="info" style="display: none; margin-top: 5px; color: #333;">
                    <p><b>Address: </b><?php echo htmlspecialchars($location['address']); ?></p>
                    <p><b>Contact Number: </b><?php echo htmlspecialchars($location['contact_no']); ?></p>
                    <p><b>Description: </b><?php echo htmlspecialchars($location['description']); ?></p>
                </div>
            </div>
        <?php } ?>
        <?php } else { ?>
            <div class="no-content">
                <img src="admin_nothing_here.png" style="width:150px;">
                <p>No centre details are added.</p>
            </div>
        <?php } ?>
    </div>

    <div id="addcentre" class="modal">
        <form class="modal-content" action="#" method="post">
            <div class="imgcontainer">
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
        
            <div class="container">
                <h2>Add Collection Centre</h2>
                <p>Fill in the details below to add a new collection centre.</p>
                <br>
                <label><b>Location Name:</b></label>
                <input type="text" id="add-name" name="location_name" placeholder="Enter collection centre name" required><br><br>

                <label><b>Address:</b></label>
                <textarea id="add-address" name="address" placeholder="Enter collection centre address"required></textarea><br><br>

                <label><b>Contact Number:</b></label>
                <input type="text" id="add-contact" name="contact_no" placeholder="Enter collection centre contact number" required><br><br>

                <label><b>Description:</b></label>
                <input type="text" id="add-description" name="description" placeholder="Enter collection centre description" required><br><br>
                
                <button class="savechanges" type="submit" name="addbutton">Add Collection Centre</button>
            </div>
        </form>
    </div>
    <?php
        if(isset($_POST['addbutton'])){
        $con=mysqli_connect("localhost","root","","cp_assignment");
        if(!$con){
            echo "Failed to connect to MySQL:".mysqli_connect_error();
        }

        $centrename=mysqli_real_escape_string($con,$_POST['location_name']);
        $address=mysqli_real_escape_string($con,$_POST['address']);
        $contact_no=mysqli_real_escape_string($con,$_POST['contact_no']);
        $description=mysqli_real_escape_string($con,$_POST['description']);

        if(empty($centrename) || empty($address) || empty($contact_no) || empty($description)){
            echo "<script>alert('Error: Please enter full details!');</script>";
        }else{
            $sql_insert="INSERT INTO location(location_name,address,contact_no,description)
            VALUES ('$centrename','$address','$contact_no','$description')";
            
        if (mysqli_query($con, $sql_insert)) {
            echo "<script>alert('Collection Centre Added Successfully'); window.location.href='admin_collection_centre.php';</script>";
        } else {
            echo "<script>alert('Error Adding Collection Centre: " . mysqli_error($con) . "');</script>";
        }
        }
        }
    ?>


    <div id="editcentre" class="modal">
        <form class="modal-content animate" action="#" method="post">
            <div class="imgcontainer">
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <div class="container">
                <h2>Edit Collection Centre</h2>
                <p>Please update the collection centre details below.</p>
                <br>
                <input type="hidden" id="edit-id" name="location_id">
                <label><b>Location Name:</b></label>
                <input type="text" id="edit-name" name="location_name" placeholder="Enter collection centre name" required><br><br>

                <label><b>Address:</b></label>
                <textarea id="edit-address" name="address" placeholder="Enter collection centre address" required></textarea><br><br>

                <label><b>Contact Number:</b></label>
                <input type="text" id="edit-contact" name="contact_no" placeholder="Enter collection centre contact number" required><br><br>

                <label><b>Description:</b></label>
                
                <input type="text" id="edit-description" name="description" placeholder="Enter collection centre description"required><br><br>
                <button class="savechanges" type="submit" name="editbutton">Save Changes</button>
            </div>
        </form>
    </div>
    <?php
        if(isset($_POST['editbutton'])){
            $location_id = mysqli_real_escape_string($con, $_POST['location_id']);                   
            $locationName=mysqli_real_escape_string($con,$_POST['location_name']);
            $locationAddress=mysqli_real_escape_string($con,$_POST['address']);
            $locationContact=mysqli_real_escape_string($con,$_POST['contact_no']);
            $locationDescription=mysqli_real_escape_string($con,$_POST['description']);

            $update_sql = "UPDATE location 
                            SET location_name='$locationName', address='$locationAddress', 
                            contact_no='$locationContact', description='$locationDescription' 
                            WHERE location_id='$location_id'";

            if (mysqli_query($con, $update_sql)) {
                echo "<script>alert('Collection Centre updated successfully!'); window.location.href='admin_collection_centre.php';</script>";
            } else {
                echo "<script>alert('Error updating record: " . mysqli_error($con) . "');</script>";
            }
        }

        // Handle delete request
        if (isset($_POST['deletebutton'])) {
            $location_id = mysqli_real_escape_string($con, $_POST['delete_id']);

            $delete_sql = "DELETE FROM location WHERE location_id='$location_id'";
            
            if (mysqli_query($con, $delete_sql)) {
                echo "<script>alert('Collection Centre deleted successfully'); window.location.href='admin_collection_centre.php';</script>";
            } else {
                echo "<script>alert('Error deleting record: " . mysqli_error($con) . "');</script>";
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
    // Convert PHP array to JavaScript
    var locations = <?php echo $locationsJSON; ?>;

    function initMap() {
        var map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 3.0555, lng: 101.7005 }, // Default to KL
            zoom: 14,
        });

        var locations = <?php echo json_encode($locations); ?>; 
        var geocoder = new google.maps.Geocoder();

        locations.forEach(function (location) {
            geocoder.geocode({ address: location.address }, function (results, status) {
                if (status === "OK") {
                    var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title: location.location_name,
                    });

                    var infoWindow = new google.maps.InfoWindow({
                        content: "<b>" + location.location_name + "</b><br>" + location.address,
                    });

                    marker.addListener("click", function () {
                        infoWindow.open(map, marker);
                    });
                } else {
                    console.log("Geocode failed: " + status);
                }
            });
        });
    }



    function openEditModal(id, name, address, contact, description) {
        document.getElementById("editcentre").style.display = "block";
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-name").value = name;
        document.getElementById("edit-address").value = address;
        document.getElementById("edit-contact").value = contact;
        document.getElementById("edit-description").value = description;
    }

    function openAddModal(){
        document.getElementById("addcentre").style.display = "block";
    }


    function closeModal() {
        document.getElementById("editcentre").style.display = "none";
    }

    function closeAddModal() {
        document.getElementById("addcentre").style.display = "none";
    }


    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this collection centre?")) {
            document.getElementById('delete-id').value = id;
        }
    }

    
    function toggleInfo(locationId) {
        var infoDiv = document.getElementById("info-" + locationId);
        if (infoDiv.style.display === "none" || infoDiv.style.display === "") {
            infoDiv.style.display = "block";
        } else {
            infoDiv.style.display = "none";
        }
    }
    </script>
    <script 
        async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuzWuyPcG8GwD5dRIHV0sFm3FdvJW_y3o&callback=initMap">
    </script>
</body>
</html>