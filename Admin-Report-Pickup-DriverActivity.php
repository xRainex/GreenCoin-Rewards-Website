<!-- DATE FOLLOW TIME SLOT DATE OR PICKUP REQUEST DATE -->


<?php
    $servername = "localhost"; 
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    $query3 = "
        SELECT 
            DATE(ts.date) AS pickup_date,
            d.driver_name, 
            COUNT(pr.pickup_request_id) AS total_pickups
        FROM pickup_request pr
        JOIN driver d ON pr.driver_id = d.driver_id
        JOIN time_slot ts ON pr.time_slot_id = ts.time_slot_id
        GROUP BY pickup_date, d.driver_name
        ORDER BY pickup_date DESC, total_pickups DESC
    ";

    $result3 = $conn->query($query3);

    $driverPickupCounts = [];

    while ($row = $result3->fetch_assoc()) {
        $driverPickupCounts[] = $row;
    }
    $selectedYear = isset($_POST['year']) ? $_POST['year'] : date("Y");
    $selectedYear = (int) $selectedYear;
    $queryChart = "
        SELECT d.driver_name, COUNT(pr.pickup_request_id) AS total_pickups 
        FROM pickup_request pr
        JOIN driver d ON pr.driver_id = d.driver_id
        JOIN time_slot ts ON pr.time_slot_id = ts.time_slot_id
        WHERE YEAR(ts.date) = $selectedYear
        GROUP BY d.driver_name
        ORDER BY total_pickups DESC
    ";

$resultChart = $conn->query($queryChart);

$drivers = [];
$pickups = [];

while ($row = $resultChart->fetch_assoc()) {
    $drivers[] = $row['driver_name'];
    $pickups[] = $row['total_pickups'];
}

    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Activity Report</title>
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

    #monthFilter {
        margin-bottom: 10px;
        padding: 5px;
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

    @media screen and (max-width: 768px) {
        th, td {
            font-size: 14px;
            padding: 10px;
        }
    }

    canvas {
        max-width: 100%;
        height: 300px;
        display: block;
        margin: 0 auto; 
    }
    #pickupChart {
        width: 100%; 
        max-width: 1000px; 
        height: auto; 
        max-height: 400px; 
    }

    .yearFilterDropdown {
        position: relative;
        display: inline-block;
        float:right;
    }

    .yearFilterDropdown select {
        padding: 12px 16px;
        width: 180px; 
        border: 2px solid #2d6a4f;
        border-radius: 8px;
        background: #ffffff;
        color: #2d6a4f;
        font-size: 16px;
        font-weight: bold;
        appearance: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropdown-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #2d6a4f;
        pointer-events: none; 
    }

    .yearFilterDropdown select:hover {
        border-color: #1b4332;
    }

    .yearFilterDropdown select:focus {
        outline: none;
        border-color: #1b4332;
        box-shadow: 0px 0px 6px rgba(27, 67, 50, 0.4);
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
        <h2 class="header">Driver Activity Report</h2>
        <div class="report-container">
            <div class="yearFilterDropdown">
            <form method="post" id="yearFilterForm">
                <select name="year" id="yearFilter" onchange="document.getElementById('yearFilterForm').submit();">
                    <option value="2025" <?php if ($selectedYear == 2025) echo "selected"; ?>>Year 2025</option>
                    <option value="2024" <?php if ($selectedYear == 2024) echo "selected"; ?>>Year 2024</option>
                    <option value="2023" <?php if ($selectedYear == 2023) echo "selected"; ?>>Year 2023</option>
                </select>
                <i class="fa-solid fa-caret-down dropdown-icon"></i>
            </form>
            </div>
            <canvas id="driverPickupChart" style="display: block; box-sizing: border-box;padding-bottom:10px;"></canvas>
            <div class="yearFilterDropdown">
                <select id="monthFilter">
                    <option value="">All Months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <i class="fa-solid fa-caret-down dropdown-icon"></i>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Pickup Date</th>
                        <th>Driver Name</th>
                        <th>Total Pickups</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($driverPickupCounts as $row) : 
                        $month = date('m', strtotime($row['pickup_date'])); // Extract month
                        $year = date('Y', strtotime($row['pickup_date']));  // Extract year
                    ?>
                        <tr class="month-<?php echo $month; ?> year-<?php echo $year; ?>">
                            <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_pickups']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="Admin-Report-PickupRequest-PDF.php" method="post">
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
        document.addEventListener("DOMContentLoaded", function () {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        var ctx = document.getElementById('driverPickupChart').getContext('2d');

        var pickupChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($drivers); ?>,
                datasets: [{
                    label: 'Pickup Requests Accepted',
                    data: <?php echo json_encode($pickups); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
</body>

</html>