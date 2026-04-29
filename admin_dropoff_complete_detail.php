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
        $con = mysqli_connect("localhost","root","","cp_assignment");

        if(mysqli_connect_errno()){
            echo "Failed to connect to MySQL:".mysqli_connect_error();
            exit();
        }

        if(isset($_GET['dropoff_id'])){
            $dropoff_id = mysqli_real_escape_string($con, $_GET['dropoff_id']);
            $sql = "SELECT 
                        dropoff.dropoff_id,
                        dropoff.dropoff_date,
                        dropoff.status,
                        dropoff.item_image,
                        user.username,
                        user.phone_number,
                        user.email,
                        location.location_name,
                        item.item_name,
                        item.item_id,
                        item.point_given,  
                        item_dropoff.quantity
                        
                        FROM dropoff 
                        LEFT JOIN user ON dropoff.user_id = user.user_id
                        LEFT JOIN location ON dropoff.location_id = location.location_id
                        LEFT JOIN item_dropoff ON item_dropoff.dropoff_id = dropoff.dropoff_id
                        LEFT JOIN item ON item_dropoff.item_id = item.item_id
                        WHERE dropoff.dropoff_id = '$dropoff_id'
                        AND dropoff.status = 'Complete'";
            
            $result = mysqli_query($con, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $request = mysqli_fetch_assoc($result); // Store result in $request variable
            } else {
                echo "<script>alert('No pickup request details found.'); window.location.href='admin_pickup_request.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('No pickup request selected.'); window.location.href='admin_pickup_request.php';</script>";
            exit();
        }
    ?>

    <div class="content">
        <div class="title">
            <h2>Dropoff Complete Details</h2>
        </div>

        <hr style="width: 92%; margin-left:45px;">
        <button class="back" type=submit name="back" onclick="window.location.href='admin_dropoff_complete.php'"><i class="fa-solid fa-arrow-left"></i> Back to List</button>
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
            <h3><i class="fas fa-map-marker-alt"></i> Dropoff Item</h3>
            <div class="user-detail-information">
            <div class="detail-item">
                <label><b>Items Dropoff </b></label>
                    <img src="https://drive.google.com/uc?export=view&id=<?php echo $request['item_image']; ?>" class="image-preview" id="imagePreview">
                
            <div class="detail-item">
                <label><b>Item Name: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['item_name']; ?>                
                </span>
            </div>
            <div class="detail-item">
                <label><b>Item Quantity: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['quantity']; ?>                
                </span>
            </div>
            </div>
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
    </script>

</body>
</html>