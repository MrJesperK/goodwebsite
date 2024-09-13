<?php
session_start();
require '../db/dbconn.php';

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bokning</title>
    <link rel="stylesheet" href="../styles/Cart.css">
    <link rel="stylesheet" href="../styles/bokning_sida.css">
    <link rel="stylesheet" href="../styles/sidomeny.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../scripts/bokning_sida.js"></script>

</head>
<body class="font-tests d-flex flex-column min-vh-100">

<ul class="nav nav-underline bg-body-tertiary border-bottom justify-content-center">

    <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;" href="index.php">Hem</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="shop.php">Webbshop</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="ban_sida.php">Karta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" style="color:black; font-size: 20px;" href="bokning_sida.php">Bokning</a>
    </li>
    <?php if (isset($_SESSION['username'])): ?>
        <li class="nav-item">
            <a href="user.php" class="nav-link" style="color:black; font-size: 20px;">Mina sidor</a>
        </li>
    <?php endif; ?>
    <?php if (!isset($_SESSION['username'])): ?>
        <li class="nav-item">
            <a class="nav-link" style="color:black; font-size: 20px;" href="login.php">Logga in</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <form method="post">
                <button type="submit" name="logout" class="nav-link" style="color:black; font-size: 20px;">Logga ut</button>
            </form>
        </li>
    <?php endif; ?>
</ul>

<!-- Hamburger Menu -->
<div id="main" style="margin-bottom: 100px;">
    <span class="hamburg" onclick="openNav()">&#9776;</span>
</div>

<!-- Side Navigation -->
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="bokning_sida.php">Bokning</a>
    <a href="user.php">Mina sidor</a>
    <a href="ban_sida.php">Karta</a>
</div>

<!-- Calendar Section -->
<div class="CAL">
    <div class="calendar">
        <div class="header">
            <button id="prevBtn"><i style="font-size: x-large;">&larr;</i></button>
            <div class="monthYear" id="monthYear"></div>
            <button id="nextBtn"><i style="font-size: x-large;">&rarr;</i></button>
        </div>
        <div class="days">
            <div class="day">mån</div>
            <div class="day">tis</div>
            <div class="day">ons</div>
            <div class="day">tor</div>
            <div class="day">fre</div>
            <div class="day">lör</div>
            <div class="day">sön</div>
        </div>
        <div class="dates" id="dates"></div>
    </div>

    <div class="D" id="D">
        <ul id="HJ" class="G" style="width: 100%;"></ul>
    </div>

    <!-- Booking Form -->
    <form action="validera_bokning.php" method="POST" onsubmit="return BtnClick();">
    <input type="hidden" name="date" id="bookingdate">
    <input type="hidden" name="time1" id="bookingtime">
    <input type="number" name="players" id="amountplayers" placeholder="How many players? 1-8" required min="1" max="8" style="display:flex; margin:auto; width:340px;">
    <button id="btn" type="submit" class="btn btn-success">Boka</button>
</form>
</div>

<!-- Footer -->
<footer class="footer mt-auto">
    <div class="containerfooter">
        <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: Kastmyrens@diskare.com</p>
            <p>Telefon: +123-456-7890</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2024 Kastmyrens Diskare. All rights reserved.
    </div>
</footer>

<script src="../scripts/bokning_sida.js"></script>
<script src="../scripts/sidomeny.js"></script>
</body>
</html>