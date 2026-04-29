<!-- NEED CHART?? -->
<?php
    $servername = "localhost"; 
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    // First query: Returning Users details
    $ReturningClientsTrend = [];
    $query1 = "
        SELECT 
            u.username,
            u.email,
            COUNT(*) AS total_services,
            MAX(latest_service_date) AS latest_service_date
        FROM (
            SELECT 
                p.user_id,
                p.datetime_submit_form AS latest_service_date
            FROM pickup_request p

            UNION ALL

            SELECT 
                d.user_id,
                d.dropoff_date AS latest_service_date
            FROM dropoff d
        ) AS all_services
        JOIN user u ON u.user_id = all_services.user_id
        GROUP BY u.user_id, u.username, u.email
        HAVING COUNT(*) > 1
        ORDER BY latest_service_date DESC;
    ";

    $result1 = $conn->query($query1);
    while ($row = $result1->fetch_assoc()) {
        $ReturningClientsTrend[] = $row;
    }

    
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Returning Clients Report</title>
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
        overflow: hidden;
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

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #e6f4ea;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    thead {
        background-color: #1f7922;
        color: white;
        text-transform: uppercase;
    }

    th, td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #c4e1c5;
    }

    th:nth-child(1), td:nth-child(1) { width: 15%; }  /* Date */
    th:nth-child(2), td:nth-child(2) { width: 10%; }  /* Star */
    th:nth-child(3), td:nth-child(3) { width: 20%; }  /* User Name */
    th:nth-child(4), td:nth-child(4) { width: 35%; }  /* Review (Wider) */
    th:nth-child(5), td:nth-child(5) { width: 20%; } 

    tbody tr:nth-child(even) {
        background-color: #d6f5d6; 
    }

    tbody tr:hover {
        background-color: #b3e6b3;
        transition: background-color 0.3s;
    }

    th:first-child, td:first-child {
        border-left: none;
    }

    th:last-child, td:last-child {
        border-right: none;
    }

    .generate-btn {
        background-color: #78A24C;
        color: white;
        margin: 10px 200px 0px 500px;
        font-size: 18px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .generate-btn:hover {
        background-color: #61863D;
        transform: scale(1.05);
    }

    .generate-btn:active {
        background-color: #4F6D32; 
        transform: scale(0.98);
    }

    #reviewsChart{
        width: 100%; 
        max-width: 1000px; 
        height: auto; 
        max-height: 400px; 
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
        <h2 class="header">Returning Clients Report</h2>
        <div class="report-container">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Total Services</th>
                        <th>Latest Service Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ReturningClientsTrend as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['username']); ?></td>
                            <td><?php echo htmlspecialchars($client['email']); ?></td>
                            <td><?php echo $client['total_services']; ?></td>
                            <td><?php echo htmlspecialchars($client['latest_service_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="Admin-Report-Users-ReturningClients-PDF.php" method="post">
                <button type="submit" class="generate-btn">Generate PDF Report</button>
            </form>
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

    </script>
</body>

</html>
