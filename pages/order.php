<?php
require '../db/dbconn.php';

$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtdWFlanl6b2ZjbGNzeG1pbGhyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTcyNTI4MzIzOCwiZXhwIjoyMDQwODU5MjM4fQ.gO6JV_lsqSsu7GcPKCBXpkD5v_RPj9pxZto2JTm6u8M';

$supabaseUrl = 'https://emuaejyzofclcsxmilhr.supabase.co'; // Replace with your Supabase project URL
$bucketName = 'kastmyrensBilder'; // Your Supabase bucket name

$order = $_GET['order'];

$orderFetch = $pdo->prepare('SELECT * FROM orders WHERE order_id = :order');
$orderFetch->bindParam(':order', $order, PDO::PARAM_INT);
$orderFetch->execute();
$fullOrder = $orderFetch->fetchAll(PDO::FETCH_ASSOC);

if (!$fullOrder || count($fullOrder) === 0) {
    echo "<script>alert('Order not found'); history.go(-1);</script>";
    exit;
}

// Check if the session user matches the user ID from the first row of the order
if ($_SESSION['userID'] != $fullOrder[0]['user_id']) {
    echo "<script> history.go(-2)</script>";
    exit;
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
    <title><?php echo $fullOrder[0]['order_id'] ?></title>
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
<body class="d-flex flex-column min-vh-100">
    
<?php require '../components/header.php' ?>

    <div class="container w-50 border border-black rounded p-0 mt-4">
    <?php 
$totalItems = count($fullOrder); // Get the total number of items
$currentIndex = 0; // Initialize a counter

foreach ($fullOrder as $orderItem): 
    $itemID = $orderItem['item_id'];

    // Fetch the item details
    $itemFetch = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $itemFetch->bindParam(':id', $itemID, PDO::PARAM_INT);
    $itemFetch->execute();
    $item = $itemFetch->fetch(PDO::FETCH_ASSOC);

    $imageNames = explode(',', $item['imageurls']);

    $imageUrls = array_map(function ($imageName) use ($supabaseUrl, $bucketName) {
      return "$supabaseUrl/storage/v1/object/public/$bucketName/webbshop/$imageName";
    }, $imageNames);

    if ($item): // Check if the item was found
        $currentIndex++; // Increment the counter for each iteration

        // Determine the correct border-radius class
        $borderRadiusClass = '';
        if ($currentIndex == 1) {
            // First item - round top corners
            $borderRadiusClass = 'rounded-top';
        } elseif ($currentIndex == $totalItems) {
            // Last item - round bottom corners
            $borderRadiusClass = 'rounded-bottom';
        }
    ?>
        <button class="orderItem d-flex flex-row align-items-center justify-content-between w-100 <?php echo ($currentIndex < $totalItems) ? 'border-bottom border-black' : ''; ?> <?php echo $borderRadiusClass; ?>" style="height: 5rem;" data-bs-toggle="modal" data-bs-target="#itemModal_<?php echo $item['id'] ?>">
            <p class="mb-0 px-2"><?php echo $item['name']; ?> (id: <?php echo $item['id'] ?>)</p>
            <p class="mb-0">pris: <?php echo $orderItem['total_price'] ?> SEK</p>
            <p class="mb-0 px-2">antal: <?php echo $orderItem['item_amount'] ?> st</p>
        </button>

        <!-- item modal -->

        <div class="modal fade ms-0" id="itemModal_<?php echo $item['id'] ?>" tabindex="-1"
        aria-labelledby="exampleModalLabel<?php echo $item['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel<?php echo $item['id']; ?>"><?php echo $item['name']; ?>
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row m-0 p-0">
              <div class="w-50">
                <div class="ms-2 picture-box bg-transparent my-2">
                  <div id="carouselExampleInterval<?php echo $item['id']; ?>" class="carousel slide shadow"
                    data-bs-ride="carousel">
                    <div class="carousel-indicators">
                      <button type="button" data-bs-target="#carouselExampleInterval<?php echo $item['id']; ?>"
                        data-bs-slide-to="0" aria-current="true" class="active" aria-label="Slide 1"></button>
                      <?php for ($i = 1; $i != sizeof($imageUrls); $i++): ?>
                        <button type="button" data-bs-target="#carouselExampleInterval<?php echo $item['id']; ?>"
                          data-bs-slide-to="<?php echo $i ?>" aria-label="Slide <?php echo $i ?>"></button>
                      <?php endfor; ?>
                    </div>
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <img src="<?php echo $imageUrls['0'] ?>" class="d-block w-100 showcase-images" alt="...">
                      </div>
                      <?php for ($i = 1; $i != sizeof($imageUrls); $i++): ?>
                        <div class="carousel-item">
                          <img src="<?php echo $imageUrls[$i] ?>" class="d-block w-100 showcase-images" alt="...">
                        </div>
                      <?php endfor; ?>
                    </div>
                    <button class="carousel-control-prev" type="button"
                      data-bs-target="#carouselExampleInterval<?php echo $item['id']; ?>" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                      data-bs-target="#carouselExampleInterval<?php echo $item['id']; ?>" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
                  </div>
                </div>
              </div>

              <div class="col border-start">
                <h2 class="text-decoration-underline text-center"><?php echo $item['name'] ?></h2>
                <p class="fs-4 fw-semibold">
                  <?php echo $item['description'] ?>
                </p>
                <hr>
                

              </div>
              <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
       
              </div>
            </div>
          </div>
        </div>



      </div>

    <?php 
    endif;

endforeach; 
?>
</div>

<footer class="footer mt-auto">
        <div class="containerfooter">
            <div class="footer-section">
            <h4>Kontakta Oss</h4>
            <p><a href="mailto:Kastmyrens@diskare.com">Email: Kastmyrens@diskare.com</a></p>
                <p>Telefon: +123-456-7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Kastmyrens Diskare. All rights reserved.
        </div>
    </footer>
    
</body>
</html>