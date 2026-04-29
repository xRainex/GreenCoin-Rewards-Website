
<?php
    $servername = "localhost"; 
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    // First query: Table details
    $dropOffItems = [];
    $query1 = "
        SELECT 
            YEAR(d.dropoff_date) AS year,
            MONTHNAME(d.dropoff_date) AS month, 
            l.location_name, 
            i.item_name, 
            id.quantity
        FROM dropoff d
        JOIN location l ON d.location_id = l.location_id
        JOIN item_dropoff id ON d.dropoff_id = id.dropoff_id
        JOIN item i ON id.item_id = i.item_id
        WHERE status = 'Completed'
        ORDER BY d.dropoff_date;
    ";


    $result1 = $conn->query($query1);
    while ($row = $result1->fetch_assoc()) {
        $dropOffItems[] = $row;
    }

    #Chart Details
    $query3 = "
        SELECT 
            i.item_name, 
            SUM(id.quantity) AS total_quantity,
            YEAR(d.dropoff_date) AS year  
        FROM item_dropoff id
        JOIN dropoff d ON id.dropoff_id = d.dropoff_id
        JOIN item i ON id.item_id = i.item_id
        WHERE status = 'Completed'
        GROUP BY i.item_name, YEAR(d.dropoff_date) 
        ORDER BY total_quantity DESC;
    ";

    $result = $conn->query($query3); 

    $data = [];

    while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    }
    $queryitem = "SELECT DISTINCT item_name FROM item ORDER BY item_name ASC"; 
    $itemresult = $conn->query($queryitem);

    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drop Off Items Report</title>
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
    #dropOffItemsChart{
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
        <h2 class="header">Drop Off Items Report</h2>
        <div class="report-container">
            <div class="yearFilterDropdown">
                <select id="yearFilter">
                    <option value="2025">Year 2025</option>
                    <option value="2024">Year 2024</option>
                    <option value="2023">Year 2023</option>
                </select>
                <i class="fa-solid fa-caret-down dropdown-icon"></i>
            </div>
            <canvas id="dropOffItemsChart" style="display: block; box-sizing: border-box;padding-bottom:10px;"></canvas>
            <div class="yearFilterDropdown">
                <select id="itemFilter">
                <option value="">All Items</option>
                    <?php
                    while ($row = $itemresult->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['item_name']) . '">' . htmlspecialchars($row['item_name']) . '</option>';
                    }
                    ?>
                </select>
                <i class="fa-solid fa-caret-down dropdown-icon"></i>
            </div>
            <table id="dropOffTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Location Name</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="dropOffTableBody">
                    <?php foreach ($dropOffItems as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['month']); ?></td>
                            <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="Admin-Report-DropOff-DropOffItems-PDF.php" method="post">
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
            let dropOffTableData = <?php echo json_encode($dropOffItems); ?>;
            let dropOffChartData = <?php echo json_encode($data); ?>;
            console.log("Initial Chart Data:", dropOffChartData);
            console.log("Initial Table Data:", dropOffTableData);

            let ctx = document.getElementById("dropOffItemsChart").getContext("2d");

            function getCurrentYear() {
                return new Date().getFullYear().toString();
            }

            // **Filtering function for table data**
            function filterTableDataByYear(year) {
                return dropOffTableData.filter(item => item.year == year);
            }

            // **Filtering function for chart data**
            function filterChartDataByYear(year) {
                return dropOffChartData.filter(item => item.year == year);
            }

            function updateTable(year, selectedItem = "") {
                let tableBody = document.getElementById("dropOffTableBody");
                tableBody.innerHTML = ""; 

                let filteredData = filterTableDataByYear(year);
                console.log("Filtered Table Data for Year " + year + ":", filteredData);

                filteredData.forEach(row => {
                    if (selectedItem === "" || row.item_name.toLowerCase() === selectedItem.toLowerCase()) {
                        let tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${row.month}</td>
                            <td>${row.location_name}</td>
                            <td>${row.item_name}</td>
                            <td>${row.quantity}</td>
                        `;
                        tableBody.appendChild(tr);
                    }
                });
            }

            let dropOffChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: [], // Start empty, updated dynamically
                    datasets: [{
                        label: "Total Quantity",
                        data: [],
                        backgroundColor: [],
                        borderColor: [],
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

            function updateChart(year, selectedItem = "") {
                let filteredData = filterChartDataByYear(year);
                console.log("Filtered Chart Data for Year " + year + ":", filteredData);

                let itemNames = filteredData.map(item => item.item_name);
                let quantities = filteredData.map(item => item.total_quantity);

                let backgroundColors = itemNames.map(item => 
                    selectedItem && item.toLowerCase() === selectedItem.toLowerCase() 
                        ? "rgba(255, 99, 132, 0.8)"
                        : "rgba(14, 97, 43, 0.6)"
                );

                let borderColors = itemNames.map(item => 
                    selectedItem && item.toLowerCase() === selectedItem.toLowerCase() 
                        ? "rgba(255, 99, 132, 1)"
                        : "rgba(14, 97, 43, 1)"
                );

                // Update the chart data
                dropOffChart.data.labels = itemNames;
                dropOffChart.data.datasets[0].data = quantities;
                dropOffChart.data.datasets[0].backgroundColor = backgroundColors;
                dropOffChart.data.datasets[0].borderColor = borderColors;
                dropOffChart.update();
            }

            let initialYear = getCurrentYear();
            let yearFilter = document.getElementById("yearFilter");
            let itemFilter = document.getElementById("itemFilter");

            yearFilter.addEventListener("change", function () {
                let selectedYear = this.value;
                itemFilter.value = "";
                updateTable(selectedYear);
                updateChart(selectedYear);
            });

            itemFilter.addEventListener("change", function () {
                let selectedItem = this.value.toLowerCase();
                let selectedYear = yearFilter.value || getCurrentYear();
                updateTable(selectedYear, selectedItem);
                updateChart(selectedYear, selectedItem);
            });

            // Initial setup for table and chart
            updateTable(initialYear);
            updateChart(initialYear);
        });

    </script>
</body>

</html>