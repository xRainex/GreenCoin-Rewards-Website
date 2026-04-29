
<?php
    $servername = "localhost"; 
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    // First query: Pickup request details
    $dropOffRequests = [];
    $query1 = "
    SELECT 
        do.dropoff_date, 
        u.username,
        i.item_name,
        d.quantity,
        l.location_name
    FROM dropoff do
    LEFT JOIN user u ON do.user_id = u.user_id
    LEFT JOIN item_dropoff d ON do.dropoff_id = d.dropoff_id
    LEFT JOIN item i ON i.item_id = d.item_id
    LEFT JOIN location l ON do.location_id = l.location_id
    ORDER BY do.dropoff_date ASC
";

    $result1 = $conn->query($query1);
    while ($row = $result1->fetch_assoc()) {
        $dropOffRequests[] = $row;
    }

    $itemsDropOff = [];
    $query2 = "
        SELECT 
            DATE(do.dropoff_date) AS dropoff_date, 
            SUM(id.quantity) AS items_dropoff
        FROM dropoff do
        LEFT JOIN item_dropoff id ON do.dropoff_id = id.dropoff_id
        GROUP BY DATE(do.dropoff_date)
        ORDER BY dropoff_date ASC
    ";
    $result2 = $conn->query($query2);
    while ($row = $result2->fetch_assoc()) {
        $itemsDropOff[] = $row;
    }
    

    $query3 = "
        SELECT 
            YEAR(dropoff_date) AS year, 
            MONTH(dropoff_date) AS month, 
            COUNT(*) AS total_dropoff 
        FROM dropoff
        GROUP BY year, month
        ORDER BY year DESC, month ASC
    ";

    $result = $conn->query($query3); 

    $data = [];

    while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    }
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drop Off Request Report</title>
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
    #dropOffChart{
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
        <h2 class="header">Drop Off Requests Report</h2>
        <div class="report-container">
            <div class="yearFilterDropdown">
                <select id="yearFilter">
                    <option value="2025">Year 2025</option>
                    <option value="2024">Year 2024</option>
                    <option value="2023">Year 2023</option>
                </select>
                <i class="fa-solid fa-caret-down dropdown-icon"></i>
            </div>
            <canvas id="dropOffChart" style="display: block; box-sizing: border-box;padding-bottom:10px;"></canvas>
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
                        <th>Drop Off Date</th>
                        <th>User Name</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Location Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dropOffRequests as $row) :
                        $month = date('m', strtotime($row['dropoff_date'])); // Extract month
                        $year = date('Y', strtotime($row['dropoff_date'])); // Extract year
                    ?>
                        <tr class="month-<?php echo $month; ?> year-<?php echo $year; ?>">
                            <td><?php echo htmlspecialchars($row['dropoff_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="Admin-Report-DropOff-DropOffRequest-PDF.php" method="post">
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
            let dropOffData = <?php echo json_encode($data); ?>;

            function getCurrentYear() {
                return new Date().getFullYear().toString(); 
            }

            function filterDataByYear(year) {
                if (year === "") {
                    year = getCurrentYear();
                }
                return dropOffData.filter(item => item.year === year);
            }

            let ctx = document.getElementById("dropOffChart").getContext("2d");
            const monthNames = {
                "01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr",
                "05": "May", "06": "Jun", "07": "Jul", "08": "Aug",
                "09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
            };

            function updateChart(year) {
                let filteredData = filterDataByYear(year);
                console.log("Filtered Data:", filteredData);

                if (filteredData.length === 0) {
                    dropOffChart.data.labels = [];
                    dropOffChart.data.datasets[0].data = [];
                } else {
                    dropOffChart.data.labels = filteredData.map(item => {
                        let monthNum = item.month.toString().padStart(2, "0"); 
                        return monthNames[monthNum] || monthNum; 
                    });

                    dropOffChart.data.datasets[0].data = filteredData.map(item => item.total_dropoff);
                }

                dropOffChart.update();
            }

            let initialYear = getCurrentYear();
            let initialData = filterDataByYear(initialYear);

            console.log("Initial Data:", initialData);

            let dropOffChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: initialData.map(item => {
                        let monthNum = item.month.toString().padStart(2, "0"); 
                        return monthNames[monthNum] || monthNum;
                    }),
                    datasets: [{
                        label: "Total Drop Off per Month",
                        data: initialData.map(item => item.total_dropoff),
                        backgroundColor: Array(initialData.length).fill("rgba(14, 97, 43, 0.6)"),
                        borderColor: "rgba(14, 97, 43, 0.6)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, 
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : null; 
                                }
                            }
                        }
                    }
                }

            });
            document.getElementById("yearFilter").addEventListener("change", function () {
                let selectedYear = this.value;
                let monthFilter = document.getElementById("monthFilter");
                monthFilter.value = ""; 
                let defaultColor = "rgba(14, 97, 43, 0.6)";
                dropOffChart.data.datasets[0].backgroundColor = Array(dropOffChart.data.labels.length).fill(defaultColor);

                updateChart(selectedYear); 
                applyFilters(); 
            });

            document.getElementById("monthFilter").addEventListener("change", function () {
                let selectedMonth = this.value.padStart(2, "0");  
                let defaultColor = "rgba(14, 97, 43, 0.6)";
                let highlightColor = "rgba(153, 201, 143, 0.8)";

                let labelToNumber = Object.fromEntries(Object.entries(monthNames).map(([num, name]) => [name, num]));
                let newColors = dropOffChart.data.labels.map(() => defaultColor);

                if (selectedMonth !== "") { 
                    let selectedMonthName = monthNames[selectedMonth]; 
                    
                    dropOffChart.data.labels.forEach((label, index) => {
                        if (label === selectedMonthName) {
                            newColors[index] = highlightColor; 
                        }
                    });
                }

                dropOffChart.data.datasets[0].backgroundColor = newColors;
                dropOffChart.update();
                applyFilters();
            });

            function applyFilters() {
                let selectedMonth = document.getElementById("monthFilter").value;
                let selectedYear = document.getElementById("yearFilter").value || getCurrentYear();
                let rows = document.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    let monthClass = [...row.classList].find(cls => cls.startsWith("month-"));
                    let yearClass = [...row.classList].find(cls => cls.startsWith("year-"));

                    let rowMonth = monthClass ? monthClass.replace("month-", "").padStart(2, "0") : "";
                    let rowYear = yearClass ? yearClass.replace("year-", "") : "";

                    let monthMatches = (selectedMonth === "" || rowMonth === selectedMonth);
                    let yearMatches = (rowYear === selectedYear);
                    row.style.display = yearMatches && monthMatches ? "" : "none";
                });
            }

            applyFilters();
        });


    </script>
</body>

</html>