<?php 
require '../db/dbconn.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the 'players' field is set
    if (isset($_POST['players'])) {
        // Sanitize and validate integer input
        $players = filter_var($_POST['players'], FILTER_VALIDATE_INT);
        if ($players !== false && $players >= 1 && $players <= 8) {
            // Input is valid, process it
            "Number of players: " . $players;
        } else {
            echo "Invalid number of players. Please enter a number between 1 and 8.";
        }
    } else {
        echo "No players data received.";
    }
}


$DateTimeData = implode(' ', $_POST);


$Split = explode(" ", $DateTimeData, 5);

$dateWork = $Split[0] . " " . $Split[1] . " " . $Split[2];
$timeWork = $Split[3];
$players = $Split[4];

$dateTimeObj = DateTime::createFromFormat('d F Y', $dateWork);
if ($dateTimeObj) {
    $formattedDateWork = $dateTimeObj->format('Y-m-d');  // Convert to 'Y-m-d'
}

  

if(isset($_SESSION['username'])){
    try {
        $fetchStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $fetchStmt->bindParam(":id", $_SESSION['userID'], PDO::PARAM_INT);
        $fetchStmt->execute();
        $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        $name = $user['name'];
        $email = $user['email'];
   
}  catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage() . "<br />";
    die();
    
}


if(isset($_POST['SubmitButton'])){
try{  
    if (empty($formattedDateWork) || empty($timeWork) || empty($players)) {
        die("Error: player is empty");
    }

    $inserStmt = $pdo->prepare('INSERT INTO bokningar(user_id, datum_, tid_, people) VALUES (:User, :Datum, :Tid, :People)');
    $inserStmt->bindParam(':User', $_SESSION['userID'], PDO::PARAM_INT);
    $inserStmt->bindParam(':Datum', $formattedDateWork);
    $inserStmt->bindParam(':Tid', $timeWork,PDO::PARAM_STR);
    $inserStmt->bindParam(':People', $players, PDO::PARAM_INT);
    $inserStmt->execute();


     header("location:user.php");
    } catch(PDOException $e) {
             echo 'Connection failed: '.$e->getMessage()."<br />";
        }
    }
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
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body  class="d-flex flex-column min-vh-100 m-0 p-0" >

<?php require '../components/header.php'?>

<div>
<div style="
margin:auto;
display:flex;
position:relative;
justify-content:center;
width:300px;
top: 120px; ">
    <h1 style="
    font-weight:300;">Bokning</h1>

</div>

<br>
    <p style="
    display:flex; 
    margin:auto; 
    width: 500px; 
    top:100px; 
    
    padding: 5px;
   
    position:relative; ">Vänligen bekräfta dina bokningsdetaljer nedan.
Genom att fortsätta godkänner du att det valda datumet och tiden är korrekta. Se till att alla detaljer är korrekta innan du skickar in din bokning. När bokningen väl är inskickad kan ändringar kanske inte göras. Om allt ser bra ut, klicka på 'Verifiera Bokning' för att slutföra din bokning.</p>
    
<div>
<button onclick="window.location.href='bokning_sida.php'" class="btn btn-danger"
style="
display:flex;
margin: auto; 
position:relative;
width:300px;
top:100px;
font-weight: 500;
justify-content:center;
">Ångra Bokning</button>

<div>
<div style="
  width: 100%;
  justify-content: center;
  margin: auto;
  margin-top: 100px;">
  
  <div 
  style="
  align-items: left;
  display: flex;
  font-weight: 300;
  justify-content: center;
  
  width: 400px;
  margin:auto;
 "
  
  >
  
  Datum: <?php echo $dateWork; ?>
  <br>
  Tid: <?php echo $timeWork;?> CET
  <br>
  Spelare: <?php echo $players; ?> Players
  <br>
  Användare: <?php echo $_SESSION['username']; ?>
  <br> Email: <?php echo $email; ?>
  </div>




</div>
</div>
<form method="POST">
    <input type="hidden" name="date" value="<?= $dateWork?>">
    <input type="hidden" name="time" value="<?= $timeWork?>">
    <input type="hidden" name="people" value="<?= $players?>">
<button name="SubmitButton" class="btn btn-success" type="submit" 
style="display: flex;
margin:auto;
width:300px;
font-weight: 500;
justify-content: center;
"> Verifiera Bokning </button>

</form>
</div>
</div>
<br> 
<br>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>