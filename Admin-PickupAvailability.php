<?php
    session_start();
    
    $admin_id = $_SESSION["admin_id"] ?? null;

    $con = mysqli_connect("localhost", "root", "", "cp_assignment");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $result = $con->query("SELECT `name` FROM admin LIMIT 1"); 
    $testAdmin = $result->fetch_assoc();

    if (isset($_GET['action']) && $_GET['action'] === 'get_driver_count') {
        header('Content-Type: application/json');
    
        $driverRes = mysqli_query($con, "SELECT COUNT(*) as total FROM driver");
        $driverCount = mysqli_fetch_assoc($driverRes)['total'] ?? 0;
    
        $locationsRes = mysqli_query($con, "SELECT location_id, location_name FROM location");
        $locations = [];
    
        while ($row = mysqli_fetch_assoc($locationsRes)) {
            $locations[] = $row;
        }
    
        echo json_encode([
            "driver_count" => (int)$driverCount,
            "locations" => $locations
        ]);
        exit();
    }

    // AJAX timeslot fetch
    if (isset($_GET['date']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        $date = $_GET['date'];

        $query = "
            SELECT ts.time_slot_id, ts.date, ts.time, ts.no_driver_per_slot
            FROM time_slot ts
            WHERE ts.date = ?
            ORDER BY ts.time
        ";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 's', $date);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo "<div style='display: flex; flex-direction: column; gap: 15px;'>";

            while ($row = mysqli_fetch_assoc($result)) {
                $time = $row['time'];
                $count = $row['no_driver_per_slot'];
                echo "
                    <div style='border: 1px solid #ccc; border-radius: 10px; padding: 15px; background: #f9fff8; box-shadow: 0 2px 6px rgba(0,0,0,0.05);'>
                        <h4 style='margin-bottom: 8px;'>$time</h4>
                        <p><strong>Drivers Allowed:</strong> $count</p>
                    </div>
                ";
            }

            echo "</div>";
        } else {
            echo "<p>No timeslots available for this date.</p>";
        }

        exit();
    }

    // Handle timeslot creation via POST 
    if (
        $_SERVER["REQUEST_METHOD"] === "POST" &&
        isset($_POST['date'], $_POST['time'])
    ) {
        $date = $_POST['date'];
        $time = $_POST['time'];

        // Get driver count
        $driverResult = mysqli_query($con, "SELECT COUNT(*) as total FROM driver");
        $driverRow = mysqli_fetch_assoc($driverResult);
        $maxDrivers = $driverRow['total'] ?? 0;

        $stmt = $con->prepare("INSERT INTO time_slot (date, time, no_driver_per_slot) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $date, $time, $maxDrivers);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Fetch admin details
    $admin_details = [];
    if ($admin_id) {
        $adminQuery = "SELECT * FROM admin WHERE admin_id = '$admin_id'";
        $adminResult = mysqli_query($con, $adminQuery);
        $admin_details = mysqli_fetch_assoc($adminResult);

        // Count unread notifications
        $unreadQuery = "SELECT COUNT(*) AS unread_count FROM user_notification WHERE admin_id = '$admin_id' AND status = 'unread'";
        $unreadResult = mysqli_query($con, $unreadQuery);
        $unreadData = mysqli_fetch_assoc($unreadResult);
        $unreadCount = $unreadData['unread_count'] ?? 0;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


    <title>Pickup Availability - Green Coin</title>

<style>
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

    .container {
        display: flex; 
        align-items: stretch; 
        min-height: 100vh; 
        width: 100%;
    }

    .sidebar {
        width: 250px;
        background: #f8f9fa;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        justify-content: space-between;
        flex-direction: column;
        display: flex;
        position: fixed;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 100;
    }

    .profile-container{
        width:100%;
        /*margin-top:130px;*/
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

    #scrollTopBtn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: green;
        color: white;
        border: none;
        border-radius: 50%;
        display: none; 
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 20px;
        transition: opacity 0.3s, transform 0.3s;
        z-index: 100;
    }

    #scrollTopBtn:hover {
        background-color: darkgreen;
        transform: scale(1.1);
    }

    .main-content {
        margin-left: 250px; /* Same as sidebar width */
        padding: 30px;
        width: calc(100% - 250px);
        overflow-x: hidden;
        position: relative;
    }

    .pickupavailability-container{
        flex: 1;
        width: 100%;
        padding: 50px;
        margin-left: 20px;
    }

    .pickupavailability-header{
        flex-direction: column;
        align-items: left;
        border-radius: 30px;
        max-height: 1000px;
        color: black;
        width: 100%;
        justify-content: space-between;
    }

    .timeslot-display-panel {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .timeslot-content {
        flex: 1;
        padding: 25px;
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }


    #selectedDateDisplay {
        font-weight: bold;
        font-size: 18px;
        color: #1a3c2f;
        border-bottom: 1px solid #d4d4d4;
        padding-bottom: 8px;
    }

    #timeslotTableContainer .timeslot-card {
        border: 1px solid #d5e5d0;
        border-left: 5px solid #4CAF50;
        padding: 12px 18px;
        background: #f6fff8;
        border-radius: 8px;
        transition: transform 0.2s;
    }

    #timeslotTableContainer .timeslot-card:hover {
        transform: scale(1.03);
        background-color: #e8ffe7;
    }

    #timeslotTableContainer h4 {
        margin: 0 0 5px;
        font-size: 16px;
        color: #333;
    }

    #timeslotTableContainer p {
        margin: 0;
        font-size: 14px;
    }

    .slot-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 10px;
    }

    .slot-block {
        background-color: #f3f3f3;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        cursor: pointer;
        text-align: center;
        font-weight: bold;
        transition: 0.2s ease;
    }

    .slot-block:hover {
        background-color: #d1f5d0;
        border-color: #4CAF50;
    }

    .slot-block.active {
        background-color: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }


