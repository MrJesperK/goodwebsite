<?php 
require '../db/dbconn.php';

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

$bookedDates = [];
try {
    $stmt = $pdo->query("SELECT datum_ FROM bokningar");
    $bookedDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// copy the php code below to all pages aswell
if (isset($_SESSION['userID'])) {
    $cartStmt = $pdo->prepare('SELECT * FROM carts WHERE user_id = :user_id');
    $cartStmt->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_INT);
    $cartStmt->execute();
  
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (isset($_POST['updateCart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['userID'];
  
    $updateCartStmt = $pdo->prepare("UPDATE carts SET amount = :quantity WHERE user_id = :user_id AND product_id = :product_id");
    $updateCartStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $updateCartStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $updateCartStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $updateCartStmt->execute();
  }
  
  if (isset($_POST['checkout'])) {
    $user_id = $_SESSION['userID'];
  
    $checkOrderIdStmt = $pdo->prepare('SELECT order_id FROM orders ORDER BY id DESC LIMIT 1');
    $checkOrderIdStmt->execute();
  
    $orderExists = $checkOrderIdStmt->fetch(PDO::FETCH_ASSOC);
  
    if ($orderExists){
      $order_id = $orderExists['order_id'] + 1;
    }
  
    foreach ($cartItems as $item) {
      $product_id = $item['product_id'];
      $quantity = $item['amount']; 
      $fetchProdPrice = $pdo->prepare('SELECT price, stock FROM products WHERE id = :id');
      $fetchProdPrice->bindParam(':id', var: $product_id);
      $fetchProdPrice->execute();
      $priceAndStock = $fetchProdPrice->fetch(PDO::FETCH_ASSOC);  
      $price = $quantity * $priceAndStock['price']; 
  
   
  
    $insertOrderStmt = $pdo->prepare('INSERT INTO orders (created_at, user_id, total_price, item_amount, item_id, order_id) VALUES (now(), :user, :price, :amount, :item, :order_id)');
    $insertOrderStmt->bindParam(':item', $product_id, PDO::PARAM_INT);  
    $insertOrderStmt->bindParam(':amount', $quantity, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':price', $price, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $insertOrderStmt->execute();
  
    $newStock = $priceAndStock['stock'] - $quantity;
  
    $updateProductsStmt = $pdo->prepare('UPDATE products SET stock = :newStock WHERE id = :product_id');
    $updateProductsStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $updateProductsStmt->bindParam(':newStock', $newStock, PDO::PARAM_INT);
    $updateProductsStmt->execute();
    }
  
    $cartDeleteStmt = $pdo->prepare('DELETE FROM carts WHERE user_id = :user');
    $cartDeleteStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
    $cartDeleteStmt->execute();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }
  
  if (isset($_POST['deleteCart'])){
    $user_id = $_SESSION['userID'];
    $cartDeleteStmt = $pdo->prepare('DELETE FROM carts WHERE user_id = :user');
    $cartDeleteStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
    $cartDeleteStmt->execute();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Booking Form</title>
   
     <!-- copy these to all files -->
     <link rel="stylesheet" href="Cart.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <link rel="stylesheet" href="../styles/shop.css">
  <link rel="stylesheet" href="../styles/footer.css">
  <link rel="stylesheet" href="../styles/sidomeny.css">
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- end of files to copy -->
    <style>
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .flatpickr-day.booked {
            background-color: #f8d7da; /* Light red */
            pointer-events:none;
        }
        .flatpickr-day.available {
            background-color: #d4edda; /* Light green */
           
        }

        .flatpickr-day.available:hover {
            background-color: lightblue; /* Light green */
            transition: 0.6s ease-in-out;
           
        }

        .flatpickr-day.disabled {
        background-color: #d3d3d3; /* Light gray for past dates */
        color: #a9a9a9; /* Darker gray text */
        pointer-events: none;
    }

    #message{
        display:flex;
        text-align:center;
        margin:auto;
    }
    </style>
</head>

<body class="font-tests d-flex flex-column min-vh-100">
    <?php require '../components/header.php' ?>

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

<div id="message" class="mt-3"></div>
    <div class="form-container">
        <h1>Manuell Booking</h1>
        <form id="manualBookingForm" action="process_bokning.php" method="post">

            <div class="mb-3">
                <label for="datum" class="form-label">Select Date:</label>
                <input type="date" id="datum" name="datum" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="time" class="form-label">Select Time:</label>
                <select id="time" name="time" class="form-select" required>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>

            <div class="mb-3">
                <label for="players" class="form-label">Number of Players:</label>
                <input type="number" id="players" name="players" class="form-control" required min="1" max="8" placeholder="Hur många spelare?">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Submit Booking</button>
        </form>
    </div>

    <script>  
    
    function toggleNav() {
            document.getElementById("mySidenav").classList.toggle('open');
            document.getElementById("mySidenav").style.width = "20%";

        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove('open');
            document.getElementById("mySidenav").style.width = "0";
            document.body.style.backgroundColor = "white";
        }</script>
    <script>

       $(document).ready(function() {
    const bookedDates = <?php echo json_encode($bookedDates); ?>;

    flatpickr("#datum", {
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: [
            function(date) {
                return date < new Date();
            }
        ],
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const dateStr = dayElem.dateObj.toISOString().split('T')[0];
            if (bookedDates.includes(dateStr)) {
                dayElem.classList.add('booked');
            } else if (dateStr < new Date().toISOString().split('T')[0]) {
                dayElem.classList.add('disabled');
            } else {
                dayElem.classList.add('available');
            }
        }
    });

    let times = [];
    for (let i = 9; i < 18; i++) {
        let hour = i < 10 ? '0' + i : i;
        times.push(hour + ':00');
        times.push(hour + ':30');
    }
    times.forEach(time => {
        $("#time").append(`<option value="${time}">${time}</option>`);
    });

    $("#manualBookingForm").submit(function(event) {
        event.preventDefault();

        const formData = $(this).serializeArray();
        const data = {};

        formData.forEach(field => {
            data[field.name] = field.value;
        });

        $.ajax({
            url: 'process_bokning.php',
            method: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            data: $.param(data),
            success: function(response) {
                try {
                    const json = JSON.parse(response);
                    if (json.success) {
                        $("#message").html(`<div class="alert alert-success">Booking submitted successfully!</div>`);

                        setTimeout(function() {
                        window.location.href = "user.php"; // Replace with your desired page
                    }, 1000); // 1000 ms = 1 seconds

                    } else {
                        $("#message").html(`<div class="alert alert-danger">Error: ${json.message}</div>`);
                    }
                } catch (e) {
                    console.error("Parsing error:", e);
                    $("#message").html(`<div class="alert alert-danger">An unexpected error occurred.</div>`);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $("#message").html(`<div class="alert alert-danger">An error occurred while processing your request.</div>`);
            }
        });
    });
});
    </script>
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
</body>
</html>
