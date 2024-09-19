<?php
require '../db/dbconn.php';

if (isset($_POST['logout'])) {
session_destroy();
header('Location: index.php');
exit();
}

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
    
    <!-- Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="crossorigin=""></script>
    
    <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-gesture-handling@latest/dist/leaflet-gesture-handling.min.css" type="text/css">

    <script src="https://unpkg.com/@raruto/leaflet-gesture-handling@latest/dist/leaflet-gesture-handling.min.js"></script>

    
    <style>
        #map { height: 350px; }
    </style>
    <!-- Map -->
    
    <title>Document</title>
    <link rel="stylesheet" href="Cart.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles/shop.css">
  <link rel="stylesheet" href="../styles/footer.css">
  <link rel="stylesheet" href="../styles/sidomeny.css">
  <link rel="stylesheet" href="../styles/color.css">
  <link rel="stylesheet" href="../styles/index.css">
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
</head>


  <body class="font-tests d-flex flex-column min-vh-100">
    <!--Navbar-->
    <?php require '../components/header.php' ?>
<br>
<div class="container-fluid w-25 h-auto m-auto p-4 border border-black welcome-div font-welcome rounded">
<h1 class="text-center m-auto">Kastmyrens diskare</h1>
<h4 class="text-center m-auto ">Sveriges bästa diskgolfarklubb</h4>
</div>

<div class="container-fluid w-50 h-auto m-auto p-4 border border-black welcome-div-smaller-devices font-welcome rounded">
    <h1 class="text-center m-auto fs-3">Kastmyrens diskare</h1>
    <h4 class="text-center m-auto fs-5 fw-normal">Sveriges bästa diskgolfarklubb</h4>
    </div>
<hr>
    <!--d-flex flex-row-->
    <div class="w-100 mb-5 images-backdrop p-2 info-bilder">
   <div class="ms-2 picture-box bg-transparent my-2">
       <div id="carouselExampleInterval" class="carousel slide shadow" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>
           <div class="carousel-inner">
             <div class="carousel-item active" data-bs-interval="5000">
               <img src="../images/Disc_golf.png" class="d-block w-100 showcase-images" alt="...">
             </div>
             <div class="carousel-item" data-bs-interval="5000">
               <img src="../images/EDGC_2007_hole18.png" class="d-block w-100 showcase-images" alt="...">
             </div>
             <div class="carousel-item" data-bs-interval="5000">
                <img src="../images/disc_golf_frisbee_frisbee_golf-557024.png" class="d-block w-100 showcase-images" alt="...">
              </div>
           </div>
           <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
             <span class="carousel-control-prev-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Previous</span>
           </button>
           <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
             <span class="carousel-control-next-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Next</span>
           </button>
         </div>
   </div>
    <!--w-25-->
   <div class="m-auto mb-5 p-3 font-intro text-box">
    <h2 class="text-center" style="color:whitesmoke;">Välkommen!</h2>
    <hr>
    <p class="fs-5" style="color:Whitesmoke;">
        Välkommen till kastmyrens diskare! Din plats för att njuta och ha kul med familjen.
        <br>
        Upplev spänningen med frisbeegolf på en av Sveriges bästa banor! Hos Kastmyrens Diskare erbjuder vi en unik kombination av naturupplevelser och sportig utmaning för spelare på alla nivåer. Oavsett om du är nybörjare eller en erfaren spelare, hittar du hos oss en gemenskap som brinner för frisbeegolf.
        <br>
    </p>
    <button class="btn float-end welcome-button border border-black" style="background-color: #A9B388;"><a href="bokning_sida.php" class="text-black text-decoration-none">Boka tid nu!</a></button>
  </div>
</div>
</body>
<div id="map"></div>
    
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
    
<script>
    
var map = L.map("map", {
        center: [60.67206949702055, 17.110107547842237],
        zoom: 13,
        gestureHandling: true
    });    
    
var marker = L.marker([60.67206949702055, 17.110107547842237]).addTo(map);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
  maxZoom: 19,
}).addTo(map);
  
marker.bindPopup("Kastmyrens Diskare").openPopup();
</script>
<script src="../scripts/sidomeny.js"></script>
</html>
