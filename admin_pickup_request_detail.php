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

    .menu li i {
        color:rgb(134, 134, 134);
        width: 5px;
        padding-right:18px;
    }

    .menu li.active {
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
        margin: 20px 10px;
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

    .reject-button{
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #EE6666 ;
        color: white;
        border: none;
    }

    .reject-button:hover {
        background-color: #DD5555;
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
        padding-top:10%;
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

    .driver-select {
        width: 100%;
        padding: 12px 15px;
        margin: 15px 0;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        background-color: #f8f9fa;
    }

    .driver-select:focus {
        outline: none;
        border-color: #78A24C;
        box-shadow: 0 0 5px rgba(120, 162, 76, 0.3);
    }

    .confirm-assign {
        width: 100%;
        padding: 12px;
        background-color: #78A24C;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
    }

    .confirm-assign:hover {
        background-color: #689040;
    }

    .reject-reason-select {
        width: 100%;
        padding: 12px 15px;
        margin: 15px 0;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        background-color: #f8f9fa;
    }

    .reject-reason-select:focus {
        outline: none;
        border-color: #EE6666;
        box-shadow: 0 0 5px rgba(238, 102, 102, 0.3);
    }

    .reject-confirm {
        width: 100%;
        padding: 12px;
        background-color: #EE6666;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
    }

    .reject-confirm:hover {
        background-color: #DD5555;
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
                $driver_id = mysqli_real_escape_string($con, $_POST['driver_id']);
                $time_slot_id = mysqli_real_escape_string($con, $_POST['time_slot_id']);
                
                // Update the pickup request with driver assignment
                $update_query = "UPDATE pickup_request 
                                SET driver_id = '$driver_id', status = 'Assigned' 
                                WHERE pickup_request_id = '$pickup_request_id'";
                
                if (mysqli_query($con, $update_query)) {
                    // Decrement available slots
                    $slot_update = "UPDATE time_slot 
                                SET no_driver_per_slot = no_driver_per_slot - 1 
                                WHERE time_slot_id = '$time_slot_id'";
                    mysqli_query($con, $slot_update);
        
                    $user_query = "SELECT user_id FROM pickup_request WHERE pickup_request_id = '$pickup_request_id'";
                    $user_result = mysqli_query($con, $user_query);
                    $user_data = mysqli_fetch_assoc($user_result);
                    $user_id = $user_data['user_id'];
        
                    $driver_query = "SELECT driver_name, contact_no FROM driver WHERE driver_id = '$driver_id'";
                    $driver_result = mysqli_query($con, $driver_query);
                    $driver_data = mysqli_fetch_assoc($driver_result);
        
                    $announcement = "Your pickup request has been assigned to driver: ".$driver_data['driver_name'].
                                    " (Contact Number: ".$driver_data['contact_no']."). ".
                                    "The driver will be in touch with you soon and come for the scheduled pickup.";
                    $notificationQuery = "INSERT INTO user_notification(user_id, datetime, title, announcement, status) 
                                        VALUES ('$user_id', NOW(), 'Driver Assigned Completed', '$announcement', 'unread')";
                    
                    if (!mysqli_query($con, $notificationQuery)) {
                        throw new Exception("Failed to create notification");
                    }
                    
                    // Redirect back with success message
                    echo "<script>
                                alert('Assign Driver successfully!');
                                window.location.href='admin_pickup_request.php'; 
                            </script>";
                    exit();
                } else {
                    echo "<script>alert('Error updating record: " . mysqli_error($con) . "');</script>";
                    exit();
                }
            }
        
            if ($_POST['action'] === 'reject_request') {
                $pickup_request_id = mysqli_real_escape_string($con, $_POST['pickup_request_id']);
                $reject_reason = mysqli_real_escape_string($con, $_POST['reject_reason']);
                
                // Start transaction
                mysqli_begin_transaction($con);
                
                try {
                    // Get user_id for notification
                    $user_query = "SELECT user_id FROM pickup_request WHERE pickup_request_id = '$pickup_request_id'";
                    $user_result = mysqli_query($con, $user_query);
                    $user_data = mysqli_fetch_assoc($user_result);
                    $user_id = $user_data['user_id'];
                    
                    // Update the pickup request with rejection
                    $update_query = "UPDATE pickup_request 
                                    SET status = 'Rejected'
                                    WHERE pickup_request_id = '$pickup_request_id'";
                    
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update request status");
                    }
                    
                    // Create notification with rejection reason
                    $announcement = "Your pickup request has been rejected. Reason: ".$reject_reason.
                                   ". Please submit a new request or contact support for assistance.";
                    
                    $notificationQuery = "INSERT INTO user_notification(user_id, datetime, title, announcement, status) 
                                        VALUES ('$user_id', NOW(), 'Request Rejected', '$announcement', 'unread')";
                    
                    if (!mysqli_query($con, $notificationQuery)) {
                        throw new Exception("Failed to create notification");
                    }
                    
                    // Commit transaction
                    mysqli_commit($con);
                    
                    echo "<script>
                            alert('Request rejected successfully!');
                            window.location.href='admin_pickup_request.php'; 
                          </script>";
                    exit();
                    
                } catch (Exception $e) {
                    // Rollback on error
                    mysqli_rollback($con);
                    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
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
                user.username,
                user.email,
                user.phone_number,
                time_slot.date AS pickup_date, 
                time_slot.time AS pickup_time,
                item_pickup.quantity,
                item.item_name
            FROM pickup_request 
            LEFT JOIN item_pickup ON item_pickup.pickup_request_id = pickup_request.pickup_request_id
            LEFT JOIN item ON item_pickup.item_id = item.item_id
            LEFT JOIN user ON pickup_request.user_id = user.user_id
            LEFT JOIN time_slot ON pickup_request.time_slot_id = time_slot.time_slot_id
            WHERE pickup_request.pickup_request_id = '$pickup_id'";
            
            $result = mysqli_query($con, $sql);
        
            if ($result && mysqli_num_rows($result) > 0) {
                $request = mysqli_fetch_assoc($result);
                $time_slot_id = $request['time_slot_id'];
            } else {
                echo "<script>alert('No pickup request details found.'); window.location.href='admin_pickup_request.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('No pickup request selected.'); window.location.href='admin_pickup_request.php';</script>";
            exit();
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
                user.username,
                user.email,
                user.phone_number,
                time_slot.date AS pickup_date, 
                time_slot.time AS pickup_time,
                item_pickup.quantity,
                item.item_name
            FROM pickup_request 
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
        <button class="back" type=submit name="back" onclick="window.location.href='admin_pickup_request.php'"><i class="fa-solid fa-arrow-left"></i> Back to List</button>
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
            
            </div>
        </div>
        
        <div class="user-detail">
            <div class="action-buttons">
                <button type="submit" class="assign-button" onclick="openAssignDriverModal()">
                    <i class="fas fa-user-check"></i> Assign Driver
                </button>
                <button type="button" class="reject-button" onclick="openRejectModal()">
                    <i class="fas fa-times"></i> Reject Request
                </button>
            </div>
        </div>
    </div>

    <div id="assigndriver" class="modal">
    <form class="modal-content animate" action="#" method="post">
        <div class="imgcontainer">
            <span class="close" onclick="closeModal('assigndriver')">&times;</span>
        </div>
        <div class="container">
            <h2><i class="fas fa-user-check"></i>Assign Driver</h2>
            <p>Select a driver for this pickup request</p>
            <br>
            <div id="driverList">
                <?php
                $show_confirm_button=true;
                if (!empty($time_slot_id)) {
                    // Get available slots for this time slot
                    $slot_query = mysqli_query($con, "SELECT no_driver_per_slot FROM time_slot WHERE time_slot_id = '$time_slot_id'");
                    $slot_data = mysqli_fetch_assoc($slot_query);
                    $available_slots = $slot_data['no_driver_per_slot'] ?? 0;

                    if($available_slots > 0) {
                        // Get available drivers (not currently assigned to other pickups at this time)
                        $drivers = mysqli_query($con, "
                            SELECT driver.* 
                            FROM driver 
                            LEFT JOIN pickup_request ON driver.driver_id = pickup_request.driver_id 
                                AND pickup_request.time_slot_id = '$time_slot_id'
                                AND pickup_request.status = 'Assigned'
                            WHERE pickup_request.driver_id IS NULL
                        ");
                        
                        if(mysqli_num_rows($drivers) > 0) {
                            echo '<select name="driver_id" class="driver-select" required>';
                            echo '<option value="">-- Select a driver --</option>';
                            while($driver = mysqli_fetch_assoc($drivers)) {
                                echo '<option value="'.$driver['driver_id'].'">';
                                echo htmlspecialchars($driver['driver_name']).' ';
                                echo '</option>';
                            }
                            echo '</select>';
                        } else {
                            echo '<p>No available drivers found for this time slot.</p>';
                            $show_confirm_button = false;
                        }
                    } else {
                        echo '<p>No available slots for this time period.</p>';
                        $show_confirm_button = false;
                    }
                } else {
                    echo '<p>Error: No time slot associated with this request</p>';
                    $show_confirm_button = false;
                }
                ?>
            </div>
               
            <input type="hidden" name="pickup_request_id" value="<?php echo $pickup_id; ?>">
            <input type="hidden" name="time_slot_id" value="<?php echo isset($time_slot_id) ? $time_slot_id : ''; ?>">            
            <input type="hidden" name="action" value="assign_driver">
            
            <button type="submit" class="confirm-assign">Confirm Assign</button>
        </div>
    </form>
    </div>

    <div id="rejectRequest" class="modal">
    <form class="modal-content animate" action="#" method="post">
        <div class="imgcontainer">
            <span class="close" onclick="closeModal('rejectRequest')">&times;</span>
        </div>
        <div class="container">
            <h2><i class="fas fa-times-circle"></i> Reject Request</h2>
            <p>Please select a reason for rejecting this pickup request</p>
            <br>
            <select name="reject_reason" class="reject-reason-select" required>
                <option value="">-- Select rejection reason --</option>
                <option value="Incomplete information">Incomplete information</option>
                <option value="Invalid pickup location">Invalid pickup location</option>
                <option value="No available drivers">No available drivers</option>
                <option value="Outside service area">Outside service area</option>
            </select>

            <input type="hidden" name="pickup_request_id" value="<?php echo $pickup_id; ?>">
            <input type="hidden" name="action" value="reject_request">
            
            <button type="submit" class="reject-confirm">Confirm Rejection</button>
        </div>
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

        function openAssignDriverModal() {
            document.getElementById('assigndriver').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Update window.onclick to handle modal closing
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        function openRejectModal() {
            document.getElementById('rejectRequest').style.display = 'block';
        }


        
    </script>

</body>
</html>