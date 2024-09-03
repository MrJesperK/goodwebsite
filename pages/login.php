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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logga in!</title>
    <link rel="stylesheet" href="../styles/color.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/sidomeny.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<ul class="nav nav-underline bg-body-tertiary border-bottom justify-content-center">
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
        <a class="nav-link active" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
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

    <div class="w-25 m-auto border rounded border-black d-flex  justify-content-center mt-4 p-3">

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
    </form>
    </div>


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

    <script src="../scripts/sidomeny.js"></script>
    <script src="../scripts/createUser.js"></script>
</body>
</html>