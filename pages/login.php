<?php 
require '../db/dbconn.php';

if (isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] == "POST") {

    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $email = htmlspecialchars($_POST["email"]);

    try {
        $loginStmt = $pdo->prepare("SELECT * FROM users WHERE name = :username AND email = :email");
        $loginStmt->bindParam(":username", $username, PDO::PARAM_STR);
        $loginStmt->bindParam(":email", $email, PDO::PARAM_STR);
        $loginStmt->execute();

        $user = $loginStmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                $_SESSION['username'] = $user['name'];
                $_SESSION['userID'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['admin'] = $user['isAdmin'];

                header('Location: index.php');
        } else {
            echo "Invalid username or password";
            die();
        }
    } elseif (!$user){
        echo "Invalid username or password";
        die();
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage() . "<br />";
    die();
}
}

if (isset($_POST['logout'])){
    session_destroy();
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
    <title>Logga in!</title>
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
<body class="d-flex flex-column min-vh-100">

<?php require '../components/header.php'; ?>
<style>
  @media only screen and (max-width: 768px){
    .phoneLogin {
      width: 80% !important;
    }
  }
</style>
    <div class="w-25 m-auto border rounded border-black d-flex  justify-content-center mt-4 p-3 phoneLogin">

    <form method="post" class="m-auto flex-column w-75">
        <div class="mb-3 w-75 m-auto">
        <input class="w-100" type="text" id="username" name="username" placeholder="Användarnamn">
        </div>

        <div class="mb-3 w-75 m-auto">
        <input class="w-100" type="email" id="email" name="email" placeholder="exempel@gmail.com">
        </div>

        <div class="mb-3 w-75 m-auto">
        <input class="w-100 me-3" type="password" id="password" name="password" placeholder="Lösenord123">    
        </div>

        <div class="mb-3 w-75 text-center m-auto">
        <label for="showPass">Visa lösenord: </label>
        <input type="checkbox" name="showPass" id="showPass" onclick="showPassword()"> 
        </div>

        <input type="submit" name="login" value="Logga in!" class="w-100 m-auto mb-3 text-center btn btn-primary">

        <a href="createUser.php">Har du inget konto?</a>
        <hr>
        <a href="#">Glömt lösenord?</a>
    </form>
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

    <script src="../scripts/sidomeny.js"></script>
    <script src="../scripts/createUser.js"></script>
</body>
</html>