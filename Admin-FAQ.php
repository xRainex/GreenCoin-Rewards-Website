<?php
    session_start();

    //$admin_id = $_SESSION["admin_id"] ?? null;

    $con = mysqli_connect("localhost", "root", "", "cp_assignment");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $result = $con->query("SELECT `name` FROM admin LIMIT 1"); 
    $testAdmin = $result->fetch_assoc();

    // Fetch FAQ categories
    $categoryQuery = "SELECT DISTINCT category FROM faq";
    $categoryResult = mysqli_query($con, $categoryQuery);
    $categories = [];
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row['category'];
    }

    //  CRUD
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_faq'])) {
            $category = mysqli_real_escape_string($con, $_POST['faq_category']);
            $question = mysqli_real_escape_string($con, $_POST['faq_question']);
            $answer = mysqli_real_escape_string($con, $_POST['faq_answer']);

            $query = "INSERT INTO faq (question, answer, category) VALUES ('$question', '$answer', '$category')";
            $_SESSION['message'] = mysqli_query($con, $query) ? "FAQ added successfully" : "Error adding FAQ: " . mysqli_error($con);
        } elseif (isset($_POST['edit_faq'])) {
            $faq_id = (int)$_POST['faq_id'];
            $category = mysqli_real_escape_string($con, $_POST['faq_category']);
            $question = mysqli_real_escape_string($con, $_POST['faq_question']);
            $answer = mysqli_real_escape_string($con, $_POST['faq_answer']);

            $query = "UPDATE faq SET question='$question', answer='$answer', category='$category' WHERE faq_id=$faq_id";
            $_SESSION['message'] = mysqli_query($con, $query) ? "FAQ updated successfully" : "Error updating FAQ: " . mysqli_error($con);
        } elseif (isset($_POST['delete_faq_id'])) {
            $faq_id = (int)$_POST['delete_faq_id'];
            $query = "DELETE FROM faq WHERE faq_id=$faq_id";
            $_SESSION['message'] = mysqli_query($con, $query) ? "FAQ deleted successfully" : "Error deleting FAQ: " . mysqli_error($con);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Fetch data
    $faqQuery = "SELECT * FROM faq ORDER BY category, faq_id";
    $faqResult = mysqli_query($con, $faqQuery);
    $faqs = mysqli_fetch_all($faqResult, MYSQLI_ASSOC);

    // Fetch message before any echo or HTML
    $message = $_SESSION['message'] ?? null;
    $error = $_SESSION['error'] ?? null;

    unset($_SESSION['message'], $_SESSION['error']);

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
    <title>FAQ - Green Coin</title>  
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playpen+Sans:wght@100..800&display=swap');
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
        margin-left: 100px;
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

    .faq-container{
        flex: 1;
        width: 100%;
        padding: 50px;
        margin-left: 20px;
    }


    .faq-header{
        background-image: url('User-FAQ-Header-Stickman.svg');
        background-position: top center;
        background-repeat: no-repeat;
        background-size: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1vh 5vh 12vh;
        border-radius: 30px;
        max-height: 1000px;
        color: black;
        width: 100%;
    }

    .faq-header-title{
        font-size: 48px;
        font-family: "Playpen Sans", cursive;
        line-height: 1.7;
        letter-spacing: 3px;
    }

    .faq-header-desc{
        font-size: 15px;
        text-align: center;
        font-family: "Playpen Sans", cursive;
        letter-spacing: 1px;
    }

    .search{
        width: 100%;
        width: 600px;
        height: 50px;
        background-color: white;
        margin-top: 60px;
        border-radius: 30px;
        display: flex;
        justify-content: space-between;
        padding: 0px;
    }

    .search input{
        width: 80%;
        height: 50px;
        padding: 10px 30px;
        background: transparent;
        border: none;
        font-size: 15px;
        outline: none;
    }

    .search button{
        width: 100px;
        height: 40px;
        margin: 5px 5px;
        background-color:rgb(209, 137, 42);
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 14px;
    }

    .accordion-container{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-direction: row;
        padding: 30px 30px 60px 30px;
        gap: 30px;
    }

    .category-list{
        width: 200px;
        flex-shrink: 0; 
    }

    .category-list p{
        font-family: "Playpen Sans", cursive;
        font-weight: bold;
        color: black;
        font-size: 17px;
        line-height: 1.9;
    }

    .category-list a{
        font-family: "Playpen Sans", cursive;
        display: block;
        text-decoration: none;
        color: black;
        margin-bottom: 5px;
        position: relative;
        transition: 0.3 ease;
        font-size: 15px;
        color:rgb(76, 76, 76);
        line-height: 2.4;
    }

    .category-list a:before{
        content:'';
        height: 16px;
        width: 3px;
        position: absolute;
        top: 10px;
        left: -10px;
        background-color: green;
        transition: 0.3 ease;
        opacity: 0;
    }

    .category-list a:hover::before{
        opacity: 1;
    }

    .category-list a:hover{
        transform: translateX(-4px);
        color: green;
    }

    .category-list a.active{
        transform: translateX(-4px);
        color: green;
    }

    .category-list a.active::before {
        opacity: 1;
    }

    .accordion {
        flex:1;
        display: flex;
        flex-direction: column;
        width: 100%; 
        min-width:0;
    }

    .category-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px; 
        font-size: 23px;
        color: rgb(158, 102, 19); 
    }

    .category-title i {
        font-size: 30px;
        height: auto;
        color: rgb(180, 139, 78); 
    }

    .faq{
        margin-top: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgb(208, 208, 208);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .question{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        cursor: pointer;
    }

    .question h3{
        font-size: 18px;
        line-height: 2.0;
        flex: 1;
    }

    .answer{
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-in-out, opacity 0.5s ease-in-out;
    }

    .answer p{
        padding-top: 10px;
        line-height: 1.7;
        font-size: 15px;
    }

    .faq.active .answer{
        max-height: 500px;
        opacity: 1;
    }

    .faq.active .chevron {
        transform: rotate(180deg);
    }


    .faq i{
    font-size: 18px;
    line-height: 2.0;
    color: rgb(209, 137, 42);
    transition: transform 0.5s ease-in-out;
    }

    .faq .chevron {
        transition: transform 0.5s ease-in-out;
    }
    .faq.active .chevron {
        transform: rotate(180deg);
    }


    @keyframes fade {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to{
        opacity: 1;
        transform: translateY(0px);
    }
    }

    footer{
        background-color: rgb(226, 234, 210);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        width: 100%;
        height: 370px;
        padding: 3rem 1rem;
    }

    .footer-container{
        max-width: 1140px;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .footer-container p{
        font-family: "Playpen Sans", cursive;
        font-size: 14.5px;
        color:rgb(76, 76, 76);
        line-height: 1.5;
    }

    .row{
        display: flex;
        justify-content: space-between;
        padding-top: 20px;
        width: 100%;
        flex-wrap: wrap;
    }

    .col{
        min-width: 250px;
        color: black;
        padding: 0 2rem;
    }

    .col .footer-logo{
        width: 200px;
        margin-bottom: 25px;
        background-color: #78A24C;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
    }

    .col h3{
        font-family: "Playpen Sans", cursive;
        color: black;
        font-size: 17px;
        font-weight: 500;
        margin-bottom: 20px;
        position: relative;
    }

    .col h3::after{
        content:'';
        height: 3px;
        width: 0px;
        background-color:rgb(226, 186, 54);
        position: absolute;
        bottom: 0;
        left: 0;
        transition: 0.3s ease;
    }

    .col h3:hover::after{
        width: 30px;
    }

    .col .social a i{
        color: black;
        font-size: 23px;
        margin-top: 40px;
        margin-right: 15px;
        transition: 0.3 ease;
    }

    .col .social a i:hover{
        transform: scale(1.5);
        filter: grayscale(25);
    }

    .col .footer-nav a{
        font-family: "Playpen Sans", cursive;
        display: block;
        text-decoration: none;
        color: black;
        margin-bottom: 5px;
        position: relative;
        transition: 0.3 ease;
        font-size: 14.5px;
        color:rgb(76, 76, 76);
        line-height: 1.9;
        cursor: pointer;
    }

    .col .footer-nav a:before{
        content:'';
        height: 16px;
        width: 3px;
        position: absolute;
        top: 5px;
        left: -10px;
        background-color: green;
        transition: 0.3 ease;
        opacity: 0;
    }

    .col .footer-nav a:hover::before{
    opacity: 1;
    }

    .col .footer-nav a:hover{
    transform: translateX(-4px);
    color: green;
    }

    .col .contact-details{
        font-family: "Playpen Sans", cursive;
        display: flex;
        justify-content: column;
        align-items: flex-start;
        gap: 10px;
    }

    .col .contact-details i{
        margin-right: 10px;
        margin-top: 2px;
        font-size: 16px; 
    }

    .faq-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .edit-btn, .delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        transition: all 0.3s ease;
    }

    .edit-btn {
        color: #3498db;
    }

    .delete-btn {
        color: #e74c3c;
    }

    .edit-btn:hover {
        color: #2980b9;
        transform: scale(1.1);
    }

    .delete-btn:hover {
        color: #c0392b;
        transform: scale(1.1);
    }

    .modal {
        display: none; 
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5); 
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {opacity: 0}
        to {opacity: 1}
    }

    .modal-content {
        background-color: #fefefe;
        margin: 8% auto;
        padding: 30px 40px;
        border: 1px solid #ccc;
        width: 500px;
        max-width: 90%;
        border-radius: 12px;
        animation: floatIn 0.4s ease;
        font-family: "Playpen Sans", cursive;
    }

    .modal-content h1 {
        font-size: 22px;
        margin-bottom: 10px;
        text-align: center;
        color: #2e7d32;
    }

    .modal-content p {
        font-size: 14px;
        color: #444;
        margin-bottom: 15px;
        text-align: center;
    }

    .modal-content label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .modal-content input[type="text"],
    .modal-content textarea,
    .modal-content select {
        width: 100%;
        padding: 10px 15px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .addformbtn {
        width: 100%;
        background-color: #2e7d32;
        color: white;
        padding: 12px;
        font-size: 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .addformbtn:hover {
        background-color: #1b5e20;
    }

    /* Close button */
    .close {
        position: absolute;
        top: 8px;
        right: 20px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
    }

    .category-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 25px;
        font-size: 23px;
        color: rgb(158, 102, 19);
        flex-wrap: wrap;
    }


    .toast-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #28a745; /* default green */
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        font-weight: bold;
        z-index: 9999;
        animation: toastFadeOut 4s forwards;
    }

    .alert-error {
        background-color: #dc3545;
    }

    @keyframes toastFadeOut {
        0% { opacity: 1; }
        80% { opacity: 1; }
        100% { opacity: 0; transform: translateY(-20px); }
    }


