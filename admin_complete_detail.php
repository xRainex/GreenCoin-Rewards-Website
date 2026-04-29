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

    .modal-details span {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        display: block;
    }

    .total-points-container {
        grid-column: span 2; /* Make it span both columns */
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

.status-container {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-left: 0;
}

.status-container label {
    margin: 0;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'assign_driver') {
                $pickup_request_id = mysqli_real_escape_string($con, $_POST['pickup_request_id']);
                
                // Calculate total points
                $point_query = "SELECT item.point_given, item_pickup.quantity, 
                                pickup_request.user_id, 
                                SUM(item.point_given * item_pickup.quantity) AS total_points
                                FROM pickup_request
                                LEFT JOIN item_pickup ON item_pickup.pickup_request_id = pickup_request.pickup_request_id
                                LEFT JOIN item ON item_pickup.item_id = item.item_id
                                WHERE pickup_request.pickup_request_id = '$pickup_request_id'
                                GROUP BY pickup_request.pickup_request_id";
                
                $point_result = mysqli_query($con, $point_query);
                $point_data = mysqli_fetch_assoc($point_result);
                
                if ($point_data) {
                    $points = $point_data['total_points'];
                    $user_id = $point_data['user_id'];
                    
                    // Start transaction
                    mysqli_begin_transaction($con);
                    
                    try {
                        // Update pickup request with points and status
                        $update_request = "UPDATE pickup_request 
                                         SET status = 'Completed', 
                                             total_point_earned = $points 
                                         WHERE pickup_request_id = '$pickup_request_id'";
                        
                        if (!mysqli_query($con, $update_request)) {
                            throw new Exception("Failed to update pickup request");
                        }
                        
                        // Update user's points
                        $update_points = "UPDATE user SET points = points + $points WHERE user_id = '$user_id'";
                        
                        if (!mysqli_query($con, $update_points)) {
                            throw new Exception("Failed to update user points");
                        }
                        
                        // Commit transaction
                        mysqli_commit($con);
                        
                        echo "<script>
                                alert('Points assigned successfully!');
                                window.location.href='admin_pickup_assign.php'; 
                              </script>";
                        exit();
                        
                    } catch (Exception $e) {
                        // Rollback on error
                        mysqli_rollback($con);
                        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Could not retrieve point information');</script>";
                }
            }
        }

        if(isset($_GET['pickup_request_id'])){
            $pickup_id = mysqli_real_escape_string($con, $_GET['pickup_request_id']);
            $sql = "SELECT 
                pickup_request.pickup_request_id,
                pickup_request.time_slot_id,
                pickup_request.address,
                pickup_request.status,
                pickup_request.datetime_submit_form,
                pickup_request.item_image,
                pickup_request.remark,
                pickup_request.total_point_earned,
                user.username,
                user.email,
                user.phone_number,
                time_slot.date AS pickup_date, 
                time_slot.time AS pickup_time,
                item_pickup.quantity,
                item.item_name,
                item.point_given,
                driver.driver_name
            FROM pickup_request 
            LEFT JOIN driver on pickup_request.driver_id=driver.driver_id
            LEFT JOIN item_pickup ON item_pickup.pickup_request_id = pickup_request.pickup_request_id
            LEFT JOIN item ON item_pickup.item_id = item.item_id
            LEFT JOIN user ON pickup_request.user_id = user.user_id
            LEFT JOIN time_slot ON pickup_request.time_slot_id = time_slot.time_slot_id
            WHERE pickup_request.pickup_request_id = '$pickup_id'";
            
            $result = mysqli_query($con, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $request = mysqli_fetch_assoc($result); // Store result in $request variable
                $time_slot_id = $request['time_slot_id'];
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
            <h2>Pickup Request Details</h2>
        </div>

        <hr style="width: 92%; margin-left:45px;">
        <button class="back" type=submit name="back" onclick="window.location.href='admin_complete_request.php'"><i class="fa-solid fa-arrow-left"></i> Back to List</button>
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
                    <label><b>Request Date: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo date('d M Y H:i', strtotime($request['datetime_submit_form'])); ?>
                    </span>
                </div>
            </div>
        </div>
            
        <div class="user-detail">
            <h3><i class="fas fa-map-marker-alt"></i> Pickup Information</h3>
            <div class="user-detail-information">
            <div class="detail-item">
                <label><b><Address></Address>Address: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['address']; ?>
                </span>
            </div>
            <div class="detail-item">
                <label><b>Pickup Date: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo date('d M Y', strtotime($request['pickup_date'])); ?>                    
                </span>
            </div>
            <div class="detail-item">
                <label><b>Pickup Time: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo date('H:i', strtotime($request['pickup_time'])); ?>
                </span>
            </div>
            </div>
        </div>

        <div class="user-detail">
            <h3><i class="fas fa-box-open"></i> Items for Pickup</h3>
            <div class="user-detail-information">
            <div class="detail-item">
                <label><b><Address></Address>Items for pickup: </b></label>
                    <iframe src="https://drive.google.com/file/d/<?php echo $request['item_image']; ?>/preview" width="250" height="150" style="border:none;" margin="10px"></iframe>              
            </div>
            <div class="detail-item">
                <label><b>Item: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['item_name']; ?>                    
                </span>
            </div>
            <div class="detail-item">
                <label><b>Quantity: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['quantity']; ?>                    
                </span>
            </div>
            <div class="detail-item">
                <label><b>Remark: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['remark']; ?>                    
                </span>
            </div>
            <div class="detail-item">
                <label><b>Driver Incharge: </b></label>
                <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['driver_name']; ?>                    
                </span>
            </div>
            
            </div>
        </div>

        <div class="user-detail">
            <h3><i class="fa-solid fa-chart-simple"></i>Pickup Status</h3>
            <div class="user-detail-information">
                <div class="detail-item">
                    <label><b>Status: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['status']; ?>                    
                </span>
                </div>
                <div class="detail-item">
                    <label><b>Point Given: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                    <?php echo $request['total_point_earned']; ?>                    
                </span>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-details" class="modal">
    <form class="modal-content animate" action="#" method="post">
        <div class="imgcontainer">
            <span class="close" onclick="closeModal('assigndriver')">&times;</span>
        </div>
        <div class="container">
            <h2><i class="fas fa-coins"></i> Assign Points</h2>
            <p>Confirm points assignment for this pickup request</p>
            
            <div class="modal-details">
                <div>
                <label><b>Item Category: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['item_name']; ?>                    
                    </span>
                </div>
                <div>
                <label><b>Quantity: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['quantity']; ?>                    
                    </span>
                </div>
                <div>
                <label><b>Points Per Item: </b></label>
                    <span style="background-color: #d9d9d9; padding: 12px 15px; margin-top:6px; margin-bottom:15px; border-radius: 5px;">
                        <?php echo $request['point_given']; ?>                    
                    </span>
                </div>
                <div>
            </div>
            <div class="total-points-container">
                <label><b>Total Points: </b></label>
                <span class="total-points-value">
                    <?php $total_points = $request['point_given'] * $request['quantity'];echo htmlspecialchars($total_points); ?>
                </span>
            </div>
            
            <input type="hidden" name="pickup_request_id" value="<?php echo $pickup_id; ?>">
            <input type="hidden" name="action" value="assign_driver">
        </div>  
            <button type="submit" class="confirm-button">Confirm Assignment</button>
       
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

        function openAssignPointModal() {
            document.getElementById('modal-details').style.display = 'block';
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