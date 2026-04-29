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

    .today{
        border:none;
        background-color:#FDE2AC;
        padding:10px 20px;
        text-align:center;
        font-size: 16px;
        margin:0 10px 10px 0;
        cursor:pointer;
        border-radius:8px;
        color: #E67E22;
    }

    .all{
        border:none;
        background-color:#CCE5FF;
        padding:10px 20px;
        text-align:center;
        font-size: 16px;
        margin:10px 10px 10px 0px;
        cursor:pointer;
        border-radius:8px;
        color: #2980B9;
    }

    .today:hover,
    .all:hover{
        opacity:70%;
        transform: translateY(-2px);
    }

    .detail-card{
        display: flex;
        flex-direction: column;
        width: 95%;
        margin: 15px 0;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .filter-container {
        display: flex;
        gap: 50px;
        margin: 25px 0;
        flex-wrap: wrap;
        align-items:flex-end;
    }

    .filter-group {
        flex: 0 0 180px;
        
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        
    }

    .filter-group input {
        width: 100%;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .filter-group select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .filter-group input:focus {
        outline: none;
        border-color:#78A24C;
        box-shadow: 0 0 0 2px rgba(120, 162, 76, 0.2);
    }

    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .filter-button{
        background-color: #78A24C;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size:14px;
        border:none;
    }

    .reset-button{
        background-color: #f8f9fa;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size:14px;
        border:1px solid #ddd;
    }

    table{
        border-collapse:collapse;
        width:100%;
        font-size:16px;
    }

    table.center{
        margin-left:auto;
        margin-right:auto;
    }

    th {
        padding: 15px;
        text-align: left;
        background-color:#E0E1E1;
        
    }
    
    td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background-color: rgba(120, 162, 76, 0.05);
        cursor:pointer;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-unread {
        background-color: #FFF3CD;
        color: #856404;
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
    
    <div class="content">
        <div class="title">
            <h2>Dropoff Requests</h2>
        </div>

        <hr style="width: 92%; margin-left:45px;">
        <div>
            <button class="today">Dropoff Requests</button>
            <button class="all" onclick="window.location.href='admin_dropoff_complete.php'">Completed Requests</button>
        </div>
        <div class="detail-card">
            <h3>Dropoff Requests</h3>
            <?php
                $con = mysqli_connect("localhost","root","","cp_assignment");

                if(mysqli_connect_errno()){
                    echo "Failed to connect to MySQL:".mysqli_connect_error();
                }

                $sql = "SELECT 
                        dropoff.dropoff_id,
                        dropoff.dropoff_date,
                        dropoff.status,
                        user.username,
                        location.location_name
                        
                        FROM dropoff 
                        LEFT JOIN user ON dropoff.user_id = user.user_id
                        LEFT JOIN location ON dropoff.location_id = location.location_id
                        WHERE dropoff.status = 'unread'
                        GROUP BY dropoff.dropoff_id
                        ORDER BY dropoff_date, username";
                $result = mysqli_query($con,$sql);
            ?>
            <div class="filter-container">
                <div class="filter-group">
                    <label for="filter_dropoff_date">Filter by Drop Date</label>
                    <input type="date" id="filter_dropoff_date">
                </div>
                <div class="filter-group">
                    <label for="filter_location">Filter by Location</label>
                    <select id="filter_location">
                        <option value="">All Locations</option>
                        <?php
                        // Get all unique locations from the result set
                        $location_query = "SELECT DISTINCT location_name FROM location ORDER BY location_name";
                        $location_result = mysqli_query($con, $location_query);
                        while($loc = mysqli_fetch_assoc($location_result)) {
                            echo '<option value="'.htmlspecialchars($loc['location_name']).'">'.htmlspecialchars($loc['location_name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="filter-button" onclick="filterByDate()">Apply Filter</button>
                    <button class="reset-button" onclick="resetDateFilter()">Reset</button>
                </div>
            </div>
            <table id="assigned_table">
                <tr>
                    <th style="width:5%;"></th>
                    <th style="width:20%;">User Name</th>
                    <th style="width:20%;">Dropoff Date</th>
                    <th style="width:20%;">Dropoff Location</th>
                    <th style="width:10%;">Status</th>
                </tr>
                <?php
                    $counter=1;
                    while($row=mysqli_fetch_array($result)){
                ?>
                <tr class="row_hover" onclick="window.location.href='admin_dropoff_detail.php?dropoff_id=<?php echo $row['dropoff_id'];?>'">
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo $row['username'];?></td>
                    <td><?php echo date('d M Y', strtotime($row['dropoff_date'])); ?></td>
                    <td><?php echo $row['location_name']; ?></td>                    
                    <td><span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>         
                    <?php
                }
                ?>
            </table>
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

        function filterByDate(){
            const dropoffDate = document.getElementById('filter_dropoff_date').value;
            const location = document.getElementById('filter_location').value;
            const table = document.getElementById("assigned_table");
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const rowDropoffDate = cells[2].textContent.trim(); 
                const rowLocation = cells[3].textContent.trim(); 

                const formattedRowDropoffDate = formatDateForComparison(rowDropoffDate.split(' ').slice(0, 3).join(' '));

                let showRow = true;
                if (dropoffDate && formattedRowDropoffDate !== dropoffDate) {
                    showRow = false;
                }
                if (location && rowLocation !== location) {
                    showRow = false;
                }
                rows[i].style.display = showRow ? '' : 'none';
            }


        }

        function resetDateFilter() {
            document.getElementById('filter_dropoff_date').value = '';
            document.getElementById('filter_location').value = '';
            const table = document.getElementById('assigned_table');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                rows[i].style.display = '';
            }
        }

        function formatDateForComparison(displayDate) {
            const months = {
                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04', 
                'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
            };
            
            const parts = displayDate.split(' ');
            if (parts.length === 3) {
                const day = parts[0].padStart(2, '0');
                const month = months[parts[1]];
                const year = parts[2];
                return `${year}-${month}-${day}`;
            }
            return displayDate; 
        }






    </script>

</body>
</html>