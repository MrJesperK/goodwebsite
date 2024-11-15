<?php 
require '../db/dbconn.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
    
$stmt = $pdo->prepare("SELECT order_id, SUM(total_price) as total_price, SUM(item_amount) as item_amount FROM orders WHERE user_id = :id GROUP BY order_id ORDER BY order_id");

$stmt->bindParam(':id', $_SESSION['userID'], PDO::PARAM_INT);

$stmt->execute();
$results_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM bokningar WHERE user_id = :id ORDER BY datum_ DESC");
//$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id");

$stmt->bindParam(':id', $_SESSION['userID'], PDO::PARAM_INT);

$stmt->execute();
$results_bokningar = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalprice = 0;
$totalProducts = 0;

$currentDateTime = new DateTime();

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
//   no more than this
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['username'];?></title>

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
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- end of files to copy -->

    <style>
        
        @media (max-width: 1100px) {
            .m{
                justify-self: center !IMPORTANT;
                text-align: center;
                flex-direction: column;
                margin: 0vh !IMPORTANT;
                
            }
            
            .d{
                border-right: 4px solid black;
                border-radius: 15px 15px 0 0 !IMPORTANT;
                width: 100vw !IMPORTANT;
            }
            
            .l{
              margin: auto;
            }
            
            .bordered-div{
                width: 100%;
                height: 100%;
                margin: 0vh !IMPORTANT;
            }
            
            .modern-table{
                max-width: 100%;
            }
        }
        
        
        .phonenav{
            display: none;
        }
        

        @media screen and (max-width: 768px) {
        .notmobile {
            display: none;
        }
        .phonenav {
            display: block;
        }
    }
            
        .bordered-div {
            padding: 20px;
            border-top: 4px solid black; /* Top border */
            border-left: 4px solid black; /* Left border */
            border-radius: 15px 0 0 0; /* Only the top-left corner is rounded */
           /*  background-color:   Optional: background color */
            width: 300px; /* Optional: set width */
            margin: 20px; /* Optional: add margin */
        }


        .modern-table {
            width: 100%;
            border-collapse: collapse;
            
            max-width: 800px;
            margin: 0px auto;
            border-collapse: separate; /* Allows rounded corners */
            border-spacing: 0; /* Ensures no spacing between cells */
            border-radius: 10px; /* Rounds the corners of the table */
            overflow: hidden; 
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            
        }
        
        .j{
            max-height: 24vh;
            overflow-y: auto;
        }

        .modern-table thead {
            background-color: #A9B388; /* Header background color */
            color: white;
        }

        .modern-table th,
        .modern-table td {
            padding: 15px;
            text-align: left;
        }

        .modern-table th {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #ddd; /* Light separator line */
            transition: background-color 0.3s; /* Smooth transition on hover */
        }

        .modern-table tbody tr:hover {
            background-color: #F0F4D1; /* Light hover effect */
        }

        .modern-table tbody td {
            color: #333;
        }

        .modern-table tbody tr:last-child {
            border-bottom: none; /* Remove border from the last row */
        }
        
        .l{
               min-width: 33vh; 
               max-width: 34vh; 
            }
        
        .j::-webkit-scrollbar {
        width: 12px; /* Width of the scrollbar */
        }

        /* Scrollbar track */
        .j::-webkit-scrollbar-track {
            background: #f1f1f1; /* Background color of the scrollbar track */
            border-radius: 10px; /* Rounded corners for the scrollbar track */
        }

        /* Scrollbar thumb */
        .j::-webkit-scrollbar-thumb {
            background-color: #888; /* Color of the scrollbar thumb */
            border-radius: 10px; /* Rounded corners for the scrollbar thumb */
            border: 2px solid #f1f1f1; /* Border around the scrollbar thumb */
        }

        /* Scrollbar thumb on hover */
        .j::-webkit-scrollbar-thumb:hover {
            background-color: #555; /* Color of the scrollbar thumb on hover */
        }
        
        .header-table {
        border-collapse: collapse;
        width: 100%;
        }
        
        .content-table {
        border-collapse: collapse;
        width: 100%;
        }
        
        .modern-table th, .modern-table td {
        padding: 8px;
        text-align: left;
        white-space: nowrap; /* Prevents text from wrapping */
        }

        /* Ensure column widths are consistent between the header and content */
        .header-table th, .content-table td {
            width: 33%; /* Adjust if column widths are uneven */
        }
        
        .button {
          background-color: #5F6F52; /* Green background */
          color: white; /* White text */
          padding: 10px 30px; /* Some padding */
          border: none; /* Remove borders */
          border-radius: 30px; /* Rounded corners */
          cursor: pointer; /* Pointer/hand icon */
          font-size: 16px; /* Increase font size */
          transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transitions */
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow */
        }

        .button:hover {
          background-color: #45a049; /* Darker green on hover */
          transform: translateY(-2px); /* Slight lift on hover */
        }

        .button:active {
          transform: translateY(1px); /* Slight push down on click */
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Shadow adjustment on click */
        }
        
        .t{
            background-color: lightgray;
        }

    </style>

