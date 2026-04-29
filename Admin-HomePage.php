<?php
    // Database connection (replace with actual connection details)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cp_assignment";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch recent pickup requests
    $recentPickupQuery = "SELECT * FROM pickup_request ORDER BY datetime_submit_form DESC LIMIT 5"; 
    $recentPickupResult = $conn->query($recentPickupQuery);
    $pickupResults = [];
    while ($row = $recentPickupResult->fetch_assoc()) {
        $pickupResults[] = [
            'status' => $row['status'],
            'datetime_submit_form' => $row['datetime_submit_form']
        ];
    }

    // Fetch recent drop-off requests
    $recentDropOffQuery = "SELECT * FROM dropoff ORDER BY dropoff_date DESC LIMIT 5"; 
    $recentDropOffResult = $conn->query($recentDropOffQuery);
    $dropOffResults = [];
    while ($row = $recentDropOffResult->fetch_assoc()) {
        $dropOffResults[] = [
            'status' => $row['status'],
            'dropoff_date' => $row['dropoff_date']
        ];
    }

    // Fetch total pickup orders
    $totalPickupOrdersQuery = "SELECT COUNT(*) as total_pickups FROM pickup_request";
    $totalPickupOrdersResult = $conn->query($totalPickupOrdersQuery);
    $totalPickupOrders = $totalPickupOrdersResult->fetch_assoc()['total_pickups'];

    // Fetch pending pickup orders
    $pendingPickupOrdersQuery = "SELECT COUNT(*) as pending_pickups FROM pickup_request WHERE status = 'Submitted'";
    $pendingPickupOrdersResult = $conn->query($pendingPickupOrdersQuery);
    $pendingPickupOrders = $pendingPickupOrdersResult->fetch_assoc()['pending_pickups'];

    // Fetch total drop-off points
    $totalDropOffQuery = "SELECT COUNT(*) as total_dropoff FROM dropoff";
    $totalDropOffResult = $conn->query($totalDropOffQuery);
    $totalDropOff = $totalDropOffResult->fetch_assoc()['total_dropoff'];

    // Fetch total drop-off requests
    $totalPendingDropOffQuery = "SELECT COUNT(*) as total_pending_dropoff FROM dropoff WHERE status = 'Submitted'";
    $totalPendingDropOffResult = $conn->query($totalPendingDropOffQuery);
    $totalPendingDropOff = $totalPendingDropOffResult->fetch_assoc()['total_pending_dropoff'];

    $conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes floatIn {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
            .recent-activities-card {
                flex-direction: column;
                align-items: center;
            }
        }
        @media (max-width: 768px) {
            .dashboard-cards-container {
                flex-direction: column;
                align-items: center;
            }
            .pickup-stats, .dropoff-stats {
                width: 80%;
                margin-bottom: 20px;
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Roboto", sans-serif;;
        }
        html {
            background-color: rgba(220, 245, 237, 0.48);
            height: 100%;
            overflow: hidden;
        }
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: rgba(220, 245, 237, 0.48);
            height: 100vh;
            overflow: hidden;
        }
        .profile-container {
            width: 100%;
            margin-top: 130px;
            display: flex;
            justify-content: flex-end; /* Make sure it aligns to the right */
        }

        .profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 2px solid rgba(116, 116, 116, 0.76);
            padding: 10px;
            width: 93%;
            position: relative;
            margin-left: 15px;
            box-sizing: border-box;
        }

        .profile-info {
            font-size: 14px;
            flex-grow: 1;
            padding-left: 15px;
        }

        .profile-info p {
            margin: 0;
        }

        .profileicon {
            font-size: 30px;
            color: #333;
        }

        .profile-container .profile img {
            margin-left: auto; /* Push the image to the right side */
            width: 150px; /* Adjust the size as needed */
            background-color: #78A24C;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            margin-left: 13px;
        }

        .sidebar {
            width: 250px;
            background: #f8f9fa;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            justify-content: space-between;
            flex-direction: column;
            display: flex;
            position: relative;
            min-height: 100vh;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin-left: 15px;
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
            color: rgb(134, 134, 134);
            width: 5px;
            padding-right: 18px;
        }

        .menu li.active {
            background-color: #E4EBE6;
            border-radius: 10px;
            color: rgb(11, 91, 19);
        }

        .menu a:hover, .menu a.active {
            background: #E4EBE6;
            color: rgb(11, 91, 19);
        }

        .menu li.active i, .menu li:hover i {
            color: green;
            background-color: #E4EBE6;
        }
        
        .menu li.active a,
        .menu li:hover a {
            color: rgb(11, 91, 19);
            background-color: #E4EBE6;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            width: 100vw;
            max-height: 100vh;
        }

        .header {
            text-align: left;
            width: 100%;
            margin-top: 50px;
            font-size: 34px;
            margin-left: 20px;
            margin-bottom: 20px;
            animation: floatIn 0.8s ease-out;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .pickup-stats,
        .dropoff-stats {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 48%; /* Adjust width for the two containers */
        }

        .card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 100%; /* Make cards take the full height of the container */
        }

        .header {
            text-align: left;  
            width: 100%;
            margin-top: 50px;
            font-size:34px;
            margin-left:20px;
            margin-bottom: 20px; 
            animation: floatIn 0.8s ease-out;
        }

        .card h3 {
            font-size: 16px;
            color: #666;
        }

        .card h1 {
            font-size: 36px;
            margin: 0;
            color: #333;
        }

        .card p {
            font-size: 14px;
            color: #666;
        }

        .card i {
            font-size: 30px;
            color: #007bff;
            margin-left:auto;
            margin-top:-30px;
        }
        
        .container {
            display: flex;
            flex: 1; /* Take up available space */
            max-width: 100vw; /* Ensure it doesn't overflow the viewport width */
            height: 100vh; /* Make sure the container height matches the viewport */
            overflow: hidden; /* Prevent any overflow */
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            bottom: 100%;
            background: white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            width: 100px;
            z-index: 10;
            text-align: left;
            padding: 5px 0;
        }

        .dropdown-btn {
            border: none;
            background-color: transparent;
            cursor: pointer;
            font-size: 16px;
        }

        .recent-activities-card,.dashboard-cards-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            width: 100%;
            max-width: 1400px;
            margin: 20px auto;
            align-items: center; /* Center the items vertically */
            margin-left:20px;
            margin-right:-70px;
        }

        .recent-activities {
            width: 48%; /* Left side for Pickup Activities */
            padding-right: 20px;
        }

        .recent-activities-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 500px;
        }

        .activities-list div {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #555;
        }

        .activities-list div i {
            margin-right: 8px;
            color: #007bff;
            font-size: 18px;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .activity-card:hover {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-card .details {
            display: block;
        }

        .activity-card .details p {
            margin: 5px 0;
        }

        .activity-card .status {
            font-size: 13px;
            color: #888;
            font-weight: bold;
        }

        .activity-card .time {
            color: #aaa;
            font-size: 12px;
            margin-top: 5px;
        }

        .activity-card.complete {
            border-left: 5px solid #4CAF50;
            background-color: #E8F5E9;
        }

        .activity-card.pending {
            border-left: 5px solid #FF9800;
        }

        .activity-card.submitted {
            border-left: 5px solid #FFEB3B;
            background-color: #FFF9C4;
        }

        .activity-card .icon-container {
            margin-right: 10px;
        }

        .activity-card .icon-container i {
            color: #4CAF50;
        }

        .activity-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        hr.divider {
            border: 0;
            border-top: 2px solid #ccc;
            margin: 20px 0;
            width: 100%;
        }

        .pickup-stats h1,
        .dropoff-stats h1 {
            font-size: 36px; /* Larger size for the number */
            margin-bottom: 5px; /* Smaller gap between number and text */
            color: #333;
        }

        .pickup-stats h3,
        .dropoff-stats h3 {
            font-size: 16px; /* Smaller font for the text */
            font-weight: normal; /* No bold text */
            color: #666; /* Optional: color for the text */
            margin: 0; /* Remove default margin to keep it tight under the number */
        }

        .card i {
            font-size: 30px;
            color: rgb(134, 134, 134);
        }

        .pickup-stats .card,
        .dropoff-stats .card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;  /* Align items to the left */
            justify-content: center;
            gap: 5px; /* Space between the number and text */
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-height: 50px; /* Ensure the cards have the same minimum height */
            flex: 1; /* Allow cards to take up equal space */
        }

        .details p {
            margin: 5px 0;
        }

        .time {
            margin-left: auto;  /* Push the time to the right side */
            font-size: 12px;  /* Make the time smaller */
            color: #aaa;  /* Lighter color for time */
        }

        .recent-dropoff {
            width: 48%; /* Right side for Dropoff Activities */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            align-items: flex-start; /* Align items to the left side */
        }
        .recent-dropoff ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }

        .recent-dropoff h3 {
            text-align: left;
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .recent-dropoff li {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
            width: 100%;
        }

        .recent-dropoff .icon-container {
            margin-left: auto; /* Push the icon to the right */
        }

        .recent-dropoff .icon-container i {
            color: #007bff;
            font-size: 18px;
            margin-left: 10px; /* Space between text and icon */
        }

        .pickup-stats,
        .dropoff-stats {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 48%; /* Adjust width for the two containers */
            flex: 1; /* Ensure both containers take up equal width and space */
            height: 100%; /* Ensure both containers have the same height */
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            width: 100%;
        }

        .logout-btn {
            padding: 12px 30px;
            background-color: #f44336;  /* Red background */
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px; /* Add some top margin for spacing */
            width: 100%; /* Make the logout button take up full width */
        }

        /* Hover effect for the logout button */
        .logout-btn:hover {
            background-color: #e53935;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Active effect when the button is clicked */
        .logout-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Focus effect for accessibility */
        .logout-btn:focus {
            outline: none;
            border: 2px solid #333;
        }

        /* Popup overlay */
        .modal-overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000; /* Ensure it's on top of other content */
        }

        /* Modal container */
        .modal-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        .modal-container button {
            flex: 1; /* Make buttons take equal space */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-container .button-container {
            display: flex; /* Enable flexbox to make buttons in the same row */
            justify-content: space-between; /* Space out the buttons */
            gap: 10px; /* Optional: Add space between the buttons */
        }

        .modal-container .cancel-btn {
            background-color: #f44336; /* Red background for cancel */
        }

        .modal-container button {
            background-color: #45a049; /* Green on hover for "Yes" */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div>
                <a href="Admin-HomePage.php">
                    <img src="User-Logo.png" style="width: 200px; margin-bottom: 25px; background-color: #78A24C; padding: 10px; border-radius: 10px; cursor: pointer; margin-left: 13px;">
                </a>
            </div>
            <ul class="menu">
                <li id="dashboard-item"><a href="#" onclick="activateLink(this)"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                <li><a href="Admin-Notification.php" onclick="activateLink(this)"><i class="fa-solid fa-envelope"></i>Notifications</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-truck-moving"></i>Pickup Requests</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-regular fa-calendar-days"></i>Pickup Availability</a></li>
                <li><a href="admin_driver.php" onclick="activateLink(this)"><i class="fa-solid fa-address-book"></i>Drivers</a></li> 
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-dolly"></i>Drop-Off Requests</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Points</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-gift"></i>Rewards</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-comments"></i>Reviews</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-scroll"></i>Reports</a></li>
                <li><a href="fill_here.php" onclick="activateLink(this)"><i class="fa-solid fa-circle-question"></i>FAQ</a></li>
            </ul>
            <div class="profile-container" style="position: relative; display: inline-block;">
                <button class="logout-btn" onclick="logout()">Logout</button>
                <div id="logoutModal" class="modal-overlay">
                    <div class="modal-container">
                        <h2>Are you sure you want to proceed?<br></br></h2>
                        <div class="button-container">
                            <a href="Admin-Login.php">
                                <button class="confirm-btn">Yes</button>
                            </a>
                            <a href="Admin-Login.php">
                                <button class="cancel-btn">Cancel</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="header">Great to have you back!</div>
            <div class="dashboard-cards-container">
                <!-- Pickup Stats (Left) -->
                <div class="pickup-stats">
                    <div class="card">
                        <h1><?php echo $totalPickupOrders; ?></h1>
                        <h3>Total Pickup Orders</h3>
                        <i class="fa-solid fa-map-marker"></i>
                    </div>
                    <div class="card">
                        <h1><?php echo $pendingPickupOrders; ?></h1>
                        <h3>Pending Pickup Orders</h3>
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>

                <!-- Drop-Off Stats (Right) -->
                <div class="dropoff-stats">
                    <div class="card">
                    <h1><?php echo $totalDropOff; ?></h1> 
                        <h3>Total Dropoff Orders</h3>
                        <i class="fa-solid fa-map-marker"></i>
                    </div>
                    <div class="card">
                    <h1><?php echo $totalPendingDropOff; ?></h1> 
                        <h3>Pending Dropoff Order</h3>
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
            </div>

            <div class="recent-activities-card">
                <div class="recent-activities">
                    <h3>Recent Pickup Activities</h3>
                    <ul id="pickup-list">
                        <?php foreach ($pickupResults as $pickup) : ?>
                            <li class="activity-card <?php echo strtolower($pickup['status']); ?>">
                                <div class="details">
                                    <p><strong>Status:</strong> <?php echo $pickup['status']; ?></p>
                                    <p class="time"><strong>Submitted:</strong> <?php echo date('d-m-Y H:i', strtotime($pickup['datetime_submit_form'])); ?></p>
                                </div>
                                <div class="icon-container">
                                    <i class="fa-solid fa-truck-moving"></i> <!-- Truck icon -->
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="recent-activities">
                    <h3>Recent Dropoff Activities</h3>
                    <ul id="dropoff-list">
                        <?php foreach ($dropOffResults as $dropoff) : ?>
                            <li class="activity-card <?php echo strtolower($dropoff['status']); ?>">
                                <div class="details">
                                    <p><strong>Status:</strong> <?php echo $dropoff['status']; ?></p>
                                    <p class="time"><strong>Dropoff Date:</strong> <?php echo date('d-m-Y H:i', strtotime($dropoff['dropoff_date'])); ?></p>
                                </div>
                                <div class="icon-container">
                                    <i class="fa-solid fa-truck-moving"></i> <!-- Truck icon -->
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="divider"></div>
    </div>
</body>
</html>
<script>
        // Show the modal

        function logout() {
            // Show the confirmation modal instead of the browser default confirmation
            showModal();
        }

        function showModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        // Close the modal without any action
        function closeModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Handle logout action
        function logoutConfirmed() {
            // Redirect to the login page
            window.location.href = 'Admin-Login.php'; // Replace with your login URL
        }

        // Function to activate the clicked link
        function activateLink(link) {
            // Get all list items in the menu
            let items = document.querySelectorAll('.menu li');
            
            // Remove 'active' class from all menu items
            items.forEach(item => {
                item.classList.remove('active');
            });

            // Add 'active' class to the parent <li> of the clicked link
            link.closest('li').classList.add('active');
        }

        // Function to automatically highlight the active menu item based on the current URL
        function setActivePage() {
            // Get the current URL path (without query string)
            const currentPath = window.location.pathname;

            // Find all the menu items
            const menuLinks = document.querySelectorAll('.menu a');
            
            menuLinks.forEach(link => {
                // Compare href with current path for exact match
                if (link.pathname === currentPath) {
                    link.closest('li').classList.add('active');
                } else {
                    link.closest('li').classList.remove('active');
                }
            });
        }
        // Call the function when the page is loaded
        window.addEventListener('load', setActivePage);
</script>