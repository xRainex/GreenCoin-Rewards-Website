<?php 
    session_start();
    if (isset($_SESSION['user_id'])){

    }else{
        echo "<script>window.location.href='User-Login.php';</script>";
    }

    $user_id = $_SESSION["user_id"] ?? null;

    $conn = mysqli_connect("localhost", "root", "", "cp_assignment");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    if (isset($_POST['userId'])){
        

        $newUsername = $_POST['username'];
        $newPhoneNumber = $_POST['user-phoneNo'];
        $newAddress = $_POST['user-address'];
        $newDOB = $_POST['user-dob'];
        if (strpos($newDOB, '/') !== false) {
            $dateParts = explode('/', $newDOB);
            if (count($dateParts) === 3) {
                $newDOB = "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
            }
        }

        

        // Validate date format
        $dateRegex = "/^\d{4}-\d{2}-\d{2}$/";
        if (!preg_match($dateRegex, $newDOB)) {
            $newDOB = NULL;  // Prevent incorrect date storage
        }
        $newPW = $_POST['user-password'];
        $newProfileImg = $_POST['currentProfileImage'];
        if (trim($newPhoneNumber) == ""){
            if(trim($newAddress) == ""){
                $updateUserDetailsQuery = mysqli_query($conn, "UPDATE user SET username='$newUsername', 
                                            phone_number = NULL, address = NULL,
                                            dob = '$newDOB', password = '$newPW', profile_image = '$newProfileImg' 
                                            WHERE user_id = '$user_id'");
            }else{
                $updateUserDetailsQuery = mysqli_query($conn, "UPDATE user SET username='$newUsername', 
                                                phone_number = NULL, address = '$newAddress',
                                                dob = '$newDOB', password = '$newPW', profile_image = '$newProfileImg' 
                                                WHERE user_id = '$user_id'");
            }
        }else{
            if(trim($newAddress) == ""){
                $updateUserDetailsQuery = mysqli_query($conn, "UPDATE user SET username='$newUsername', 
                                                phone_number = '$newPhoneNumber', address = NULL,
                                                dob = '$newDOB', password = '$newPW', profile_image = '$newProfileImg' 
                                                WHERE user_id = '$user_id'");
            }else{
                $updateUserDetailsQuery = mysqli_query($conn, "UPDATE user SET username='$newUsername', 
                                                phone_number = '$newPhoneNumber', address = '$newAddress',
                                                dob = '$newDOB', password = '$newPW', profile_image = '$newProfileImg' 
                                                WHERE user_id = '$user_id'");
            }
        }
    }else if (isset($_POST['submitBtn'])){
        $star_given = $_POST['star-given'];
        $review_text = $_POST['review-text'];
        $pr_dr = $_POST['pr_dr_type'];
        $pr_dr_id = $_POST['pr_dr_id'];


        if ($pr_dr == "Dropoff"){
            $insertNewReviewQuery = "INSERT INTO review(dropoff_id, pickup_request_id, review, date, star) VALUES
                                    ('$pr_dr_id', NULL, '$review_text', NOW(), '$star_given')";    
        }else{
            $insertNewReviewQuery = "INSERT INTO review(dropoff_id, pickup_request_id, review, date, star) VALUES
                                    (NULL, '$pr_dr_id', '$review_text', NOW(), '$star_given')";    
        }
        $insertNewReview = mysqli_query($conn, $insertNewReviewQuery);
         
        $system_announcement = "Thank you for your feedback! 💬
        Your review has been successfully submitted.
        We truly appreciate you taking the time to share your experience — it helps us grow and serve you better. 🌟🌿";
        $requestSubmittedNotiQuery = "INSERT INTO user_notification(user_id, datetime, title, announcement, status) VALUES 
        ('$user_id', NOW(), 'Review Submitted! 📝', '$system_announcement', 'unread')";
        mysqli_query($conn, $requestSubmittedNotiQuery);

        $admin_announcement = "A user has share their recycling experience. Check the review section to view and respond.";
        $newRequestNotiQuery = "INSERT INTO admin_notification(user_id, datetime, title, announcement, status) VALUES 
        ('$user_id', NOW(), '📝 New User Review Received!', '$admin_announcement', 'unread')";
        mysqli_query($conn, $newRequestNotiQuery);
    }

    $unreadCount = 0; 

    if ($user_id) { 
        $unreadQuery = "SELECT COUNT(*) AS unread_count FROM user_notification WHERE user_id = '$user_id' AND status = 'unread'";
        $unreadResult = mysqli_query($conn, $unreadQuery);
        $unreadData = mysqli_fetch_assoc($unreadResult);
        $unreadCount = $unreadData['unread_count'];

        $userDetailQuery = mysqli_query($conn, "SELECT username, email, password, phone_number, dob, address, points, profile_image, created_at FROM user WHERE user_id = '$user_id'");
        $userDetailResult = mysqli_fetch_assoc($userDetailQuery);
        $username = $userDetailResult['username'];
        $userEmail = $userDetailResult['email'];
        $userPW = $userDetailResult['password'];
        $userPhoneNo = $userDetailResult['phone_number'];
        if ($userPhoneNo == "" || $userPhoneNo ==  "0"){
            $userPhoneNo = "-";
        }else{
            $userPhoneNo = "0".$userPhoneNo;
        }
        
        $userDOB = $userDetailResult['dob'];
        $userDOB = date_format(date_create($userDOB),'d-m-Y');

        $userAddress = $userDetailResult['address'];
        if ($userAddress == ""){
            $userAddress = "-";
        }
        $userPoints = $userDetailResult['points'];
        $userDateCreated = $userDetailResult['created_at'];
        $userDateCreated = date_format(date_create($userDateCreated),'d-m-Y');
        $userProfileImage = $userDetailResult['profile_image'];

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.css">
    <title>Profile - Green Coin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playpen+Sans:wght@100..800&display=swap');
        *{
            margin:0px;
            padding:0px;
            font-family:"Open Sans", sans-serif;
        }
        .material-icons{
            font-size: 30px;
        }

        header {
            position: sticky;
            z-index: 1000;
            top: 0;
            height: 73px;
            background-color:#78A24C;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .logo-container img {
            height: 40px;
            cursor: pointer;
        }

        .nav-links {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            transition: all 0.3 ease;
        }

        .nav-links a.active, .nav-links a:hover {
            color: white !important;
            cursor: pointer;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .noti-button, .profile-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            color: #ffffff;
            background: #78A24C;
            border: none;
            outline: none;
            border-radius: 50%;
            position: relative;
        }

        .noti-button:hover, .profile-button:hover {
            cursor: pointer;
        }

        .noti-button__badge {
            position: absolute;
            top: 5px;
            right: 0px;
            width: 20px;
            height: 20px;
            background: red;
            color: #ffffff;
            font-family: "Playpen Sans", cursive;
            display: <?php echo ($unreadCount > 0) ? 'flex' : 'none'; ?>;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
        }
        
        .page-div{
            display:flex;
            flex-direction:row;
        }

        .profile-container{
            width:25%;
            border:2px solid  #ebebeb;
            display:flex;
            flex-direction:column;
            padding:3.35vh 0px;
            margin:20px 0px 20px 60px;
            border-radius:25px;
            background-color: #ebebeb;
            position:fixed;
            height: 79vh;
        }
        
        .profile-div img{
            border-radius:50%;
            border:5px solid lightgrey;
            padding:10px;
            /* border: 5px solid rgb(210, 200, 186); */

        }

        .username{
            font-size:25px;
            font-weight:bold;
            text-align:center;
            margin:10px 0px 5px 0px;
            padding:10px 0px;
            width:100%;
            border:1px solid lightgrey;
            border-radius:20px;
        }

        .profile-div label{
            width:auto;
            font-size:16px;
        }

        .editProfile{
            background-color:rgb(116, 115, 114);
            border-radius:25px;
            font-size:16px;
            padding:10px 20px;
            margin:20px 0px;
            border:2px solid rgb(116, 115, 114);
            cursor:pointer;
            color:white;
            transition: all 0.3s ease;
        }

        .editProfile:hover{
            background-color: transparent;
            border:2px solid rgb(116, 115, 114);
            color:rgb(116, 115, 114);
        }

        .logout-btn{
            float:right;
            font-size:20px;
            margin-right:-20px;
        }

        .editProfile i {
            padding-right:8px;
        }

        .profile-details-div {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .input-container {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%; 
            max-width: 600px;
        }

        .profile-details-div label{
            color: grey;
        }

        .user-phoneNo, .user-dob, user-password{
            padding:10px 10px;
            margin:5px 0px 5px 5px;
            font-size:16px;
            border:1px solid lightgrey;
            border-radius:20px;
            flex: 1; 
        }

        .user-password{
            padding:10px 10px;
            margin:5px 0px 5px 5px;
            font-size:16px;
            border-radius:20px;
            border:1px solid lightgrey;
            flex: 1; 
        }

        .address-container {
            display: flex;
            gap: 10px; 
            margin-top:10px;
        }

        .user-address{
            padding:10px 10px;
            margin:0px 0px 0px 5px;
            font-size:16px;
            border-radius:20px;
            border:1px solid lightgrey;
            resize:none;
            width:100%;
            flex: 1; 
        }

        .profile-details-div span{
            display:inline-block;
            margin:20px 0px 20px 0px;
            font-size:16px;
        }

        .username:disabled{
            background-color:transparent;
            border-radius:0px;
            border:none;
            outline:none;
            color:black;
            padding:0px;
        }

        .user-phoneNo:disabled, .user-dob:disabled, .user-password:disabled{
            background-color:transparent;
            border-radius:0px;
            border:none;
            outline:none;
            padding:10px 0px;
            margin-left:0px;
            color:black;
            font-size:16px;
            width: 40%;
        }

        .user-email{
            background-color:transparent;
            border-radius:0px;
            border:none;
            outline:none;
            padding:10px 0px;
            color:black;
            font-size:16px;
            margin:10px 0px;
            width: 90%;
        }

        .user-joinedAt{
            background-color:transparent;
            border-radius:0px;
            border:none;
            outline:none;
            padding:10px 0px;
            color:black;
            font-size:16px;
            margin:10px 0px;
        }

        .user-password:disabled{
            background-color:transparent;
            border-radius:0px;
            border:none;
            outline:none;
            padding:10px 0px;
            margin-left:0px;
            color:black;
        }
        
        .disabledInput{
            cursor: not-allowed;
            border: 1px solid rgb(215, 215, 215);
            background-color:rgb(215, 215, 215);
            padding:10px 10px;
            margin:10px 0px 10px 5px;
            font-size:16px;
            border-radius:20px;
            flex: 1; 
        }

        .user-address:disabled{
            background-color:transparent;
            border:none;
            border-radius:0px;
            margin-left:0px;
            outline:none;
            padding:0px;
            color:black;
        }

        .change-profile{
            background-color:grey;
            border-radius:50%;
            font-size:14px;
            padding:8px;
            margin-top: -35px;
            margin-left:25px;
            position:absolute;
            z-index:20;
            display:none;
            cursor:pointer;
            color:white;
        }

        .chooseImageOverlay{
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.2); 
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000; 
            display: none;
        }

        .chooseImageContainer{
            width:600px;
            height: auto;
            border-radius:20px;
            position:relative;
            padding:20px;
            background-color:white;
            
        }

        .chooseImage{
            display:flex;
            flex-wrap:wrap;
            align-items: center;
            justify-content: center;
        }

        .chooseImage img{
            border-radius:50%;
            border:none;
            padding:10px;
        }

        .ImageContainer{
            cursor:pointer;
            margin:10px;
            border-radius:50%;
            border: 2px solid lightgrey;
        }

        .ImageSelected{
            border-radius:50%;
            border:2px solid #78a42c;
        }

        .chooseImgBtn{
            border-radius:20px;
            padding:15px;
            width:150px;
            color:white;
            cursor: pointer;
            margin-top:10px;
            background-color:#78a42c;
            border:none;
        }
        
        .logoutBtn{
            padding:10px 20px;
            border-radius:20px;
            border:2px solid grey;
            background-color:white;
            cursor: pointer;
        }

        .logoutBtn:hover{
            background-color:rgba(232, 218, 213, 0.99);
            border: 2px solid rgb(176, 121, 102);
            /* border: 2px solid rgba(232, 218, 213, 0.99); */

        }

        .logoutIcon{
            padding:0px;
            color:rgb(55, 55, 55);
            font-size:18px;
            text-align:center;
        }

        .logoutBtn:hover .logoutIcon{
            /* color:rgb(254, 253, 252); */
        }

        .showHidePw{
            display:none;
            cursor: pointer;
        }

        .phone-error-message, .pw-error-message, .date-error-message, .name-error-message{
            display: none;
            font-size: 14px;
            color:#f7656d;
            padding:10px 0px;
            text-align: left;
            font-family: "Open Sans", sans-serif;

        }

        .right-container{
            width:65%;
            margin:20px 60px 20px 0px;
            flex: 1;
            display:flex;
            flex-direction:column;
            margin-left: 32%;
            overflow-y: scroll;
            overflow-x:hidden;
            position: relative;
            background-color:#FAFAF6;
            border: 1px solid lightgrey;
            height: 86vh;
            border-radius: 25px;
        }
        
        .tab{
            position: fixed;
            width:61.05%;
            background-color: #FAFAF6;
            z-index:100;
            padding-top:20px;
            border-radius:25px;
            margin: 0px 20px;
        }

        .all-record{
            margin:80px 20px 0px 20px;
            width: 96%;
            overflow-y: auto;
            overflow-x: hidden;
            flex:1;
        }

        .tablink{
            background-color:transparent;
            border:none;
            font-size:18px;
            float:left;
            cursor: pointer;
            width: 50%;
            padding:10px 20px;
            font-weight:bold;
            font-family: "Playpen Sans", cursive;
            border-bottom: 3px solid lightgrey;
            color: lightgrey;
        }

        .selected-tab{
            color:rgb(158, 102, 19);
            border-bottom: 3px solid rgb(158, 102, 19);
        }

        hr{
            border: none;
            height: 1.5px;
            background-color: rgb(197, 197, 196);
            opacity: 1;
        }

        .record-row{
            width: 100%;
            gap:30px;
            display:flex;
            flex-direction:row;
            padding:10px 15px;
            margin:0px 10px;
            
        }

        .record-icon{
            width:5%;
            margin:auto 0px;
        }

        .record-icon img{
            vertical-align:middle;
        }

        .record-details{
            width: 70%;
        }

        .record-details h3{
            padding:5px 0px;
        }

        .date{
            font-size:12px;
            color:grey;
        }

        .item{
            font-size:14px;
            color:grey;
        }
        .record-points{
            width: 15%;
            display:flex;
            flex-direction:row;
        }

        .record-points-img{
            margin:auto 5px;
        }

        .record-points-img img{
            vertical-align:middle;
        }

        .record-points-p{
            font-size:18px;
            font-weight:bold;
            margin:auto 5px;
        }

        .reward-History{
            display:none;
            z-index:5;
        }

        .recycle-History{
            z-index:5;
        }

        .record-status{
            width: 10%;
            display:flex;
            flex-direction:column;
            margin:auto 0px;
        }

        .record-status-text{
            font-size: 18px;
            font-weight:bold;
            text-align:center;
        }

        .record-review-btn button{
            /*background-color: rgb(209, 137, 42);*/
            background-color:transparent;
            border-radius: 20px;
            border:2px solid #427c5d;
            text-align:center;
            padding:5px;
            width: 100%;
            color:#427c5d;
            margin-top:10px;
            font-size:16px;
        }

        .record-review-btn button:hover{
            background-color:rgba(188, 207, 196, 0.3);
        }

        
        .review-form-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-out;
        }

        .review-form-overlay.show {
            opacity: 1;
            display: block;
        }

        .review-form-overlay.hide {
            opacity: 0;
        }

        .review-form-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        .review-form-popup.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            visibility: visible;
        }

        .review-form-popup.hide {
            opacity: 0;
            transform: translate(-50%, -50%) scale(1); 
            transition: opacity 0.3s ease-out;
        }
        
        .close-container {
            text-align: center;
            margin: 20px 0 40px 40px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 25px;
            top: 0;
            color:rgb(40, 79, 12);
            font-size: 35px;
        }

        .close:hover, .close:focus {
            color:rgb(13, 84, 17);
            cursor: pointer;
        }

        .review-form-container {
            padding-left: 50px;
            padding-right: 50px;
            padding-bottom: 50px;
            padding-top: 25px;
        }

        .review-form-header h3 {
            font-size: 30px;
            line-height: 1.8;
        }

        .review-form-p p {
            line-height: 1.5;
            color:rgb(89, 89, 89);
        }

        .starSelected{
            color: #f8c455 !important;
        }

        .review-form-input-div label{
            color :rgb(89,89,89);
        }

        .review-form-input-div textarea{
            width: 94%;
            padding: 12px 16px;
            margin: 8px 0px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size:16px;
            outline:none;
            transition: all 0.3s ease-in-out;
            height: 180px;
            resize:none;
        }

        .review-form-star{
            margin:10px 0px;
        }

        .review-form-star i {
            color: lightgrey;
            font-size:30px;
        }
        
        .submitBtn{
            background: linear-gradient(135deg, rgb(78, 120, 49), rgb(56, 90, 35)); 
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            border-radius: 8px; 
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px; 
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); 
        }

        .submitBtn:hover{
            background: linear-gradient(135deg, rgb(78, 120, 49), rgb(56, 90, 35)); 
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px); 
        }

        .submitBtn:active{
            transform: scale(0.98);
        }
    
        .star-error-message, .review-text-error-message{
            color: #f7656d;
            font-size:14px;
            display:none;
        }

    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="User-Logo.png" onclick="window.location.href='User-Homepage.php'">
        </div>
        <ul class="nav-links">
            <li><a onclick="window.location.href='User-Homepage.php'">Home</a></li>
            <li><a onclick="window.location.href='User-Pickup Scheduling.php'">Pickup Scheduling</a></li>
            <li><a onclick="window.location.href='User-Drop-off Points.php'">Drop-off Points</a></li>
            <li><a onclick="window.location.href='User-Rewards.php'">Rewards</a></li>
            <li><a onclick="window.location.href='User-Review.php'">Review</a></li>
            <li><a onclick="window.location.href='User-FAQ.php'">FAQ</a></li>
        </ul>
        <div class="header-icons">
            <button class="noti-button" type="button" onclick="redirectToNotifications()">
                <span class="material-icons">mail</span>
                <?php if ($user_id && $unreadCount > 0) { ?>
                    <span class="noti-button__badge" id="notiBadge"><?php echo $unreadCount; ?></span>
                <?php } ?>
            </button>
            <button class="profile-button" type="button" onclick="window.location.href = 'User-Profile.php'">
                <span class="material-icons">account_circle</span>
            </button>
        </div>
    </header>
    <div class="page-div">
        <div class="profile-container">
            <div style="margin:auto; width:80%;">
                <form method="post" id="userDetailForm">
                    <div class="profile-div">
                        <center>
                            <div>
                                <input name="userId" type="text" hidden>
                                <center><img style="background-color:white; border-radius:50px;" id="currentProfileImg" src="<?php echo $userProfileImage;?>" width="90"></center>
                                <input type="hidden" id="currentProfileImageInput" name="currentProfileImage" value="<?php echo $userProfileImage;?>">
                                <center><span><i class="fa-solid fa-pen change-profile"></i></span></center>
                            </div>
                        </center>
                        <div class="chooseImageOverlay">
                            <div class="chooseImageContainer">
                                <div class="chooseImage">
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-1.png">
                                        <img src="User-Profile-Avatar-1.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-2.png">
                                        <img src="User-Profile-Avatar-2.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-3.png">
                                        <img src="User-Profile-Avatar-3.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-4.png">
                                        <img src="User-Profile-Avatar-4.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-5.png">
                                        <img src="User-Profile-Avatar-5.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-6.png">
                                        <img src="User-Profile-Avatar-6.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-7.png">
                                        <img src="User-Profile-Avatar-7.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-8.png">
                                        <img src="User-Profile-Avatar-8.png" width="75">
                                    </div>
                                    <div class="ImageContainer" data-value="User-Profile-Avatar-9.png">
                                        <img src="User-Profile-Avatar-9.png" width="75">
                                    </div>
                                </div>
                                <center><button type="button" class="chooseImgBtn">OK</button></center>
                            </div>
                            
                        </div>

                        <input type="text" class="username" name="username" value="<?php echo $username; ?>" ><br>
                        <p class="name-error-message">Please enter your name.</p>
                        <label class="points"><center><strong>Points: </strong><?php echo $userPoints; ?></center></label>
                        <center>
                            <button type="button" id="editProfile" class="editProfile" name="editProfile" value="0"><i id="edit-btn-icon"class="fa-solid fa-pen"></i>Edit Profile</button> 
                            <button type="button" class="logoutBtn" onclick="window.location.href = 'User-Logout.php'"><i class="fa-solid fa-right-from-bracket logoutIcon"></i></button> 
                        </center>
                    </div>
                    <br>
                    <div class="profile-details-div">
                        <div class="input-container">
                            <label>Email:</label>
                            <input type="text" class="user-email" value="<?php echo $userEmail;?>">
                        </div>

                        <div class="input-container">
                            <label style="white-space:nowrap;">Phone Number:</label>
                            <input type="text" class="user-phoneNo" name="user-phoneNo" value="<?php echo $userPhoneNo;?>">
                        </div>
                        <p class="phone-error-message">Please enter a valid phone number.</p>

                        <div class="input-container">
                            <label>Date of Birth:</label>
                            <input type="text" class="user-dob" onblur="(this.type='text')" name="user-dob" value="<?php echo htmlspecialchars($userDOB);?>">
                        </div>
                        <p class="date-error-message">Please choose your date of birth.</p>

                        <div class="input-container">
                            <label>Password:</label>
                            <input type="password" class="user-password" name="user-password" value="<?php echo $userPW;?>" >
                            <img src="User-HidePasswordIcon.png" width="20px" class="showHidePw">
                        </div>
                        <p class="pw-error-message">Please enter your password.</p>
                        

                        <div class="input-container">
                            <label>Joined At:</label>
                            <input type="text" class="user-joinedAt" value="<?php echo $userDateCreated;?>">
                        </div>

                        <div class="address-container">
                            <label>Address:</label>
                            <textarea class="user-address" name="user-address" ><?php echo $userAddress;?></textarea>
                        </div>
                    </div>
                   

                </form>
            </div>
        </div>
        <div class="right-container">
            <div class="tab">
                <button class="tablink selected-tab" id="defaultOpen">Points History</button>
                <button class="tablink">Recycle History</button>
            </div>
            <div class="reward-History">
                <div class="all-record">
                    <?php 
                        $getPointHistoryQuery = mysqli_query($conn, "SELECT dr.dropoff_id AS transaction_id, dr.dropoff_date AS transaction_date, 
                                                                        dr.total_point_earned AS points, '-' AS reward_id ,'Dropoff' AS transaction_type FROM dropoff dr 
                                                                        WHERE dr.user_id = '$user_id' AND dr.status = 'Complete'
                                                                        UNION ALL
                                                                        SELECT pr.pickup_request_id AS transaction_id, pr.datetime_submit_form AS transaction_date, 
                                                                        pr.total_point_earned AS points, '-' AS reward_id, 'Pickup' AS transaction_type FROM pickup_request pr 
                                                                        WHERE pr.user_id = '$user_id' AND pr.status = 'Completed'
                                                                        UNION ALL
                                                                        SELECT rr.redeem_reward_id AS transaction_id, rr.redeem_datetime AS transaction_date, 
                                                                        reward.point_needed AS points , rr.reward_id AS reward_id, 'Reward' AS transaction_type FROM redeem_reward rr 
                                                                        INNER JOIN reward ON rr.reward_id = reward.reward_id WHERE rr.user_id = '$user_id'
                                                                        ORDER BY transaction_date DESC;");

                        while ($getPointHistoryResult = mysqli_fetch_assoc($getPointHistoryQuery)){
                            $transaction_id = $getPointHistoryResult['transaction_id'];
                            echo '<div class="record-row">';
                                echo '<div class="record-icon">';
                                    if ($getPointHistoryResult['transaction_type'] == "Dropoff"){
                                        echo '<img src="User-Profile-History-DropoffIcon.png" width="50">';
                                    }else if ($getPointHistoryResult['transaction_type'] == "Pickup"){
                                        echo '<img src="User-Profile-History-PickupIcon.png" width="50">';
                                    }else if ($getPointHistoryResult['transaction_type'] == "Reward"){
                                        echo '<img src="User-Profile-History-RewardIcon.png" width="50">';
                                    }
                                    
                                echo '</div>';
                                echo '<div class="record-details">';
                                    echo '<p class="date">'.$getPointHistoryResult['transaction_date'].'</p>';
                                    echo '<h3>'.$getPointHistoryResult['transaction_type'].'</h3>';
                                    echo '<p class="item">';
                                    if ($getPointHistoryResult['transaction_type'] == "Dropoff"){
                                        $getItemQuery = mysqli_query($conn, "SELECT i.item_name AS item, idr.quantity AS quantity FROM item_dropoff idr 
                                                                                    INNER JOIN ITEM i ON idr.item_id = i.item_id WHERE 
                                                                                    idr.dropoff_id = '$transaction_id'");
                                    }else if ($getPointHistoryResult['transaction_type'] == "Pickup"){
                                        $getItemQuery = mysqli_query($conn, "SELECT i.item_name AS item, ipr.quantity AS quantity FROM item_pickup ipr 
                                                                                    INNER JOIN ITEM i ON ipr.item_id = i.item_id WHERE 
                                                                                    ipr.pickup_request_id = '$transaction_id'");
                                    }else if ($getPointHistoryResult['transaction_type'] == "Reward"){
                                        $getItemQuery = mysqli_query($conn, "SELECT reward_name as item, '1' AS quantity FROM reward WHERE reward_id = '$getPointHistoryResult[reward_id]'");
                                    }
                                    $count = 1;
                                    while ($getItemResult = mysqli_fetch_assoc($getItemQuery)){
                                        if ($count == 1){
                                            echo '<span>'.$getItemResult['item'].' (x'.$getItemResult['quantity'].')<span>';
                                        }else{
                                            echo '<span>, '.$getItemResult['item'].' (x'.$getItemResult['quantity'].')<span>';
                                        }
                                        $count += 1;
                                    }
                                    echo '</p>';
                                echo '</div>';
                                echo '<div class="record-points">';
                                    echo '<div class="record-points-img">';
                                        echo '<img src="User-Profile-History-CoinIcon.png" width="30">';
                                    echo '</div>';
                                    echo '<div class="record-points-p">';
                                        if ($getPointHistoryResult['transaction_type'] == "Dropoff" || $getPointHistoryResult['transaction_type'] == "Pickup"){
                                            echo '<p style="color:#427c5d;">+ '.$getPointHistoryResult['points'].'</p>';
                                        }else if ($getPointHistoryResult['transaction_type'] == "Reward"){
                                            echo '<p style="color:#fa5613;">- '.$getPointHistoryResult['points'].'</p>';
                                        }
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                            echo '<hr>';
                        }
                    ?>
                </div>
            </div>
            <?php 
                $getRecyleHistoryQuery = mysqli_query($conn, "SELECT dr.dropoff_id AS transaction_id, 
                                                            dr.dropoff_date AS transaction_date, dr.status AS status, 
                                                            dr.total_point_earned, 'Dropoff' AS transaction_type 
                                                            FROM dropoff dr WHERE dr.user_id = '$user_id' AND dr.status = 'Complete'
                                                            UNION ALL
                                                            SELECT pr.pickup_request_id AS transaction_id, 
                                                            pr.datetime_submit_form AS transaction_date, 
                                                            pr.status AS status, pr.total_point_earned, 'Pickup' AS transaction_type 
                                                            FROM pickup_request pr WHERE pr.user_id = '$user_id'
                                                            AND pr.status NOT IN ('Unread', 'Assigned')
                                                            ORDER BY transaction_date DESC;");
            ?>
            <div class="recycle-History">
                <div class="all-record">
                    <?php 
                        while ($getRecyleHistoryResult = mysqli_fetch_assoc($getRecyleHistoryQuery)){ 
                            $transaction_id = $getRecyleHistoryResult['transaction_id'];        
                            $transaction_type = $getRecyleHistoryResult['transaction_type'];              
                            echo '<div class="record-row">';
                                echo '<div class="record-icon">';
                                    if ($getRecyleHistoryResult['transaction_type'] == "Dropoff"){
                                        echo '<img src="User-Profile-History-DropoffIcon.png" width="50">';
                                    }else if ($getRecyleHistoryResult['transaction_type'] == "Pickup"){
                                        echo '<img src="User-Profile-History-PickupIcon.png" width="50">';
                                    }
                                echo '</div>';
                                echo '<div class="record-details">';
                                    echo '<p class="date">'.$getRecyleHistoryResult['transaction_date'].'</p>';
                                    echo '<h3>'.$getRecyleHistoryResult['transaction_type'].'</h3>';
                                    echo '<p class="item">';
                                    if ($getRecyleHistoryResult['transaction_type'] == "Dropoff"){
                                        $getRecyleItemQuery = mysqli_query($conn, "SELECT i.item_name AS item, idr.quantity AS quantity FROM item_dropoff idr 
                                                                                    INNER JOIN ITEM i ON idr.item_id = i.item_id WHERE 
                                                                                    idr.dropoff_id = $transaction_id");
                                    }else if ($getRecyleHistoryResult['transaction_type'] == "Pickup"){
                                        $getRecyleItemQuery = mysqli_query($conn, "SELECT i.item_name AS item, ipr.quantity AS quantity FROM item_pickup ipr 
                                                                                    INNER JOIN ITEM i ON ipr.item_id = i.item_id WHERE 
                                                                                    ipr.pickup_request_id = $transaction_id");
                                    }
                                    $count = 1;
                                    while ($getRecyleItemResult = mysqli_fetch_assoc($getRecyleItemQuery)){
                                        if ($count == 1){
                                            echo '<span>'.$getRecyleItemResult['item'].' (x'.$getRecyleItemResult['quantity'].')<span>';
                                        }else{
                                            echo '<span>, '.$getRecyleItemResult['item'].' (x'.$getRecyleItemResult['quantity'].')<span>';
                                        }
                                        $count += 1;
                                    }
                                    echo '</p>';
                                echo '</div>';

                                if($getRecyleHistoryResult['transaction_type'] == "Pickup"){
                                    $alrReviewQuery = mysqli_query($conn, "SELECT count(*) AS count FROM review where pickup_request_id = '$transaction_id'");
                                    $alrReviewResult = mysqli_fetch_assoc($alrReviewQuery)['count'];
                                    if ($alrReviewResult > 0){
                                        echo '<div class="record-status">';
                                            echo '<div class="record-status-text">';
                                                echo '<p style="color:#427c5d;">Rated</p>';
                                            echo '</div>';
                                        echo '</div>';
                                    }else if($getRecyleHistoryResult['status'] == "Completed"){
                                        echo '<div class="record-status">';
                                            echo '<div class="record-review-btn">';
                                                echo '<center><button onclick="showPopup(' . $transaction_id . ', \'' . $transaction_type . '\')">Rate</button></center>';
                                            echo '</div>';
                                        echo '</div>';
                                    }else if($getRecyleHistoryResult['status'] == "Rejected"){
                                        echo '<div class="record-status">';
                                            echo '<div class="record-status-text">';
                                                echo '<p style="color:rgb(209, 23, 5);">'.$getRecyleHistoryResult['status'].'</p>';
                                            echo '</div>';
                                        echo '</div>';
                                    }
                                }else if ($getRecyleHistoryResult['transaction_type'] == "Dropoff"){
                                    $alrReviewQuery = mysqli_query($conn, "SELECT count(*) AS count FROM review where dropoff_id = '$transaction_id'");
                                    $alrReviewResult = mysqli_fetch_assoc($alrReviewQuery)['count'];
                                    if ($alrReviewResult > 0){
                                        echo '<div class="record-status">';
                                            echo '<div class="record-status-text">';
                                                echo '<p style="color:#427c5d;">Rated</p>';
                                            echo '</div>';
                                        echo '</div>';
                                    }else{
                                        echo '<div class="record-status">';
                                            echo '<div class="record-review-btn">';
                                                    echo '<center><button onclick="showPopup(' . $transaction_id . ', \'' . $transaction_type . '\')">Rate</button></center>';
                                                echo '</div>';
                                        echo '</div>';
                                    }
                                }
                            echo '</div>';
                            echo '<hr>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>  
    
    <div class="review-form-overlay"></div>
    <div id="reviewFormDiv" class="modal">
        <form class="review-form-popup" method="post">
            <div class="close-container">
                <span class="close" id="closePopup">&times;</span>
            </div>
            <div class="review-form-container">
                <div class="review-form-header">
                    <h3>Review</h3>
                </div>
                <div class="review-form-p">
                    <p>Share your experience with us! Your feedback helps us improve and serve you better.</p>
                </div>
                <br><br>
                <div class="review-form-input-div">
                    <label>Rate</label>
                    <div class="review-form-star">
                        <i class="fa-solid fa-star star1" data-star="1" onclick="givenStar(1)"></i>
                        <i class="fa-solid fa-star star2" data-star="2" onclick="givenStar(2)"></i>
                        <i class="fa-solid fa-star star3" data-star="3" onclick="givenStar(3)"></i>
                        <i class="fa-solid fa-star star4" data-star="4" onclick="givenStar(4)"></i>
                        <i class="fa-solid fa-star star5" data-star="5" onclick="givenStar(5)"></i>
                    </div>
                    <p class="star-error-message">Please choose a rating from 1 to 5 stars before submitting.</p>
                    <br>
                    <label>Review</label><br>
                    <textarea name="review-text" id="review-text" placeholder="Share details of your own experience at this place"></textarea>
                    <p class="review-text-error-message">Please write a review before submitting.</p>
                </div>
                <input type="hidden" name="star-given" id="star-given">
                <input type="hidden" name="pr_dr_id" id="pr_dr_id">
                <input type="hidden" name="pr_dr_type" id="pr_dr_type">
                <br>
                <button class="submitBtn" type="submit" name="submitBtn">Submit</button>
            </div>
        </form>
    </div>


    <script>
        function redirectToNotifications() {
            fetch("User-Notification.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "check_login=true"
            })
            .then(response => response.text())
            .then(isLoggedIn => {
                console.log("Login Check Response:", isLoggedIn); // Debugging

                if (isLoggedIn.trim() === "true") {
                    window.location.href = 'User-Notification.php';
                } else {
                    window.location.href = 'User-Login.php'; 
                }
            })
            .catch(error => console.error("Error checking login:", error));
        }

        document.getElementById('editProfile').addEventListener('click', function () {
            const editBtn = document.getElementById('editProfile');
            const changeImage = document.getElementsByClassName('change-profile')[0];
            const form = document.getElementById('userDetailForm');
            const emailInput = document.getElementsByClassName('user-email')[0];
            const dateJoinedInput = document.getElementsByClassName('user-joinedAt')[0];

            if (editBtn.value == "0"){
                changeImage.style.display = "inline";
                const allInputs = document.querySelectorAll('.profile-container input');
                allInputs.forEach(input => input.disabled = false);

                emailInput.disabled = true;
                dateJoinedInput.disabled = true;
                emailInput.classList.add("disabledInput");
                dateJoinedInput.classList.add("disabledInput");

                const textarea = document.querySelector(".user-address");
                textarea.disabled = false;

                const addressLabel = document.querySelector(".address-container label");
                addressLabel.style.paddingTop = "10px";

                adjustHeight(textarea);
                editBtn.innerHTML = '<i class="fa fa-save"></i> Save';
                editBtn.value = "1";
                
            }else{
                let hasError = false;

                let dobInput = document.querySelector('.user-dob');

                if (dobInput.value.includes("-")) {  // Convert back to YYYY-MM-DD
                    let dateParts = dobInput.value.split("-");
                    if (dateParts.length === 3) {
                        dobInput.value = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                    }
                }

                const phoneInput = document.querySelector('.user-phoneNo');
                const phonePattern = /^01[0-46-9]\d{7,8}$/;
                if (phoneInput.value.trim() === "-" || phoneInput.value.trim() === "" || phoneInput.value.trim() == "0"){
                    phoneInput.value = "";
                    document.querySelector('.phone-error-message').style.display = "none";
                }else if(!phonePattern.test(phoneInput.value.trim())) {
                    document.querySelector('.phone-error-message').style.display = "block";
                    hasError = true;
                } else {
                    document.querySelector('.phone-error-message').style.display = "none";
                }

                const passwordInput = document.querySelector('.user-password');
                const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
                if(passwordInput.value.trim() === ""){
                    document.querySelector('.pw-error-message').textContent = "Please enter your password.";
                    document.querySelector('.pw-error-message').style.display = "block";
                    hasError = true;
                } else if(!passwordPattern.test(passwordInput.value.trim())) {
                    document.querySelector('.pw-error-message').textContent = "Password must have at least 8 characters, including an uppercase letter, a lowercase letter, and a number.";
                    document.querySelector('.pw-error-message').style.display = "block";
                    hasError = true;
                } else {
                    document.querySelector('.pw-error-message').style.display = "none";
                }

                const enteredDOB = new Date(dobInput.value);
                const today = new Date();
                const minDOB = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
                if (dobInput.value.trim() === "") {
                    document.querySelector('.date-error-message').textContent = "Please choose your date of birth.";
                    document.querySelector('.date-error-message').style.display = "block";
                    hasError = true;
                }else if (enteredDOB > today){
                    document.querySelector('.date-error-message').textContent = "You cannot choose a future date.";
                    document.querySelector('.date-error-message').style.display = "block";
                    hasError = true;
                }else if (enteredDOB > minDOB){
                    document.querySelector('.date-error-message').textContent = "You must be at least 18 years old.";
                    document.querySelector('.date-error-message').style.display = "block";
                    hasError = true;
                }else {
                    document.querySelector('.date-error-message').style.display = "none";
                }

                const usernameInput = document.querySelector('.username');
                if (usernameInput.value.trim() === ""){
                    document.querySelector('.name-error-message').style.display = "block"; 
                }else{
                    document.querySelector('.name-error-message').style.display = "none"; 
                }

                const address = document.querySelector(".user-address");
                if (address.value.trim() == "-" || address.value.trim() == ""){
                    address.value = "";
                }
                
                if (hasError) {
                    event.preventDefault();
                    return;
                }

                form.submit();

                allInputs.forEach(input => input.disabled = true);
                emailInput.disabled = true;
                dateJoinedInput.disabled = true;
                editBtn.innerHTML = '<i class="fa-solid fa-pen"></i> Edit Profile';
                editBtn.value = "0";
                changeImage.style.display = "none";

                const addressLabel = document.querySelector(".address-container label");
                addressLabel.style.paddingTop = "0px";

                const textarea = document.querySelector(".user-address");
                textarea.disabled = true;
                changeImage.style.display = "none";

                adjustHeight(textarea);
            }
        });

        function adjustHeight(el) {
            el.style.height = "auto"; 
            el.style.height = el.scrollHeight + "px"; 
        }

        document.addEventListener("DOMContentLoaded", function () {
            const textarea = document.querySelector(".user-address");

            if (textarea) {
                adjustHeight(textarea);

                textarea.addEventListener("input", function () {
                    adjustHeight(textarea);
                });
            }

            const allInputs = document.querySelectorAll('.profile-container input');
            allInputs.forEach(input => input.disabled = true);

            textarea.disabled = true;

            const dobInput = document.querySelector('.user-dob');

            dobInput.addEventListener("focus", function () {
                if (this.value.includes("-")) {  // If the value is already in DD-MM-YYYY format
                    let dateParts = this.value.split("-");
                    if (dateParts.length === 3) {
                        this.value = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // Convert to YYYY-MM-DD
                    }
                }
                this.type = "date";  // Ensure the input uses date picker
                this.max = new Date().toISOString().split("T")[0]; // Set max date to today
            });

            dobInput.addEventListener("blur", function () {
                if (this.value.includes("-")) {  // If the value is in YYYY-MM-DD format
                    let dateParts = this.value.split("-");
                    if (dateParts.length === 3) {
                        this.value = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // Convert back to DD-MM-YYYY
                    }
                }
                this.type = "text";  // Switch back to text display format
            });


            function convertToYYYYMMDD(dateString) {
                const parts = dateString.split("-");
                if (parts.length === 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`; 
                }
                return ""; 
            }

            function convertToDDMMYYYY(dateString) {
                const parts = dateString.split("-");
                if (parts.length === 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`; 
                }
                return "";
            }

            const password = document.getElementsByName('user-password')[0];
            const showHidePWIcon = document.getElementsByClassName("showHidePw")[0];
            password.onfocus = function(){
                showHidePWIcon.style.display = "inline";
                document.getElementsByClassName("showHidePw")[0].addEventListener("click",function(event){
                    if (password.type === "password"){
                        showHidePWIcon.src = "User-ViewPasswordIcon.png";
                        password.type = "text";
                    }else{
                        showHidePWIcon.src = "User-HidePasswordIcon.png";
                        password.type = "password";
                    }
                });
            }

            const recycleHistoryContent = document.querySelector('.recycle-History');
            const rewardHistoryContent = document.querySelector('.reward-History');
            const tabs = document.querySelectorAll('.tablink');

            if (!recycleHistoryContent || !rewardHistoryContent || tabs.length === 0) {
                console.error("One or more elements are missing.");
                return;
            }

            recycleHistoryContent.style.display = "none";
            rewardHistoryContent.style.display = "block";

            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => {

                    tabs.forEach(tab => tab.classList.remove('selected-tab'));

                    tab.classList.add('selected-tab');

                    if (index === 1) {
                        recycleHistoryContent.style.display = "block";
                        rewardHistoryContent.style.display = "none";
                    } else if (index === 0) {
                        recycleHistoryContent.style.display = "none";
                        rewardHistoryContent.style.display = "block";
                    }
                });
            });

            const openPopupBtn = document.getElementById("openPopup");
            const closePopupBtn = document.getElementById("closePopup");
            const modal = document.querySelector(".review-form-popup");
            const overlay = document.querySelector(".review-form-overlay");

            function closePopup() {
                modal.classList.remove("show");
                overlay.classList.remove("show");

                setTimeout(() => {
                    modal.classList.add("hide");
                    overlay.classList.add("hide");
                }, 300);

                setTimeout(() => {
                    modal.style.visibility = "hidden"; 
                    overlay.style.display = "none"; 
                    modal.classList.remove("hide"); 
                    overlay.classList.remove("hide");
                }, 350);
            }

            if (openPopupBtn) {
                openPopupBtn.addEventListener("click", redirectToForm);
            }

            if (closePopupBtn) {
                closePopupBtn.addEventListener("click", closePopup);
            }

            overlay.addEventListener("click", function (event) {
                if (event.target === overlay) {
                    closePopup();
                }
            });


        });

       
        let selectedImage = null;
        document.getElementsByClassName('change-profile')[0].addEventListener('click', function () {
            const chooseImage = document.getElementsByClassName('chooseImageOverlay')[0];
            const currentImageSrc = document.getElementById('currentProfileImg').src;
            const currentImageFile = currentImageSrc.substring(currentImageSrc.lastIndexOf('/') + 1);
            chooseImage.style.display = "flex";
            document.getElementsByClassName('right-container')[0].style.zIndex = "-1";
            const containers = document.querySelectorAll('.ImageContainer');
            
            containers.forEach(container => {
                console.log(currentImageFile);
                if (container.getAttribute('data-value') === (currentImageFile)){
                    container.classList.add('ImageSelected');
                }else{
                    container.classList.remove('ImageSelected');
                }
            });

            containers.forEach(container => {
                container.addEventListener('click', () => {
                    containers.forEach(div => div.classList.remove('ImageSelected'));
                    container.classList.add('ImageSelected');
                    selectedImage = container.getAttribute('data-value');
                });
            });
        });

        document.getElementsByClassName("chooseImgBtn")[0].addEventListener('click', function() {
            const chooseImage = document.getElementsByClassName('chooseImageOverlay')[0];
            const currentProfileImage = document.getElementById('currentProfileImg');
            const currentProfileImageInput = document.getElementById('currentProfileImageInput');
            
            if (selectedImage) { 
                currentProfileImage.src = selectedImage;
                currentProfileImageInput.value = selectedImage;
            }

            chooseImage.style.display = "none";
            document.getElementsByClassName('right-container')[0].style.zIndex = "1";

        });

        // right container
        document.getElementById("defaultOpen").click();   


        function showPopup(id,type) {
            const overlay = document.querySelector(".review-form-overlay");
            const modal = document.querySelector(".review-form-popup");
            const prOrDrID = document.getElementById("pr_dr_id");
            const prOrDrType = document.getElementById("pr_dr_type");

            if (!overlay || !modal) {
                console.error("Overlay or modal not found!");
                return;
            }

            overlay.style.display = "block"; 
            modal.style.visibility = "visible"; 
            prOrDrID.value = id;
            prOrDrType.value = type;

            setTimeout(() => {
                overlay.classList.add("show");
                modal.classList.add("show");
            }, 10);
        }

        function givenStar(star){
            const star1 = document.getElementsByClassName("star1")[0];
            const star2 = document.getElementsByClassName("star2")[0];
            const star3 = document.getElementsByClassName("star3")[0];
            const star4 = document.getElementsByClassName("star4")[0];
            const star5 = document.getElementsByClassName("star5")[0];
            const star_given = document.getElementById("star-given");

            if (star == 1){
                star1.classList.add("starSelected");
            }else if (star == 2){
                star1.classList.add("starSelected");
                star2.classList.add("starSelected");
            }else if (star == 3){
                star1.classList.add("starSelected");
                star2.classList.add("starSelected");
                star3.classList.add("starSelected");
            }else if (star == 4){
                star1.classList.add("starSelected");
                star2.classList.add("starSelected");
                star3.classList.add("starSelected");
                star4.classList.add("starSelected");
            }else if (star == 5){
                star1.classList.add("starSelected");
                star2.classList.add("starSelected");
                star3.classList.add("starSelected");
                star4.classList.add("starSelected");
                star5.classList.add("starSelected");
            }
            star_given.value = star;

        }

        const stars = document.querySelectorAll(".review-form-star i");

        stars.forEach(star => {
            star.addEventListener("mouseover", function () {
                let starValue = parseInt(this.getAttribute("data-star"));
                highlightStars(starValue);
            });

            star.addEventListener("mouseout", function () {
                resetStars();
            });

            star.addEventListener("click", function () {
                let starValue = parseInt(this.getAttribute("data-star"));
                selectStars(starValue);
            });
        });

        function highlightStars(starValue) {
            stars.forEach(star => {
                let value = parseInt(star.getAttribute("data-star"));
                if (value <= starValue) {
                    star.style.color = "#f8c455"; // Highlight on hover
                } else {
                    star.style.color = "lightgrey";
                }
            });
        }

        function resetStars() {
            stars.forEach(star => {
                if (!star.classList.contains("starSelected")) {
                    star.style.color = "lightgrey"; // Reset if not selected
                }
            });
        }

        function selectStars(starValue) {
            stars.forEach(star => {
                let value = parseInt(star.getAttribute("data-star"));
                if (value <= starValue) {
                    star.classList.add("starSelected");
                    star.style.color = "#f8c455";
                } else {
                    star.classList.remove("starSelected");
                    star.style.color = "lightgrey";
                }
            });
        }

        document.getElementsByClassName("review-form-popup")[0].addEventListener("submit", function(event) {
            const starGiven = document.getElementById("star-given");
            const reviewText = document.getElementById("review-text");
            const starError = document.getElementsByClassName("star-error-message")[0];
            const textError = document.getElementsByClassName("review-text-error-message")[0];
            let error = false;

            if (starGiven.value.trim() === "") {
                starError.style.display = "block";
                error = true;
            } else {
                starError.style.display = "none";
            }

            if (reviewText.value.trim() === "") {
                textError.style.display = "block";
                error = true;
            } else {
                textError.style.display = "none";
            }

            if (error) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>