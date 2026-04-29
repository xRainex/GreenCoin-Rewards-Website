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

                
        .container {
            display: flex;
            flex: 1; /* Take up available space */
            max-width: 100vw; /* Ensure it doesn't overflow the viewport width */
            height: 100vh; /* Make sure the container height matches the viewport */
            overflow: hidden; /* Prevent any overflow */
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
        .notifications {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        #notification-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #notification-list li {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        #notification-list li .announcement {
            font-size: 18px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        #notification-list li .details {
            font-size: 14px;
            color: #777;
        }

        #notification-list li small {
            color: #aaa;
            font-size: 12px;
        }

        .notifications li:hover {
            background-color: #e9f7ff;
        }

        /* Styles for icons (optional) */
        .notification-icon {
            font-size: 18px;
            color: #3498db;
            margin-right: 10px;
        }
        .sorting-select {
            padding: 12px 30px;
            background-color:rgb(135, 196, 143);
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sorting-select:hover {
            background-color: #78A24C;
        }

        .sorting-select:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sorting-select:focus {
            outline: none;
            border: 2px solid #333;
        }

        /* Style for the dropdown inside the sorting button */
        .sorting-options {
            margin-bottom: 20px;
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
                <li id="dashboard-item"><a href="Admin-Homepage.php" onclick="activateLink(this)"><i class="fa-solid fa-house"></i>Dashboard</a></li>
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
            <div class="header">Notification</div>
            <div class="notifications">
                <div class="sorting-options">
                    <label for="sort-by">Sort By: </label>
                    <select id="sort-by" class="sorting-select" onchange="fetchNotifications()">
                        <option value="user_id">User ID</option>
                        <option value="anoti_id">Announcement ID</option>
                        <option value="datetime">Date</option>
                    </select>
                </div>
                <ul id="notification-list">
                    <!-- Notifications will be injected here -->
                </ul>
            </div>
        </div>
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

    // Fetch notifications when the page loads
    function fetchNotifications() {
        const sortBy = document.getElementById('sort-by').value; // Get selected sorting option
        const sortOrder = sortBy === 'datetime' ? 'DESC' : 'ASC';  // Corrected variable declaration

        const xhr = new XMLHttpRequest();
        // Correct the URL to include both sort_by and sort_order parameters
        xhr.open('GET', 'Admin-Fetch-notification.php?sort_by=' + sortBy + '&sort_order=' + sortOrder, true); // Pass both sort parameters
        xhr.onload = function() {
            if (xhr.status === 200) {
                // On successful response, inject the notifications into the list
                document.getElementById('notification-list').innerHTML = xhr.responseText;
            } else {
                console.error('Failed to fetch notifications.');
            }
        };
        xhr.send();
    }
    // Call the functions when the page is loaded
    window.onload = function() {
        setActivePage();        // Highlight the correct active link
        fetchNotifications();   // Fetch notifications

    }
</script>