</style>    

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

    function openEditFaqModal(id, question, answer, category) {
        document.getElementById('edit_faq_id').value = id;
        document.getElementById('edit_faq_question').value = question;
        document.getElementById('edit_faq_answer').value = answer;
        document.getElementById('edit_faq_category').value = category;
        document.getElementById('editFAQ').style.display = 'block';
    }

    function confirmDeleteFaq(id) {
        if (confirm("Are you sure you want to delete this FAQ?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_faq_id';
            input.value = id;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const categoryLinks = document.querySelectorAll(".category-link");
        const faqs = document.querySelectorAll(".faq");
        const categoryTitle = document.getElementById("category-name");
        const categoryIcon = document.getElementById("category-icon");

        const categoryData = {
            "All": { title: "All", iconClass: "fa-layer-group" },
            "General": { title: "General", iconClass: "fa-book-open" },
            "Pickup Scheduling": { title: "Pickup Scheduling", iconClass: "fa-truck-moving" },
            "Drop-off Points": { title: "Drop-off Points", iconClass: "fa-map-location-dot" }, 
            "Rewards": { title: "Rewards", iconClass: "fa-gift" }
        };

        categoryLinks.forEach(link => {
            link.addEventListener("click", function (event) {
                event.preventDefault();
                const selectedCategory = this.getAttribute("data-category");

                categoryLinks.forEach(link => link.classList.remove("active"));
                this.classList.add("active");

                if (categoryData[selectedCategory]) {
                    categoryTitle.textContent = categoryData[selectedCategory].title;
                    categoryIcon.className = `fa-solid ${categoryData[selectedCategory].iconClass}`;
                }

                faqs.forEach(faq => {
                    if (selectedCategory === "All" || faq.getAttribute("data-category") === selectedCategory) {
                        faq.style.display = "block";
                    } else {
                        faq.style.display = "none";
                    }
                });
            });
        });

        // Handle toggle while ignoring clicks on buttons
        document.querySelectorAll(".faq .question").forEach(question => {
            question.addEventListener("click", function (e) {
                if (e.target.closest("button")) return; // Prevent toggle when clicking button

                const faq = this.parentElement;
                const answer = faq.querySelector(".answer");

                // Close all FAQs except the clicked one
                document.querySelectorAll(".faq").forEach(item => {
                    if (item !== faq) {
                        item.classList.remove("active");
                        item.querySelector(".answer").style.maxHeight = "0";
                        item.querySelector(".answer").style.opacity = "0";
                    }
                });

                // Toggle the clicked FAQ
                faq.classList.toggle("active");

                if (faq.classList.contains("active")) {
                    answer.style.maxHeight = answer.scrollHeight + "px";  // Adjust maxHeight based on content
                    answer.style.opacity = "1";  // Show the answer
                } else {
                    answer.style.maxHeight = "0";  // Collapse the answer
                    answer.style.opacity = "0";  // Hide the answer
                }
            });
        });


        categoryTitle.textContent = categoryData["All"].title;
        categoryIcon.className = `fa-solid ${categoryData["All"].iconClass}`;
    });

    function filterFAQ() {
        var input = document.getElementById("search_faq");
        var filter = input.value.toUpperCase();
        var faqs = document.querySelectorAll(".faq");

        document.getElementById("category-name").textContent = "All";
        document.getElementById("category-icon").className = "fa-solid fa-layer-group";

        document.querySelectorAll(".category-link").forEach(link => link.classList.remove("active"));
        document.querySelector('.category-link[data-category="All"]').classList.add("active");

        for (var i = 0; i < faqs.length; i++) {
            var question = faqs[i].querySelector(".question h3");
            if (question) {
                var txtValue = question.textContent || question.innerText;
                faqs[i].style.display = txtValue.toUpperCase().includes(filter) ? "block" : "none";
            }
        }
    }

    // Close edit modal
    function closeEditModal() {
        document.getElementById('editFAQ').style.display = 'none';
    }

    // Close if clicking outside the modal content
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('editFAQ');
        if (event.target === modal) {
            modal.style.display = 'none';
        }

        const addModal = document.getElementById('addFAQ');
        if (event.target === addModal) {
            addModal.style.display = 'none';
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const scrollTopBtn = document.getElementById("scrollTopBtn");

        scrollTopBtn.style.display = "flex";

        scrollTopBtn.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
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
                <li><a href="Admin-PickupAvailability.php"><i class="fa-regular fa-calendar-days"></i>Pickup Availability</a></li>
                <li><a href="#"><i class="fa-solid fa-address-book"></i>Drivers</a></li> 
                <li><a href="#"><i class="fa-solid fa-dolly"></i>Drop-Off Requests</a></li>               
                <li><a href="#"><i class="fa-solid fa-map-location-dot"></i>Drop-Off Points</a></li>
                <li><a href="Admin-RewardsItemsPage.php"><i class="fa-solid fa-gift"></i>Rewards</a></li>
                <li><a href="Admin-Review.php"><i class="fa-solid fa-comments"></i>Reviews</a></li>
                <li><a href="Admin-ReportsPage.php"><i class="fa-solid fa-scroll"></i>Reports</a></li>
                <li  class="active"><a href="Admin-FAQ.php"><i class="fa-solid fa-circle-question"></i>FAQ</a></li>
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


    <!-- Display alert -->
    <?php if ($message): ?>
        <div class="toast-alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="toast-alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

  



    <main class="main-content">
        <div class="faq-container">
            <header class="faq-header">
                <h1 class="faq-header-title">FAQ</h1>
                <p class="faq-header-desc">Frequently Asked Questions</p>
                <div class="search">
                    <input type="text" id="search_faq" onkeyup="filterFAQ()" placeholder="Search...">
                    <button>Search</button>
                </div>
            </header>

            <div class="accordion-container">
                <!--  For choosing categories -->
                <aside class="category-list">
                    <p>Table of Contents</p>
                    <br>
                    <a href="#" data-category="All" class="category-link active">All</a>
                    <a href="#" data-category="General" class="category-link">General</a>
                    <a href="#" data-category="Pickup Scheduling" class="category-link">Pickup Scheduling</a>
                    <a href="#" data-category="Drop-off Points" class="category-link">Drop-off Points</a>
                    <a href="#" data-category="Rewards" class="category-link">Rewards</a>
                </aside>

                <div class="accordion">
                    <section class="category-title">
                        <i id="category-icon" class="fa-solid fa-layer-group"></i>
                        <h2 id="category-name">All</h2>
                        <button 
                            onclick="document.getElementById('addFAQ').style.display='block'" 
                            class="addformbtn" 
                            style="width: 160px; margin-left: auto;">
                            + Add FAQ
                        </button>
                    </section>

                    <?php foreach ($faqs as $row): ?>
                        <section class="faq" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                            <div class="question">
                                <div style="flex: 1;">
                                    <h3><?php echo htmlspecialchars($row['question']); ?></h3>
                                </div>
                                <div class="faq-actions">
                                    <button class="edit-btn"
                                            onclick='event.stopPropagation(); openEditFaqModal(
                                                <?php echo (int)$row["faq_id"]; ?>,
                                                <?php echo json_encode($row["question"]); ?>,
                                                <?php echo json_encode($row["answer"]); ?>,
                                                <?php echo json_encode($row["category"]); ?>)'>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="delete-btn"
                                            onclick="event.stopPropagation(); confirmDeleteFaq(<?php echo (int)$row['faq_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <i class="fa-solid fa-chevron-down chevron"></i>
                                </div>
                            </div>
                            <div class="answer">
                                <p><?php echo htmlspecialchars($row['answer']); ?></p>
                            </div>
                        </section>
                    <?php endforeach; ?>


                </div>
            </div>
        </div>
    </main>
</div>

<!--add new FAQ-->
<div id="addFAQ" class="modal">
    <div class="modal-content">
        <span onclick="document.getElementById('addFAQ').style.display='none'" class="close" title="Close Modal">&times;</span>
        <form method="post" action="">
            <h1>Add New FAQ</h1>
            <p>Please fill in this form to add a new FAQ.</p><br>

            <label for="faq_category"><b>Category</b></label>
            <select id="faq_category" name="faq_category" required>
                <option value="" disabled selected>Select category</option>
                <option value="General">General</option>
                <option value="Pickup Scheduling">Pickup Scheduling</option>
                <option value="Drop-off Points">Drop-off Points</option>
                <option value="Rewards">Rewards</option>
            </select><br>

            <label for="faq_question"><b>Question</b></label>
            <input type="text" id="faq_question" name="faq_question" required><br>

            <label for="faq_answer"><b>Answer</b></label>
            <textarea id="faq_answer" name="faq_answer" required style="height:150px;"></textarea><br>

            <button class="addformbtn" type="submit" name="add_faq">Add FAQ</button>
        </form>
    </div>
</div>


<!--edit FAQ-->
<div id="editFAQ" class="modal">
    <div class="modal-content">
        <span onclick="closeEditModal()" class="close" title="Close Modal">&times;</span>
        <form method="post" action="">
            <h1>Edit FAQ</h1>
            <p>Please update the FAQ details below.</p><br>

            <label for="edit_faq_category"><b>Category</b></label>
            <select id="edit_faq_category" name="faq_category" required>
                <option value="General">General</option>
                <option value="Pickup Scheduling">Pickup Scheduling</option>
                <option value="Drop-off Points">Drop-off Points</option>
                <option value="Rewards">Rewards</option>
            </select><br>

            <label for="edit_faq_question"><b>Question</b></label>
            <input type="text" id="edit_faq_question" name="faq_question" required><br>

            <label for="edit_faq_answer"><b>Answer</b></label>
            <textarea id="edit_faq_answer" name="faq_answer" required style="height:150px;"></textarea><br>

            <input type="hidden" id="edit_faq_id" name="faq_id">

            <div style="display: flex; justify-content: space-between; gap: 10px;">
                <button type="button" onclick="closeEditModal()" class="addformbtn" style="background-color: #aaa;">Cancel</button>
                <button type="submit" name="edit_faq" class="addformbtn">Update FAQ</button>
            </div>
        </form>
    </div>
</div>    
</body>
</html>