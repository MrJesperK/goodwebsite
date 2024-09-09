<?php 
require '../db/dbconn.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
    
//$stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = :id ORDER BY created_at");
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id");

//$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);

$stmt->execute();
$results_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['username'];?></title>
    <link rel="stylesheet" href="../styles/color.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/sidomeny.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../scripts/sidomeny.js" defer></script>
    <style>
        
        @media (max-width: 1100px) {
            .m{
                /*justify-content: center !IMPORTANT;*/
                text-align: center;
                flex-direction: column;
                margin: 0vh !IMPORTANT;
                
            }
            
            .d{
                border-right: 4px solid black;
                border-radius: 15px 15px 0 0 !IMPORTANT;
                width: 100vw !IMPORTANT;
            }
            
            .bordered-div{
                width: 100%;
                height: 100%;
                margin: 0vh !IMPORTANT;
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
            max-width: 800px;
            margin: 20px auto;
            border-collapse: separate; /* Allows rounded corners */
            border-spacing: 0; /* Ensures no spacing between cells */
            border-radius: 10px; /* Rounds the corners of the table */
            overflow: hidden; /* Clips the rounded corners */
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow */
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

    </style>

</head>
<body class="d-flex flex-column min-vh-100">
    
<ul class="nav nav-underline bg-body-tertiary border-bottom justify-content-center">

    <?php if (isset($_SESSION['username'])): ?>
    <li class="nav-item">
      <a href="user.php" class="nav-link active" style="color:black; font-size: 20px;">Mina sidor</a>
    </li>
    <?php endif; ?>

        <div id="main">
          <span class="hamburg" onclick="openNav()">&#9776;</span>
        </div>
  
        <div id="mySidenav" class="sidenav">
          <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
          <a href="#">About</a>
          <a href="#">Services</a>
          <a href="#">Clients</a>
          <a href="#">Contact</a>
        </div>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;" aria-current="page" href="index.php">Hem</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="shop.php">Webbshop</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="ban_sida.php">Karta</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="bokning_sida.php">Bokning</a>
      </li>     
      <?php if (!isset($_SESSION['username'])):?>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
      </li> 
      <?php endif;?>
      <?php if (isset($_SESSION['username'])):?>
      <li class="nav-item">
        <form method="post">
        <button type="submit" name="logout" class="nav-link" style="color:black; font-size: 20px;">Logga ut</button>
        </form>
      </li> 
      <?php endif;?>
</ul>
    
<main class="">
    <h1 class="text-center mt-3"> Välkommen <?php echo $_SESSION['username']?>!</h1>
    <div class="d-flex justify-content-center m" style="margin-right: 30vh;">
        <div class="mt-4">
            <div style="color: black; font-size: 20px;">Namn: <?php echo $_SESSION['username']; ?></div>
            <div style="color: black; font-size: 20px;">Email: <?php echo $_SESSION['email']; ?></div>
            <?php if (isset($_SESSION['admin'])): ?>
                <div style="color: black; font-size: 20px;">Admin</div>
            <?php endif;?>

        </div>    
        <div class="bordered-div w-50 d-flex d flex-column" style="height: 75vh;">
            <div class="mh-50 w-100">
                <h2>Beställningar</h2>
                <table class="modern-table">
                <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Pris</th>
                    <th>Antal</th>
                </tr>
                </thead>    
                        <?php foreach ($results_orders as $order): ?>
                            <tr index="<?php $order['id'] ?>">
                                <td><?php echo $order['name'] ?></td>
                                <td><?php echo $order['price'] ?> SEK</td>
                                <td><?php echo $order['description'] ?></td>
                            </tr>  
                        <?php endforeach; ?>
                    </table>
                </div>
            <div class="mh-50 w-100" style="border-top: 4px solid black; margin-top: 10vh;">
                <h2 >Bokningar</h2>
                <table class="modern-table" >
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Tid</th>
                    <th>Spelare</th>
                </tr>
                </thead>    
                        <?php foreach ($results_orders as $order): ?>
                            <tr index="<?php $order['id'] ?>">
                                <td><?php echo $order['name'] ?></td>
                                <td><?php echo $order['price'] ?> SEK</td>
                                <td><?php echo $order['description'] ?> SEK</td>
                            </tr>  
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    
</main>

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