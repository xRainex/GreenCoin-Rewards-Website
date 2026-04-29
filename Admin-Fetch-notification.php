<?php
// List of allowed columns for sorting
$validSortOptions = ['user_id', 'anoti_id', 'datetime']; 

// Get the sorting option from the GET request
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'datetime'; // Default to 'datetime' if not set
$sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; // Default to 'ASC' if not set

// Validate sortBy to make sure it's a valid column name
if (!in_array($sortBy, $validSortOptions)) {
    $sortBy = 'datetime'; // Set to default if invalid
}

// Validate sortOrder to make sure it's either ASC or DESC
if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
    $sortOrder = 'ASC'; // Set to default if invalid
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cp_assignment";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the query based on sorting option and direction
$query = "SELECT * FROM admin_notification ORDER BY $sortBy $sortOrder";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Output the results as HTML
    while($row = $result->fetch_assoc()) {
        // Check sorting option and display fields accordingly
        if ($sortBy == 'user_id') {
            // Display user_id first if sorting by user_id
            echo "<li>
                    <div class='announcement'>
                        <strong>Sent to User ID:</strong> " . htmlspecialchars($row['user_id']) . "<br>
                        <strong>Announcement ID:</strong> " . htmlspecialchars($row['anoti_id']) . "<br> 
                        <strong>Announcement:</strong> " . htmlspecialchars($row['announcement']) . "<br>
                        <strong>Sent On:</strong> " . htmlspecialchars($row['datetime']) . "
                    </div>
                  </li>";
        } elseif ($sortBy == 'anoti_id') {
            // Display announcement ID first if sorting by anoti_id
            echo "<li>
                    <div class='announcement'>
                        <strong>Announcement ID:</strong> " . htmlspecialchars($row['anoti_id']) . "<br>  
                        <strong>Announcement:</strong> " . htmlspecialchars($row['announcement']) . "<br>
                        <strong>Sent to User ID:</strong> " . htmlspecialchars($row['user_id']) . "<br>
                        <strong>Sent On:</strong> " . htmlspecialchars($row['datetime']) . "
                    </div>
                  </li>";
        } else {
            // Default sorting (by datetime) - Display datetime first (now sorted in descending order)
            echo "<li>
                    <div class='announcement'>        
                        <strong>Sent On:</strong> " . htmlspecialchars($row['datetime']) . "<br>
                        <strong>Announcement ID:</strong> " . htmlspecialchars($row['anoti_id']) . "<br>
                        <strong>Announcement:</strong> " . htmlspecialchars($row['announcement']) . "<br>
                        <strong>Sent to User ID:</strong> " . htmlspecialchars($row['user_id']) . "
                    </div>
                  </li>";
        }
    }
} else {
    echo "No notifications found.";
}

$conn->close();
?>