</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.getElementById("fullCalendar");
        const createSlotBtn = document.getElementById("createSlotBtn");
        const slotModal = document.getElementById("createSlotModal");
        const dateInput = document.querySelector("input[name='date']");
        const selectedInput = document.getElementById("selectedSlotInput");
        const slotContainer = document.getElementById("slotOptionsContainer");
        const summaryText = document.getElementById("slotSummaryText");
        const form = document.getElementById("slotForm");

        const startHour = 10;
        const endHour = 18;

    function toggleDropdown(event) {
            event.stopPropagation(); 
            const dropdown = document.getElementById("profileDropdown");
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }

        document.querySelector(".dropdown-btn").addEventListener("click", toggleDropdown);

        document.addEventListener("click", function (event) {
            const dropdown = document.getElementById("profileDropdown");
            const button = document.querySelector(".dropdown-btn");
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });


    // ─── Open Modal Function ──────────────────────────────
    function openSlotModal() {
        slotModal.style.display = "flex";
        updateSummaryText(); // Ensure text updates when modal opens
    }

    // ─── Close Modal Function ─────────────────────────────
    function closeSlotModal() {
        slotModal.style.display = "none";
    }

    // Make cancel button work
    window.closeSlotModal = closeSlotModal;

    // ─── Calendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        height: "auto",
        selectable: true,
        dayHeaderContent: function(arg) {
        // Capitalize the first letter of the day name
        return arg.text.charAt(0).toUpperCase() + arg.text.slice(1);
        },
        buttonText: {
            today: 'Today'
        },
        dateClick: function (info) {
            const dateStr = info.dateStr;
            dateInput.value = dateStr;

            const prettyDate = new Date(dateStr).toLocaleDateString("en-GB", {
                weekday: "long",
                year: "numeric",
                month: "short",
                day: "numeric"
            });

            const dateDisplay = document.getElementById("selectedDateDisplay");
            if (dateDisplay) {
                dateDisplay.textContent = "Selected Date: " + prettyDate;
            }

            fetch("Admin-PickupAvailability.php?date=" + encodeURIComponent(dateStr), {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("timeslotTableContainer").innerHTML = html;
            });

            updateSummaryText();
        }
    });
    calendar.render();

    // ─── Open modal on button click ───────────────────────
    createSlotBtn.addEventListener("click", openSlotModal);

    // ─── Close modal on outside click ─────────────────────
    window.addEventListener("click", function (event) {
        if (event.target === slotModal) {
            closeSlotModal();
        }
    });

    // ─── Generate Slot Blocks
    function formatHour(hour24) {
        const hour = hour24 % 12 === 0 ? 12 : hour24 % 12;
        const ampm = hour24 < 12 || hour24 === 24 ? "AM" : "PM";
        return `${String(hour).padStart(2, "0")}:00 ${ampm}`;
    }

    slotContainer.innerHTML = "";
    const desiredHours = [11, 14, 16]; // 11am, 2pm, 4pm in 24-hour format

    desiredHours.forEach(hour => {
        const start = formatHour(hour);
        const end = formatHour(hour + 1);
        const displayText = `${start} - ${end}`;

        const slot = document.createElement("div");
        slot.classList.add("slot-block");
        slot.textContent = displayText;
        slot.dataset.start = start;
        slot.dataset.end = end;

        slot.addEventListener("click", function () {
            document.querySelectorAll(".slot-block").forEach(s => s.classList.remove("active"));
            slot.classList.add("active");
            selectedInput.value = start;
            updateSummaryText();
        });

        slotContainer.appendChild(slot);
    });

    // ─── Update Summary Paragraph ──────────────────────────
    function updateSummaryText() {
        const selectedSlot = document.querySelector(".slot-block.active");
        const selectedDate = dateInput.value;

        if (selectedDate && selectedSlot) {
            const prettyDate = new Date(selectedDate).toLocaleDateString("en-GB", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            });

            summaryText.textContent = `Green Coin will be available for pickups on ${prettyDate} from ${selectedSlot.dataset.start} to ${selectedSlot.dataset.end}.`;
        } else {
            summaryText.textContent = "";
        }
    }

    // ─── Validation for Submission ─────────────────────────
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateInput.min = tomorrow.toISOString().split("T")[0];

    form.addEventListener("submit", function (e) {
        if (!selectedInput.value || !dateInput.value) {
            e.preventDefault();
            alert("Please select both a date and time slot.");
        }
    });
});
</script>





