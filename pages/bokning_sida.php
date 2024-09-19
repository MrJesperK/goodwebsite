<?php
session_start();
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
    <title>Bokning</title>
     <!-- copy these to all files -->
   <link rel="stylesheet" href="Cart.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles/shop.css">
  <link rel="stylesheet" href="../styles/footer.css">
  <link rel="stylesheet" href="../styles/sidomeny.css">
  <link rel="stylesheet" href="../styles/bokning_sida.css">
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/bokning_sida.js"></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script> const bookedDates = <?php echo json_encode($bookedDates); ?>;</script>

  <!-- end of files to copy -->
    

</head>
<body>

<?php require '../components/header.php' ?>

<br>

<!-- Calendar Section -->
<div class="CAL">
    <div class="calendar">
        <div class="header">
            <button id="prevBtn"><i style="font-size: x-large;">&larr;</i></button>
            <div class="monthYear" id="monthYear"></div>
            <button id="nextBtn"><i style="font-size: x-large;">&rarr;</i></button>
        </div>
        <div id="error-message" style="color: red; text-align: center;"></div>
        <div style="color: red; text-align:center;">Green are for avaliable dates to book. Red is for already booked dates. Gray is dates that are in past</div>
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
    <div class="players-containers">
    <input type="number" name="players" id="amountplayers" placeholder="How many players? 1-8" required min="1" max="8" style="display:flex; margin:auto; width:340px;">
    </div>
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