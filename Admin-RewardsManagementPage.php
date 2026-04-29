<?php
    require 'google-api-php-client/vendor/autoload.php'; 
    $servername = "localhost";
    $username = "root";  
    $password = "";  
    $dbname = "cp_assignment";  
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Query for Unredeemed Rewards
    $unredeemed = [];
    $queryUnredeemed = "SELECT rr.redeem_reward_id, u.email, u.username, r.reward_name
                        FROM redeem_reward rr
                        JOIN user u ON rr.user_id = u.user_id
                        JOIN reward r ON rr.reward_id = r.reward_id
                        WHERE rr.status = 'Unredeemed'";
    $resultUnredeemed = mysqli_query($conn, $queryUnredeemed);
    if ($resultUnredeemed) {
        while ($row = mysqli_fetch_assoc($resultUnredeemed)) {
            $unredeemed[] = $row;
        }
    }
    
    // Query for Redeemed Rewards
    $redeemed = [];
    $queryRedeemed = "SELECT rr.datetimecomplete, u.email,u.username, r.reward_name, l.location_name
                      FROM redeem_reward rr
                      JOIN user u ON rr.user_id = u.user_id
                      JOIN reward r ON rr.reward_id = r.reward_id
                      JOIN location l ON rr.location_id = l.location_id
                      WHERE rr.status = 'Redeemed'";
    $resultRedeemed = mysqli_query($conn, $queryRedeemed);
    if ($resultRedeemed) {
        while ($row = mysqli_fetch_assoc($resultRedeemed)) {
            $redeemed[] = $row;
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $redeemRewardId = mysqli_real_escape_string($conn, $_POST['redeemRewardId']);
        $locationId = intval($_POST['locationId']);
        $currentTime = mysqli_real_escape_string($conn, $_POST['currentTime']);
    
        $queryUpdate = "
            UPDATE redeem_reward 
            SET status = 'Redeemed', location_id = $locationId, datetimecomplete = '$currentTime' 
            WHERE redeem_reward_id = '$redeemRewardId'
        ";
    
        if (mysqli_query($conn, $queryUpdate)) {
            echo "success";
        } else {
            echo "update_error";
        }
    
        exit;
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rewards Management</title>
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
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color:rgba(220, 245, 237, 0.48);
        display:flex;
        align-items: flex-start; 
        min-height: 100vh;
    }
    
    .categoryitems {
        display: flex;
        flex-grow: 1; 
        overflow: hidden;
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
        margin-top: 30px;
        font-size:30px;
        margin-left:20px;
        margin-bottom: 20px; 
        animation: floatIn 0.8s ease-out;
    }

    .main-content {
        display: flex;
        flex-direction: column;
        flex: 1; 
        padding: 20px;
        min-height: auto; 
        overflow-y: auto;
        overflow-x: hidden;
        width: 80% ;
        max-height: 100vh;
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

    .tab-bar {
        display: flex;
        gap: 1rem;
        background-color: transparent;
        padding: 1rem;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: border-bottom 0.3s, color 0.3s;
        color: #333;
    }

    .tab-btn:hover,
    .tab-btn.active {
        border-bottom: 2px solid #0e612b;
        color: #0e612b;
    }

    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
/* General styles for the reward redemption section */
#reward-redemption {
    padding: 20px;
    font-family: Arial, sans-serif;
}

.redemption-section {
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

button {
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background-color: #45a049;
}

/* Styles for the pop-up modal */
#approveredeemreward-container {
    display: none; /* Hidden by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.approvereward-content {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    width: 400px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transform: scale(0.9);
    transition: opacity 0.3s, transform 0.3s;
}

.approvereward-close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    cursor: pointer;
}

.approvereward-close-btn:hover {
    color: red;
}

#rewardCollectionForm label {
    display: block;
    margin-bottom: 8px;
}

#rewardCollectionForm select,
#rewardCollectionForm button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#rewardCollectionForm button {
    background-color: #4CAF50;
    color: white;
}

#rewardCollectionForm button:hover {
    background-color: #45a049;
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
            <li><a href="#"><i class="fa-solid fa-house"></i>Home</a></li>
            <li><a href="#"><i class="fa-solid fa-envelope"></i>Inbox</a></li>
            <li><a href="#"><i class="fa-solid fa-truck-moving"></i>Pickup Request</a></li>
            <li><a href="#"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Point</a></li>
            <li><a href="#"><i class="fa-solid fa-arrows-rotate"></i>Processing Status</a></li>
            <li class="active"><a href="#"><i class="fa-solid fa-gift"></i>Reward</a></li>
            <li><a href="#"><i class="fa-solid fa-scroll"></i>Report</a></li>
            <li><a href="#"><i class="fa-solid fa-comments"></i>Review</a></li>
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
    <div class ="main-content">
        <div class="tab-bar">
            <button class="tab-btn active" onclick="switchTab('rewards-management')">Rewards Management</button>
            <button class="tab-btn" onclick="switchTab('reward-redemption')">Reward Redemption</button>
        </div>
        <div id="reward-redemption" class="tab-content">
            <h2 class="header">Reward Redemptions</h2>
                <input type="text" id="searchBar" placeholder="Search by email or item..." />
                <!-- Unredeemed Section -->
                <section class="redemption-section">
                    <h3>Unredeemed</h3>
                    <table id="unredeemedTable">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>User Name</th>
                                <th>Reward Item</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unredeemed as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['reward_name']) ?></td>
                                    <td><button class="approve-btn" data-id="<?= $row['redeem_reward_id'] ?>">Approve</button></td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </section>
                <!-- Redeemed Section -->
                <section class="redemption-section">
                    <h3>Redeemed</h3>
                    <table id="redeemedTable">
                        <thead>
                            <tr>
                                <th>Approved On</th>
                                <th>User Email</th>
                                <th>User Name</th>
                                <th>Reward Item</th>
                                <th>Drop-off Centre</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($redeemed as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['datetimecomplete']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['reward_name']) ?></td>
                                <td><?= htmlspecialchars($row['location_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
                <div class="approveBox" id="approveredeemreward-container">
                    <div class="approvereward-content">
                        <span class="approvereward-close-btn" onclick="closeRedemption()">&times;</span>
                        <h2>Reward Collection</h2>
                        <form id="rewardCollectionForm" enctype="multipart/form-data" method="POST">
                            <input type="hidden" id="redeemRewardId" name="redeemRewardId">
                            <label>Location:</label>
                            <select id="editlocation" name="location" required>
                                <?php
                                $query = "SELECT location_id, location_name FROM location";
                                $result = $conn->query($query);

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['location_id']) . "'>" . htmlspecialchars($row['location_name']) . "</option>";
                                    }
                                } 
                                ?>
                            </select>
                            <button type="submit" name="approveReward">Approve</button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
    </div>
    <script>// Switch Tab Function
        function switchTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        // Toggle Dropdown Function
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById("profileDropdown");
            dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
        }

        // Close Dropdown if Clicked Outside
        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("profileDropdown");
            const button = document.querySelector(".dropdown-btn");
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });


        // Search Functionality for Tables
        document.addEventListener("DOMContentLoaded", function () {
            const searchBar = document.getElementById("searchBar");

            searchBar.addEventListener("input", function () {
                const searchText = this.value.toLowerCase();

                filterTable("unredeemedTable", searchText);
                filterTable("redeemedTable", searchText);
            });

            function filterTable(tableId, filterText) {
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

                for (let row of rows) {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(filterText) ? "" : "none";
                }
            }
        });
        document.addEventListener("DOMContentLoaded", function () {
            // Function to open the reward approval modal
            function openRedemption(redeemRewardId) {
                const modal = document.getElementById("approveredeemreward-container");
                const modalContent = modal.querySelector(".approvereward-content");
                const rewardIdInput = document.getElementById("redeemRewardId");

                rewardIdInput.value = redeemRewardId;
                modal.style.display = "flex";

                // Animate the modal appearance
                setTimeout(() => {
                    modalContent.style.opacity = 1;
                    modalContent.style.transform = 'scale(1)';
                }, 100);
            }

            // Close modal function
            window.closeRedemption = function () {
                const modal = document.getElementById("approveredeemreward-container");
                const modalContent = modal.querySelector(".approvereward-content");

                modalContent.style.opacity = 0;
                modalContent.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    modal.style.display = "none";
                }, 300);
            }

            // Attach event listeners to all approve buttons
            const approveButtons = document.querySelectorAll(".approve-btn");
            approveButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const redeemRewardId = button.getAttribute("data-id");
                    openRedemption(redeemRewardId);
                });
            });

            // AJAX form submission for reward approval
            const form = document.getElementById("rewardCollectionForm");
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = {
                    redeemRewardId: formData.get("redeemRewardId"),
                    locationId: formData.get("location"),
                    currentTime: new Date().toISOString().slice(0, 19).replace("T", " ")
                };
                fetch("", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams(data)
                })
                .then(res => res.text())
                .then(response => {
                    if (response === "success") {
                        alert("Reward approved successfully!");
                        window.location.reload();
                    } else if (response === "insert_error") {
                        alert("Failed to insert into redeemed_rewards.");
                    } else if (response === "update_error") {
                        alert("Failed to update redeem_reward.");
                    } else {
                        alert("Unexpected response: " + response);
                    }
                })
                .catch(err => {
                    alert("Network or fetch error: " + err.message);
                });

            });
        });
    </script>
</body>
</html>
