<?php
$conn = mysqli_connect("localhost", "root", "", "cp_assignment");
if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL:".mysqli_connect_error();
}

$query = "SELECT date, time, no_driver_per_slot FROM time_slot";
$result = $conn->query($query);

$events = [];

while ($row = $result->fetch_assoc()) {
    $date = $row['date']; 
    $time = $row['time'];
    $no_driver_per_slot = $row['no_driver_per_slot'];
    
    $today = date('Y-m-d');
    $oneWeekLater = date('Y-m-d', strtotime('+7 days'));

    if ($date < $today || $date < $oneWeekLater || $no_driver_per_slot == 0) {
        $status = 'disabled';  
    } else {
        $status = 'enabled';
    }
    // $text = 'Available Pickup: ' . $time;

    $timestamp = strtotime($time);
    $formattedTime = date("H:i ", $timestamp);
    $text = $formattedTime . " - " . date("H:i", $timestamp + 60 * 60);

    if (!isset($events[$date])) {
        $events[$date] = [];
    }
    
    $events[$date][] = ['status' => $status, 'content' => $text];  
}

$conn->close();
echo json_encode($events);
?>