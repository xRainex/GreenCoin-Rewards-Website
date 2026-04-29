<?php
    session_start();
    
    //$admin_id = $_SESSION["admin_id"] ?? null; //by default it is null, so need to login 

    $conn = mysqli_connect("localhost", "root", "", "cp_assignment");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $result = $conn->query("SELECT `name` FROM admin LIMIT 1"); 
    $testAdmin = $result->fetch_assoc();
    
    // Handle reply submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        $reviewId = $_POST['review_id'] ?? null; //default
        $replyText = $_POST['reply_text'] ?? null;
        
        if ($reviewId && $replyText) {
            $date = date('Y-m-d H:i:s');
            $query = "INSERT INTO reply_review (review_id, review, date) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iss", $reviewId, $replyText, $date);
            
            if ($stmt->execute()) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error saving reply: " . $conn->error;
            }
        } else {
            echo "Missing required data";
        }
        exit(); 
    }

    $unreadCount = 0; 

    //all reviews from the database
    $reviewQuery = "SELECT * FROM review ORDER BY date DESC";
    $reviewResult = mysqli_query($conn, $reviewQuery);
    $reviews = mysqli_fetch_all($reviewResult, MYSQLI_ASSOC);



    $starCounts = [
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0
    ];

    $starCountQuery = mysqli_query($conn, "SELECT star, COUNT(*) as count FROM review GROUP BY star");

    while ($row = mysqli_fetch_assoc($starCountQuery)) {
        $star = (int)$row['star'];
        if (isset($starCounts[$star])) {
            $starCounts[$star] = $row['count'];
        }
    }

    $star1_num = $starCounts[1];
    $star2_num = $starCounts[2];
    $star3_num = $starCounts[3];
    $star4_num = $starCounts[4];
    $star5_num = $starCounts[5];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Review - Green Coin</title>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playpen+Sans:wght@100..800&display=swap');
    

    *{
        margin: 0px;
        padding: 0px;
        font-family: "Open Sans", sans-serif;
        box-sizing: border-box;
    }

    html{
        background-color:rgba(220, 245, 237, 0.48);
        height:100%;
    }

    .logo-container img {
            height: 40px;
            cursor: pointer;
        }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: rgba(220, 245, 237, 0.48);
        min-height: 100vh;
    }

    .main-content {
        margin-left: 250px; /* Same as sidebar width */
        padding: 30px;
        width: calc(100% - 250px);
        overflow-x: hidden;
        position: relative;
    }

    .review-layout {
        display: block;
    }

    .container {
        display: flex;
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
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


    .sidebar-top .logo-container {
        margin-bottom: 20px;
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



    .header-container {
        position: absolute;
        left: 280px; 
        top: 20px;
        width: calc(100% - 300px);
        text-align: left;
        font-size: 18px ;
        font-weight: bold;
        padding: 30px;
        border-radius: 10px;
        /*margin-left: 100px;*/
    }

    .top-container{
        background-image:url("testing 32.svg");
        background-repeat: no-repeat;
        background-size:100%;
        background-position: center;
        padding: 3vh 5vw 12vh;
        margin-top: 5px;
        height:auto;
        border-radius:20px;
    }


    .title-container{
        margin:auto;
        width: 45%;
        padding-top: 20px;
    }


    .title-container h1{
        text-align:center;
        margin-bottom:20px;
        font-family: "Playpen Sans", cursive;
        font-size:48px;
        line-height: 2.1;
        letter-spacing: 2px;
    }


    .title-container p{
        font-size: 15px;
        text-align:center;
        font-family: "Playpen Sans", cursive;
    }

    .rating-container{
        width: 20%;
        margin:20px 20px 20px 150px;
        place-items: center;
        border:2px solid #636363;
        display:flex;
        flex-direction:column;
        gap:20px;
        border-radius:20px;
        background-color: #fef9d7;
        height:55vh;
    }

    .average-rating, .all-rating, .star-container {
        /*display: flex;*/
        flex-direction: column;
        align-items: center;
    }


    .rating-number h1{
        text-align:center;
        font-size:40px;
        margin-bottom:5px;
        font-family:"Playpen Sans", sans-serif;
    }

    .rating-number p {
        text-align:center;
        font-family:"Playpen Sans", sans-serif;
        margin-bottom:5px;
    }


    .average-rating .star-container::before{
        content:"\2605 \2605 \2605 \2605 \2605";
        color: lightgrey;
        font-size:25px;
    }

    .average-rating .star::before{
        content:"\2605 \2605 \2605 \2605 \2605";
        color: #f8c455;
        font-size:25px;
    }



    .all-rating-div{
        margin: 0px 0px 20px 0px;
    }

    .rating-progress-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 6px 0;
        width: 100%;
    }

    .rating-row {
        display: flex;
        align-items: center;
        margin: 8px 0;
        gap: 10px;
    }

    .star-num {
        width: 10%;
        text-align: right;
        font-weight: bold;
        font-size: 16px;
    }

    .progress {
        width: 85%;
        height: 12px;
        background-color: white;
        border: 1px solid black;
        border-radius: 25px;
        position: relative;
    }

    .bar {
        height: 100%;
        background-color: #f8c455;
        border-radius: 25px;
    }


    .num-rate{
        padding:10px;
        width: 30px;
        width: 50%;
        text-align:right;
    }
        
    .num-rate p{
        font-family:"Playpen Sans", sans-serif;
    }

        
    .review-container {
        flex: 1;
        max-width: calc(100% - 340px);
        margin: 0 auto;
        padding: 0px 0px;
    }

    .review-row{
        /* width: 80vw; */
        margin: 35px auto 10px;
        border-radius:25px;
        border: 2px solid lightgrey;
        display:flex;
        flex-direction:column;
        padding:20px 10px;
    }

    .reply-review{
        /* width: 80vw; */
        margin: 0px auto 5px;
        border-radius:25px;
        border: 2px solid lightgrey;
        display:flex;
        flex-direction:column;
        padding:15px 10px;
        background-color:rgb(231, 231, 231);
    }

    .review-top{
        display:flex;
        flex-direction: row;
        width: 100%;
    }

    .review-profile-div{
        display:flex;
        flex-direction:row;
        width: 90%;
    }

    .review-profile-img{
        width: 5%;
        margin:5px 20px 0px 10px;
    }

    .review-profile-img img{
        border:1.5px solid black;
        border-radius:50%;
        padding:5px;
    }

    .review-profile-detail{
        width: 80%;
        margin:auto 5px;
    }

    .review-username{
        font-size:16px;
        font-weight:bold;
        padding-bottom:5px;
        margin-left:10px;
    }

    .review-item{
        color:grey;
        font-size:12px;
        font-weight:bold;
        margin-left:10px;
    }

    .user-star-rating-div{
        position:relative;
        display:inline-block;
        margin-left:15px;
        margin-bottom:5px;
    }

    .review-date-div{
        width: 15%;
        margin:20px 10px;
        text-align:right;
    }

    .review-date-div p{
        color:grey;
        font-size:12px;
        font-weight:bold;
    }

    .review-text-div{
        align-items:right;
        place-items:right;
        margin:0px 30px 10px 0px;
        margin-left:20px;
        line-height:1.4;
    }

    .reply-review-top{
        display:flex;
        flex-direction: row;
        width: 100%;
    }

    .reply-review-profile-div{
        display:flex;
        flex-direction:row;
        width: 90%;
    }

    .reply-review-profile-img{
        width: 5%;
        margin:0px 20px 0px 10px;
            
    }

    .reply-review-profile-img i{
        font-size:25px;
        padding:10px 12px;
        border:2px solid black;
        border-radius:50%;
    }

    .reply-review-profile-detail{
        width: 80%;
        margin:auto 5px;
    }

    .reply-review-date-div{
        width: 15%;
        margin:auto 10px;
        text-align:right;
    }

    .reply-review-date-div p{
        color:grey;
        font-size:12px;
        font-weight:bold;
    }

    .pagination{
        position:relative;
        width: 100%;
        display: flex;
        flex-direction:row;
        margin-bottom:50px;
        margin-top:50px;
        justify-content:center;
        align-items:center;
    }

    .pagination a , .page-numbers a{
        padding: 8px 16px;
        margin: 5px;
        color:rgb(212, 212, 212);
        text-decoration: none;
        border-radius: 5px;
        font-weight:bold;
        font-family:"Playpen Sans", sans-serif;
    }

    .page-numbers a:hover {
        color: grey !important;
    }

    .pagination a:hover {
        color: grey;
    }

    .selected-page{
        color:black !important;
        pointer-events: none;
    }

         
    .page-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 20px;
    }

    .summary-box {
        max-width: calc(100% - 340px);
        margin: 0 auto 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        background-color: #fef9d7;
        border: 2px solid #636363;
        border-radius: 20px;
        padding: 30px;
        box-sizing: border-box;
    }

    .summary-left {
        flex: 0 0 auto; 
    }

    .summary-right {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end; 
    }


    
    .star-container{
        position:relative;
        display:flex;
    }

    .star{
        position:absolute;
        top:0;
        left:0;
        width: 0%;
        overflow:hidden;
    }
        
    .all-rating{
        width: 100%;
        padding:0px 10px 10px 50px;
    }

    .average-stars {
        position: relative;
        font-size: 24px;
        width: max-content;
        margin-top: 5px;
        line-height: 1;
    }

    .star-background {
        position: relative;
        display: inline-block;
        padding-left:5px;
    }

    .star-background::before {
        content: "★★★★★";
        color: lightgray;
        letter-spacing: 3px;
        text-align: center;
        padding-left:5px;
    }

    .star-fill {
        position: absolute;
        top: 0;
        left: 0;
        white-space: nowrap;
        overflow: hidden;
        height: 100%;
        padding-left:5px;
    }

    .star-fill::before {
        content: "★★★★★";
        color: #f8c455;
        letter-spacing: 3px;
        padding-left:5px;
    }


    .fill {
        height: 100%;
        background-color: #f8c455;
        border-radius: 25px;
        padding-left:5px;
    }


        
    .reply-btn {
        margin-top: 10px;
        padding: 5px 10px;
        background-color: #78A24C;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .reply-btn:hover {
        background-color: #5d823c;
    }

    .reply-form-container {
        margin-top: 10px;
        background-color:rgb(255, 255, 255);
        border-radius: 25px;
        padding: 10px 15px;
        border: 2px solid #ddd;
    }

    .reply-form {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .reply-form input {
        flex: 1;
        padding: 12px 15px;
        border: none;
        border-radius: 25px 0 0 25px;
        outline: none;
        font-size: 14px;
        background-color: white;
    }
    
    .reply-form input::placeholder {
        color: #999;
    }

    .submit-reply-btn, .cancel-reply-btn {
        padding: 5px 15px;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .submit-reply-btn {
        padding: 12px 18px;
        border: none;
        /* border-radius: 0 25px 25px 0; */
        background-color:grey;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }


    .submit-reply-btn:hover {
        background-color: #5d823c;
    }

    .cancel-reply-btn {
        background-color: #f44336;
    }

    .cancel-reply-btn:hover {
        background-color: #d32f2f;
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
</style>
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
                <li><a href="Admin-PickupAvailability.php"><i class="fa-regular fa-calendar-days"></i>Pickup Availability</a></li>
                <li><a href="#"><i class="fa-solid fa-address-book"></i>Drivers</a></li> 
                <li><a href="#"><i class="fa-solid fa-dolly"></i>Drop-Off Requests</a></li>               
                <li><a href="#"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Points</a></li>
                <li><a href="Admin-RewardsItemsPage.php"><i class="fa-solid fa-gift"></i>Rewards</a></li>
                <li class="active"><a href="Admin-Review.php"><i class="fa-solid fa-comments"></i>Reviews</a></li>
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
    <div class="review-layout">
    <section class="summary-box">
        <div class="summary-left">
        <div class="rating-number">
            <h1></h1>
            <p>6 reviews</p>
        </div>
        <div class="average-stars">
            <div class="star-background">
            <div class="star-fill"></div>
            </div>
        </div>
        </div>

        <div class="summary-right">
            <div class="all-rating"></div>
        </div>
    </section>


    
    <section class="review-container">
        <?php 

                $start = 0;
                $rows_per_page = 3;
                $countRowInReviewQuery = mysqli_query($conn, "SELECT * FROM review");
                $totalRowsInReview = mysqli_num_rows($countRowInReviewQuery);
                
                $pages = ceil($totalRowsInReview / $rows_per_page);

                if ((isset($_GET['page-nr']))){
                    $page = $_GET['page-nr'] - 1;
                    $start = $page * $rows_per_page;
                }

                $getReviewQuery = mysqli_query($conn, "SELECT * FROM review ORDER BY date DESC LIMIT $start, $rows_per_page");
                while($getReviewResult = mysqli_fetch_assoc($getReviewQuery)){
                    $userRating = ($getReviewResult['star'] / 5) * 100;
                    if ($getReviewResult['pickup_request_id'] != null){
                        $pickupRequestID = $getReviewResult['pickup_request_id'];
                        $getReviewUserQuery = mysqli_query($conn, "SELECT u.username AS username, u.profile_image AS profileImg FROM pickup_request pr 
                                                                    INNER JOIN user u ON pr.user_id = u.user_id
                                                                    INNER JOIN item_pickup ipr ON pr.pickup_request_id = ipr.pickup_request_id 
                                                                    WHERE pr.pickup_request_id = '$pickupRequestID'"); 

                        $getReviewItemQuery = mysqli_query($conn, "SELECT i.item_name AS itemName, ipr.quantity AS Quantity FROM item_pickup ipr
                                                                    INNER JOIN item i ON ipr.item_id = i.item_id 
                                                                    WHERE ipr.pickup_request_id = '$pickupRequestID'");
                    }else if ($getReviewResult['dropoff_id'] != null){
                        $dropoffID = $getReviewResult['dropoff_id'];
                        $getReviewUserQuery = mysqli_query($conn, "SELECT u.username AS username, u.profile_image AS profileImg FROM dropoff dr 
                                                                    INNER JOIN user u ON dr.user_id = u.user_id
                                                                    INNER JOIN item_dropoff idr ON dr.dropoff_id = idr.dropoff_id
                                                                    WHERE dr.dropoff_id = '$dropoffID'"); 

                        $getReviewItemQuery = mysqli_query($conn, "SELECT i.item_name AS itemName, idr.quantity AS Quantity FROM item_dropoff idr
                                                                    INNER JOIN item i ON idr.item_id = i.item_id 
                                                                    WHERE idr.dropoff_id = '$dropoffID'");
                    }
                    $getReviewUserResult = mysqli_fetch_assoc($getReviewUserQuery);

                echo '<div class="review-reply-wrapper">';
                echo '<div class="review-row">';
                    echo '<div class="review-top">';
                        // --- User Details Start ---
                            echo '<div class="review-profile-div">';
                                echo '<div class="review-profile-img"  style="padding-left:10px;padding-right:30px;">';
                                    echo '<img src="'.$getReviewUserResult['profileImg'].'" width="50">';
                                echo '</div>';
                                echo '<div class="review-profile-detail">';
                                    echo '<h3 class="review-username">'.$getReviewUserResult['username'].'</h3>';
                                    echo '<p class="review-item">';
                                        $count = 1;
                                        while($getReviewItemResult = mysqli_fetch_assoc($getReviewItemQuery)){
                                            if ($count == 1){
                                                echo '<span>'.$getReviewItemResult['itemName'].' (x'.$getReviewItemResult['Quantity'].')<span>';
                                            }else{
                                                echo '<span>, '.$getReviewItemResult['itemName'].' (x'.$getReviewItemResult['Quantity'].')<span>';
                                            }
                                            $count += 1;
                                        }
                                    echo '</p>';
                                echo '</div>';
                            echo '</div>';
                        // --- User Details End ---
                        // --- Date Start ---
                            echo '<div class="review-date-div">';
                                echo '<p>'.$getReviewResult['date'].'</p>';
                            echo '</div>';
                        // --- Date End ---
                    echo '</div>';
            

                    // --- User star rating Start ---
                        $filledStars = $getReviewResult['star'];
                        $emptyStars = 5 - $filledStars;
                        echo '<br><div class="user-star-rating-div" style="font-size: 25px; color: #f8c455; padding-left:10px;">';
                        for ($i = 0; $i < $filledStars; $i++) {
                            echo '&#9733;'; // ★ filled star(s)
                        }
                        for ($i = 0; $i < $emptyStars; $i++) {
                            echo '<span style="color: lightgray;">&#9733;</span>';
                        }
                        echo '</div><br>';
                    // --- User star rating End ---

                    // --- User reply Start ---
                    echo '<div class="review-text-div" style="padding-left:10px;">';
                    echo '<p>'.$getReviewResult['review'].'</p>';
                    echo '</div>';
                    // --- User reply End ---
                    echo '</div>'; // Review-row's end div


                    // --- Spawns reply container Start ---
                    $reviewID = $getReviewResult['review_id'];
                    $getReplyReviewQuery = mysqli_query($conn, "SELECT * FROM reply_review WHERE review_id = '$reviewID'");
                    $hasReply = mysqli_num_rows($getReplyReviewQuery) > 0;

                    if (!$hasReply) {
                        echo '<div class="reply-form-container" id="reply-form-'.$reviewID.'">';
                            echo '<form class="reply-form" data-review-id="'.$reviewID.'">';
                                echo '<input type="text" name="reply_text" placeholder="Write your reply here..." maxlength="255">';
                                echo '<button type="submit" class="submit-reply-btn"><i class="fa-solid fa-paper-plane"></i></button>';
                            echo '</form>';
                        echo '</div>';
                    
                    }
                    // --- Spawns reply container End ---

                    // --- Admin reply Start ---
                    $reviewID = $getReviewResult['review_id'];
                    $getReplyReviewQuery = mysqli_query($conn, "SELECT * FROM reply_review WHERE review_id = '$reviewID'");
                    while ($getReplyReviewResult = mysqli_fetch_assoc($getReplyReviewQuery)){
                        echo '<div class="reply-review">';
                            echo '<div class="reply-review-top">';
                                echo '<div class="reply-review-profile-div">';
                                    echo '<div class="reply-review-profile-img" style="padding-left:20px;padding-right:30px;margin-left:5px;">';
                                        echo '<center><i class="fa-solid fa-user-tie"></i></center>';
                                    echo '</div>';
                                    echo '<div class="reply-review-profile-detail">';
                                        echo '<h3 class="review-username" style="padding:5px;margin-left:5px">Green Coin</h3>';
                                    echo '</div>';
                                echo '</div>';
                                // --- Date Start ---
                                echo '<div class="reply-review-date-div">';
                                    echo '<p>'.$getReplyReviewResult['date'].'</p>';
                                echo '</div>';
                                // --- Date End ---
                            echo '</div>';
                            echo '<br><div class="review-text-div" style=" padding-left:10px;">';
                                echo '<p>'.$getReplyReviewResult['review'].'</p>';
                            echo '</div>';
                        echo '</div>';
                    }
                    // --- Admin reply End ---
                echo '</div>';


            }

            // --- Pagination Start ---
            $range = 2;
            $id = isset($_GET['page-nr']) ? (int)$_GET['page-nr'] : 1;
            echo '<div class="pagination">';
                echo '<div>';
                    echo '<a href="?page-nr=1"> << </a>';
                echo '</div>';

                echo '<div>';
                    if (isset($_GET['page-nr']) && $_GET['page-nr'] > 1){
                        $previous_page = $_GET['page-nr'] - 1; 
                        echo '<a href="?page-nr='.$previous_page.'"> < </a>';
                    }else{
                        echo '<a> < </a>';
                    }
                echo '</div>';

                if ($pages > 5) {
                    if ($id > $range + 2) {
                        echo '<a href="?page-nr=1">1</a>';
                        echo '<span>...</span>';
                    }
                }          

                echo '<div class="page-numbers">';
                    for ($i = max(1, $id - $range); $i <= min($pages, $id + $range); $i++) {
                        // for($i = 1; $i <= $pages; $i++) {
                            if ($i == $id) {
                                echo '<a class="selected-page" href="?page-nr='.$i.'">'.$i.'</a>'; // Highlight current page
                            } else {
                                echo '<a href="?page-nr='.$i.'">'.$i.'</a>';
                            }
                        // }
                    }
                echo '</div>';

                if ($pages > 5 && $id < $pages - $range - 1) {
                    echo '<span>...</span>';
                    echo '<a href="?page-nr=' . $pages . '">' . $pages . '</a>';
                }
                
            
                echo '<div>';
                    if (!isset($_GET['page-nr'])){
                        $next_page = 2; 
                        echo '<a href="?page-nr='.$next_page.'"> > </a>';
                    }else{
                        if ($_GET['page-nr'] >= $pages){
                            echo '<a > > </a>';
                        }else{
                            $next_page = $_GET['page-nr'] + 1;
                            echo '<a href="?page-nr='.$next_page.'"> > </a>';
                        }
                    }
                    echo '</div>';
            
                echo '<div>';
                    echo '<a href="?page-nr='.$pages.'"> >> </a>';
                echo '</div>';
            echo '</div>';
            // --- Pagination End ---
        ?>
    </section>
</div>
</main>


    



<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dropdown logic
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

        // Scroll to top button
        const scrollTopBtn = document.getElementById("scrollTopBtn");
        scrollTopBtn.style.display = "flex";
        scrollTopBtn.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });

        // Review data
        const data = [
            { star: 5, count: <?php echo $star5_num; ?> },
            { star: 4, count: <?php echo $star4_num; ?> },
            { star: 3, count: <?php echo $star3_num; ?> },
            { star: 2, count: <?php echo $star2_num; ?> },
            { star: 1, count: <?php echo $star1_num; ?> }
        ];

        let total_rating = 0;
        let rating_based_on_stars = 0;

        data.forEach(rating => {
            total_rating += rating.count;
            rating_based_on_stars += rating.count * rating.star;
        });

        const average = (rating_based_on_stars / total_rating).toFixed(1);
        document.querySelector('.rating-number h1').textContent = average;
        document.querySelector('.rating-number p').textContent = `${total_rating.toLocaleString()} reviews`;
        document.querySelector('.star-fill').style.width = (average / 5) * 100 + "%";

        // Render rating progress bars
        const allRatingDiv = document.querySelector('.all-rating');
        data.forEach(rating => {
            const percent = ((rating.count / total_rating) * 100).toFixed(1);
            const ratingProgress = `
                <div class="rating-progress-bar">
                    <div class="star-num">${rating.star}</div>
                    <div class="progress">
                        <div class="bar" style="width: ${percent}%;"></div>
                    </div>
                </div>
            `;
            allRatingDiv.innerHTML += ratingProgress;
        });

        // Fix star fill in each user review row
        document.querySelectorAll('.user-star-rating-div').forEach(div => {
            const rating = parseFloat(div.dataset.rating);
            const widthPercent = (rating / 5) * 100;
            const starFill = div.querySelector('.user-star-rating');
            if (starFill) {
                starFill.style.width = widthPercent + '%';
            }
        });

        // Handle reply form submission
        document.querySelectorAll('.reply-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const reviewId = this.getAttribute('data-review-id');
                const replyText = this.querySelector('input[name="reply_text"]').value.trim();

                if (!replyText) {
                    alert('Please enter a reply');
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(`review_id=${encodeURIComponent(reviewId)}&reply_text=${encodeURIComponent(replyText)}`);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        location.reload();
                    } else {
                        alert('Error saving reply');
                    }
                };
            });
        });

        // Pagination style (optional fix)
        const links = document.querySelectorAll('.page-numbers > a');
        const bodyIdElement = document.querySelector('.page-id');
        const bodyId = bodyIdElement ? parseInt(bodyIdElement.value) - 1 : 0;
        if (bodyId >= 0 && bodyId < links.length) {
            links.forEach(link => link.style.color = "rgb(212, 212, 212)");
        }
    });
</script>

    
</body>
</html>