</head>
<body class="d-flex flex-column min-vh-100 font-tests">
    
<?php require '../components/header.php' ?>
    
<main class="">
    <h1 class="text-center mt-3"> Välkommen <?php echo $_SESSION['username']?>!</h1>
    <div class="d-flex justify-content-center m" style="margin-right: 30vh;">
        <div class="mt-4 l">
            <div style="color: black; font-size: 20px;">Namn: <?php echo $_SESSION['username']; ?></div>
            <div style="color: black; font-size: 20px;">Email: <?php echo $_SESSION['email']; ?></div>
            <?php if ($_SESSION['admin'] == TRUE): ?>
                <div style="color: black; font-size: 20px;">Admin</div>
            <?php endif;?>

        </div>    
        <div class="bordered-div w-50 d-flex d flex-column" style="height: 75vh;">
            <h2>Beställningar</h2>
            <table class="modern-table header-table">
                <thead>
                    <th>Order</th>
                    <th>Pris</th>
                    <th>Antal</th>
                </thead>
                </table> 
            <div class="mh-50 w-100" style="min-height: 25vh;"> 
                <div class="j">
                <table class="modern-table content-table">
                    <?php if(isset($results_orders)):?>
                        <?php foreach ($results_orders as $order): ?>
                            
                            <tr onclick="window.location='order.php?order=<?php echo $order['order_id'] ?>'">
                                
                                <td><?php echo $order['order_id'] ?></td>
                                <td><?php echo $order['total_price'] ?> SEK</td>
                                <td><?php echo $order['item_amount'] ?></td>
                                
                            </tr>
                        <?php $totalprice = $totalprice + $order['total_price']; $totalProducts = $totalProducts + $order['item_amount']?>
                        <?php endforeach; ?>  
                    <?php endif;?>
                    </table>
                    </div>
                <?php if(!empty($results_orders)): ?>
                    <table class="modern-table header-table">
                        <thead>
                            <th>Totalt</th>
                            <th><?php echo $totalprice ?> SEK</th>
                            <th> <?php echo $totalProducts ?></th>
                            
                        </thead>
                    </table> 
                <?php endif; ?>  
                </div>
            
            <div class="mh-50 w-100" style="border-top: 4px solid black; margin-top: 3vh;">
                <h2 >Bokningar</h2>
                <table class="modern-table header-table">
                <thead>
                    <th>Datum</th>
                    <th>Tid</th>
                    <th>Spelare</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                </table>
                <div class="j">
                <table class="modern-table content-table">
                    <?php if(isset($results_bokningar)):?>
                        <?php foreach ($results_bokningar as $bokningar): 
                        if (isset($_POST['delete_' . $bokningar['id']]) && $_SERVER['REQUEST_METHOD'] == "POST") {
                    $product_to_delete = $bokningar['id'];
                    $deleteStmt = $pdo->prepare('DELETE FROM bokningar where id = :id');
                    $deleteStmt->bindParam(':id', $product_to_delete);
                    $deleteStmt->execute();
                    echo "<script> window.location.reload(); </script>";
                    }
                    
                        $dateTime = "{$bokningar['datum_']} {$bokningar['tid_']}:00";
                        
                        $givenDateTime = new DateTime($dateTime);
                        ?>
                            <tr <?php if($givenDateTime < $currentDateTime){ 
                                echo'class="t"';
                                } ?>>
                                <td><?php echo $bokningar['datum_'] ?></td>
                                <td><?php echo $bokningar['tid_'] ?></td>
                                <td><?php echo $bokningar['people'] ?></td>
                                <?php if($givenDateTime > $currentDateTime):?>
                                <td>
                                 <button class="button" data-bs-toggle="modal" data-bs-target="#cancelBooking_<?php echo $bokningar['id'] ?>">Avboka</button>
                                </td>
                                <?php else: ?>
                                
                                <td></td>
                                
                                <?php endif;?>
                            </tr>  
                            <div class="modal fade" id="cancelBooking_<?php echo $bokningar['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Avboka denna bokning?</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                  <h3 class="m-auto"></h3>
                                    <div class="d-flex flex-column justify-content-center m-auto">
                                        <p>Datum: <?php  echo $bokningar['datum_'] ?></p>
                                        <p>Tid: <?php  echo $bokningar['tid_'] ?></p>
                                        <p>Spelare: <?php  echo $bokningar['people'] ?></p>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
                                    <form method="post" index="<?php echo $bokningar['id'] ?>">
                                    <button type="submit" name="delete_<?php echo $bokningar['id'] ?>" class="btn btn-danger">Avboka</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif;?>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    
</main>

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