</head>
<body>
<div class="container">
<nav class="sidebar">
        <div class="sidebar-top">
        <div>
            <img src="User-Logo.png" style="width: 200px; margin-bottom: 25px; background-color: #78A24C; padding: 10px; border-radius: 10px; cursor: pointer; margin-left: 13px;" onclick="AdminHomePage()">
        </div>
        <ul class="menu">
        <li><a href="#"><i class="fa-solid fa-house"></i>Dashboard</a></li>
                <li><a href="#"><i class="fa-solid fa-envelope"></i>Notifications</a></li>
                <li><a href="#"><i class="fa-solid fa-truck-moving"></i>Pickup Requests</a></li>
                <li class="active"><a href="Admin-PickupAvailability.php"><i class="fa-regular fa-calendar-days"></i>Pickup Availability</a></li>
                <li><a href="#"><i class="fa-solid fa-address-book"></i>Drivers</a></li> 
                <li><a href="#"><i class="fa-solid fa-dolly"></i>Drop-Off Requests</a></li>               
                <li><a href="#"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Points</a></li>
                <li><a href="Admin-RewardsItemsPage.php"><i class="fa-solid fa-gift"></i>Rewards</a></li>
                <li><a href="Admin-Review.php"><i class="fa-solid fa-comments"></i>Reviews</a></li>
                <li><a href="Admin-ReportsPage.php"><i class="fa-solid fa-scroll"></i>Reports</a></li>
                <li><a href="Admin-FAQ.php"><i class="fa-solid fa-circle-question"></i>FAQ</a></li>
        </ul>
        </div>
        <div class="profile-container" style="position: relative; display: inline-block;">
            <div class="profile">
                <i class="profileicon fa-solid fa-circle-user"></i>
                <div class="profile-info">
                    <p><strong><?php echo $testAdmin['name'] ?></strong></p>
                </div>
                <button class="dropdown-btn" onclick="toggleDropdown(event)">
                    <i class="fa-solid fa-chevron-down"></i>
                </button> 
            </div>
            <div class="dropdown" id="profileDropdown">
                <a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
    </nav>

    <button id="scrollTopBtn">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    
    <main class="main-content">
        <div class="pickupavailability-container">

            <header class="pickupavailability-header">
                <h1 class="pickupavailability-header-title">Pickup Availability</h1>
                <p class="pickupavailability-header-desc" style="margin-top:20px">These are the time slots for when our drivers are available for pickup !</p> <br>
                <button id="createSlotBtn" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    + Create Pickup Slot
                </button>

            </header>

            <div class="calendar-table-wrapper" style="display: flex; flex-direction: row; gap: 30px; margin-top: 40px;">
                <!-- Calendar -->
                <section class="calendar" style="min-width: 60%; max-width: 60%;">
                    <div id="fullCalendar" style="width: 100%; margin: 0 auto;"></div>
                </section>

                <!-- Timeslot display -->
                <section class="timeslot-display-panel">
                    <div class="timeslot-content">
                        <p id="selectedDateDisplay">Selected Date: —</p>
                        <div id="timeslotTableContainer">
                            <p>Select a date on the calendar to view timeslots.</p>
                        </div>
                    </div>
                </section>

            </div>

        </div>
    </main>
</div>





<!-- Create Pickup Slot Modal -->
<div id="createSlotModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 15px; width: 420px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h2 style="margin-bottom: 20px; text-align: center;">Create Pickup Slot</h2>
        <form id="slotForm" method="POST" action="Admin-PickupAvailability.php">
            <label>Date:</label>
            <input type="date" name="date" required style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc;"><br>

            <label>Time Slot:</label>
            <div class="custom-slot-selector">
                <input type="hidden" name="time" id="selectedSlotInput" required>
                <div id="slotOptionsContainer" class="slot-options"></div>
            </div> <br>
            <p id="slotSummaryText" style="margin-top: 20px; color: #333; font-weight: bold;"></p>


            <div style="text-align: right;">
            <button type="submit" 
                style="background: #4CAF50; color: white; padding: 10px 20px; border: none; 
                    border-radius: 6px; margin-right: 10px; cursor: pointer;">
                Save
            </button>

            <button type="button" onclick="closeSlotModal()" 
                style="padding: 10px 20px; border-radius: 6px; background: #ccc; 
                    border: none; cursor: pointer;">
                Cancel
            </button>
            </div>
        </form>
    </div>
</div>



</body>
</html>