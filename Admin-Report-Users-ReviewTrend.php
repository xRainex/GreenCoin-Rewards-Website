
<?php
    $servername = "localhost"; 
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    // First query: First Time Users details
    $ReviewTrend = [];
    $query1 = "
        SELECT 
            r.date,
            YEAR(r.date) AS review_year,
            LPAD(MONTH(r.date), 2, '0') AS review_month,
            r.star, 
            u.username, 
            r.review, 
            CASE 
                WHEN r.pickup_request_id IS NOT NULL THEN 'Pickup' 
                WHEN r.dropoff_id IS NOT NULL THEN 'Dropoff' 
                ELSE 'Unknown' 
            END AS service_type
        FROM review r
        LEFT JOIN pickup_request p ON r.pickup_request_id = p.pickup_request_id
        LEFT JOIN dropoff d ON r.dropoff_id = d.dropoff_id
        LEFT JOIN user u ON (p.user_id = u.user_id OR d.user_id = u.user_id)
        ORDER BY r.date DESC;
    ";

    $result1 = $conn->query($query1);
    while ($row = $result1->fetch_assoc()) {
        $ReviewTrend[] = $row;
    }

    $data = [];
    $queryChart = "
        WITH user_reviews AS (
            SELECT 
                r.review_id,
                r.date,
                COALESCE(d.user_id, p.user_id) AS user_id
            FROM review r
            LEFT JOIN dropoff d ON r.dropoff_id = d.dropoff_id
            LEFT JOIN pickup_request p ON r.pickup_request_id = p.pickup_request_id
        )
        SELECT 
            YEAR(ur.date) AS review_year,
            LPAD(MONTH(ur.date), 2, '0') AS review_month,
            COUNT(ur.review_id) AS review_count
        FROM user_reviews ur
        GROUP BY review_year, review_month
        ORDER BY review_year, review_month;
    ";
    
    $result2 = $conn->query($queryChart);
    while ($row = $result2->fetch_assoc()) {
        $data[] = [
            "review_year" => (int) $row["review_year"],
            "review_month" => $row["review_month"],
            "review_count" => (int) $row["review_count"]
        ];
    }
    
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Report</title>
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

    @media screen and (max-width: 768px) {
        th, td {
            font-size: 14px;
            padding: 10px;
        }
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

    canvas {
        max-width: 100%;
        height: 300px;
        display: block;
        margin: 0 auto; 
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
        <h2 class="header">Review Trend Report</h2>
        <div class="report-container">
            <div class="yearFilterDropdown">
                    <select id="yearFilter">
                        <option value="2025">Year 2025</option>
                        <option value="2024">Year 2024</option>
                        <option value="2023">Year 2023</option>
                    </select>
                    <i class="fa-solid fa-caret-down dropdown-icon"></i>
                </div>
            <canvas id="reviewsChart"></canvas>
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
                        <th>Date</th>
                        <th>Star</th>
                        <th>User Name</th>
                        <th>Review</th>
                        <th>Service Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($ReviewTrend as $row) :
                            $stars = str_repeat("&#9733;", $row['star']);
                            $month = $row['review_month'];
                            $year = $row['review_year'];
                    ?>
                    <tr class="month-<?php echo $month; ?> year-<?php echo $year; ?>">
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo $stars; ?></td> 
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['review']); ?></td>
                        <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <form action="Admin-Report-Users-Reviews-PDF.php" method="post">
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
            let reviewData = <?php echo json_encode($data); ?>;
            console.log("Loaded Review Data:", reviewData);

            if (!Array.isArray(reviewData)) {
                console.error("Review data is not an array:", reviewData);
                return;
            }

            function filterDataByYear(year) {
                console.log("Filtering data for year:", year);
                return reviewData.filter(item => item.review_year.toString() === year);
            }

            let ctx = document.getElementById("reviewsChart").getContext("2d");
            const monthNames = {
                "01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr",
                "05": "May", "06": "Jun", "07": "Jul", "08": "Aug",
                "09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
            };

            function updateChart(year) {
                let filteredData = filterDataByYear(year);
                console.log("Filtered Data:", filteredData);
                reviewsChart.data.labels = filteredData.map(item => monthNames[item.review_month]);
                reviewsChart.data.datasets[0].data = filteredData.map(item => item.review_count);
                let defaultColor = "rgba(14, 97, 43, 0.6)";
                reviewsChart.data.datasets[0].backgroundColor = new Array(filteredData.length).fill(defaultColor);

                reviewsChart.update();
            }

            function applyFilters() {
                let selectedYear = document.getElementById("yearFilter").value;
                let selectedMonth = document.getElementById("monthFilter").value.padStart(2, "0");

                console.log("Filtering for Year:", selectedYear, "and Month:", selectedMonth);

                let rows = document.querySelectorAll("tbody tr");
                let visibleRows = 0;

                rows.forEach(row => {
                    let rowYear = row.classList.contains(`year-${selectedYear}`); 
                    let rowMonth = row.classList.contains(`month-${selectedMonth}`); 

                    let yearMatches = selectedYear === "" || rowYear; 
                    let monthMatches = selectedMonth === "00" || rowMonth;

                    if (yearMatches && monthMatches) {
                        row.style.display = "";
                        visibleRows++;
                    } else {
                        row.style.display = "none";
                    }
                });

                console.log("Visible Rows After Filtering:", visibleRows);

                let tableBody = document.querySelector("tbody");
                let noDataRow = document.getElementById("no-data-row");

                if (visibleRows === 0) {
                    if (!noDataRow) {
                        noDataRow = document.createElement("tr");
                        noDataRow.id = "no-data-row";
                        noDataRow.innerHTML = `<td colspan="5" style="text-align: center; font-weight: bold;">No reviews found</td>`;
                        tableBody.appendChild(noDataRow);
                    }
                } else {
                    if (noDataRow) {
                        noDataRow.remove();
                    }
                }
            }

            let initialYear = "2025";
            let initialData = filterDataByYear(initialYear);

            let reviewsChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: initialData.map(item => monthNames[item.review_month]),
                    datasets: [{
                        label: "Total Reviews per Month",
                        data: initialData.map(item => item.review_count),
                        backgroundColor: "rgba(14, 97, 43, 0.6)",
                        borderColor: "rgba(14, 97, 43, 1)",
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
                let highlightColor = "rgba(14, 97, 43, 0.6)";
                updateChart(selectedYear);
                applyFilters();
            });

            document.getElementById("monthFilter").addEventListener("change", function () {
                let selectedMonth = this.value.padStart(2, "0");
                let highlightColor = "rgba(153, 201, 143, 0.8)";
                let defaultColor = "rgba(14, 97, 43, 0.6)";

                let newColors = reviewsChart.data.labels.map(label => {
                    return monthNames[selectedMonth] === label ? highlightColor : defaultColor;
                });

                reviewsChart.data.datasets[0].backgroundColor = newColors;
                reviewsChart.update();
                applyFilters(); 
            });

            applyFilters();
        });

    </script>
</body>

</html>
