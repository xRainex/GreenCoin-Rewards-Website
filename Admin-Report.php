<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports Management</title>
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
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html{
        background-color:rgba(220, 245, 237, 0.48);
        height:100%;
        overflow:hidden;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color:rgba(220, 245, 237, 0.48);
        display:flex;
        align-items: flex-start; 
        min-height: 100vh; 
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
    .header-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left:30px; 
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
        align-items: stretch; 
        min-height: 100vh; 
    }

    .sidebar {
        width: 250px;
        background: #f8f9fa;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        justify-content: space-between;
        flex-direction: column; 
        display:flex;
        position: relative; 
        min-height: 100vh;
    }

    .profile-container{
        width:100%;
        margin-top:130px;
    }

    .profile {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f8f9fa ;
        border-radius: 10px;
        border:2px solid rgba(116, 116, 116, 0.76);
        padding: 10px; 
        width: 93%;
        position: relative;
        margin-left: 15px;
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
        bottom: 100%; 
        background: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        width: 100px; 
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

    .report-sections {
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 1200px;
        margin: auto;
    }

    .report-category {
        display: grid;
        grid-template-columns: 1fr 2fr; 
        align-items: center;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .report-category:hover {
        transform: translateY(-2px);
    }

    .report-header {
        padding-right: 20px;
    }

    .report-header h2 {
        font-size: 20px;
        color: #333;
        margin-bottom: 10px;
    }

    .report-header i{
        margin-right:8px;
    }
    .report-header p {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .report-links a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px; 
        border-bottom: 1px solid #E0E0E0; 
        color: #333;
        text-decoration: none;
        font-size: 16px;
        background: white;
    }

    .report-links a:last-child {
        border-bottom: none; 
    }

    .report-links a i {
        margin-left: 5px;
        color: #666;
    }

    .report-links a:hover {
        color: #437439;
    }
    .report-item {
        transition: all 0.2s ease-in-out;
    }

    .hidden-reports {
        display: none; 
    }

    .show-more-btn {
        color: #007BFF;
        font-weight: bold;
        cursor: pointer;
        padding: 12px 16px;
        display: block;
        text-align: left;
    }


    .show-more-btn:hover {
        color: #007bff;
    }

    .dropdown-menu {
        display: none;
        flex-direction: column;
        margin-top: 5px;
    }

    .dropdown-menu a {
        font-size: 14px;
        padding: 5px 0;
        color: #444;
    }
    .search-bar {
        margin-bottom: 15px;
    }

    .search-bar input {
        width: 97.8%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-left: 15px;
    }

</style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div>
                <img src="User-Logo.png" style="width: 200px; margin-bottom: 25px; background-color: #78A24C; padding: 10px; border-radius: 10px; cursor: pointer; margin-left: 13px;" onclick="AdminHomePage()">
            </div>
            <ul class="menu">
                <li><a href="#"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                <li><a href="#"><i class="fa-solid fa-envelope"></i>Notifications</a></li>
                <li><a href="#"><i class="fa-solid fa-truck-moving"></i>Pickup Requests</a></li>
                <li><a href="#"><i class="fa-regular fa-calendar-days"></i>Pickup Availability</a></li>
                <li><a href="#"><i class="fa-solid fa-address-book"></i>Drivers</a></li> 
                <li><a href="#"><i class="fa-solid fa-dolly"></i>Drop-Off Requests</a></li>               
                <li><a href="#"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Points</a></li>
                <li><a href="#Admin-RewardsItemsPage.php"><i class="fa-solid fa-gift"></i>Rewards</a></li>
                <li><a href="#"><i class="fa-solid fa-comments"></i>Reviews</a></li>
                <li class="active"><a href="#"><i class="fa-solid fa-scroll"></i>Reports</a></li>
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
    </div>
    <div class ="main-content">
        <h2 class="header">All Reports</h2>
        <div class="reportcontainer">
            <div class="search-bar">
                <input type="text" id="reportSearch" placeholder="Search reports..." onkeyup="filterReports()">
            </div>
            <div class="report-sections">
                <div class="report-category">
                    <div class="report-header">
                        <h2><i class="fa-solid fa-truck-ramp-box"></i> Pickup Reports</h2>
                        <p>Analyze pickup trends and performance.</p>
                    </div>
                    <div class="report-links">
                        <a href="Admin-Report-Pickup-PickupRequest.php" class="report-item">Pickup Requests Trend <i class="fa-regular fa-bookmark"></i></a>
                        <a href="Admin-Report-Pickup-Items.php" class="report-item">Pickup Items Trend <i class="fa-regular fa-bookmark"></i></a>
                        <a href="Admin-Report-Pickup-DriverActivity.php" class="report-item">Driver Activity Trend <i class="fa-regular fa-bookmark"></i></a>
                    </div>
                </div>
                <div class="report-category">
                    <div class="report-header">
                        <h2><i class="fa-solid fa-map-pin"></i> Drop-Off Reports</h2>
                        <p>Monitor drop-off activity and trends.</p>
                    </div>
                    <div class="report-links">
                        <a href="dropoff_request_trend.php" class="report-item">Drop-Off Requests Trend <i class="fa-regular fa-bookmark"></i></a>
                        <a href="dropoff_vs_requests.php" class="report-item">Drop-Off Locations<i class="fa-regular fa-bookmark"></i></a>                        
                        <a href="dropoff_items_report.php" class="report-item">Drop-Off Items Trend <i class="fa-regular fa-bookmark"></i></a>
                    </div>
                </div>
                
                <div class="report-category">
                    <div class="report-header">
                        <h2><i class="fa-solid fa-gifts"></i> Reward Reports</h2>
                        <p>Track reward claims and user redemptions.</p>
                    </div>
                    <div class="report-links">
                        <a href="redemption_trend_report.php" class="report-item">Redemptions by Item <i class="fa-regular fa-bookmark"></i></a>
                        <a href="user_redemption_history.php" class="report-item">User Redemption History <i class="fa-regular fa-bookmark"></i></a>
                        <a href="user_points_history.php" class="report-item">User Points<i class="fa-regular fa-bookmark"></i></a>
                    </div>
                </div>
                <div class="report-category">
                    <div class="report-header">
                        <h2><i class="fa-solid fa-users"></i> User Reports</h2>
                        <p>Analyze user activity and engagement.</p>
                    </div>
                    <div class="report-links">
                        <a href="client_segmentation.php" class="report-item">First Time Users<i class="fa-regular fa-bookmark"></i></a>
                        <a href="review_trends.php" class="report-item">Review Trends <i class="fa-regular fa-bookmark"></i></a>
                        <a href="new_vs_returning_clients.php" class="report-item">Returning Clients</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleDropdown(id) {
            var dropdown = document.getElementById(id);
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }
        function toggleDropdown(id, btn) {
            var dropdown = document.getElementById(id);
            
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block"; 
                btn.style.display = "none"; 
            }
        }
        function filterReports() {
            let input = document.getElementById("reportSearch").value.toLowerCase();
            let reportCategories = document.querySelectorAll(".report-category");

            if (input === "") {
                reportCategories.forEach(category => {
                    category.style.display = "grid"; 
                    category.querySelectorAll(".report-links a").forEach(report => {
                        report.style.display = "flex"; 
                    });
                });
                return;
            }

            // Filtering logic
            reportCategories.forEach(category => {
                let reports = category.querySelectorAll(".report-links a");
                let hasMatch = false;

                reports.forEach(report => {
                    if (report.textContent.toLowerCase().includes(input)) {
                        report.style.display = "flex"; 
                        hasMatch = true;
                    } else {
                        report.style.display = "none"; 
                    }
                });

                // If there's at least one matching report, keep the category visible
                category.style.display = hasMatch ? "block" : "none"; 
            });
        }

    </script>
</body>

</html>