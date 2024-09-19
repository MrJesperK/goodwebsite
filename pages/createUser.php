<?php
require '../db/dbconn.php';

if (isset($_POST['createUser'])) {

  $name = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);
  $email = htmlspecialchars($_POST['email']);

  try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $error = "Username or email is already in use";
        } else{
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          $createStmt = $pdo->prepare('INSERT INTO users (name, password, email) VALUES (:username, :userpassword, :useremail)');

          $createStmt->bindParam(':username', $name, PDO::PARAM_STR);
          $createStmt->bindParam(':userpassword', $hashedPassword, PDO::PARAM_STR);
          $createStmt->bindParam(':useremail', $email, PDO::PARAM_STR);
          $createStmt->execute();

          header('Location: login.php');

}
  } catch(PDOException $e) {
    echo 'Connection failed: '.$e->getMessage()."<br />";
}
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
      <li class="nav-item">
        <a class="nav-link active" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
      </li> 
</ul>

<div class="w-25 m-auto border rounded border-black d-flex  justify-content-center mt-4 p-3">

<form method="post" class="m-auto flex-column w-75">
    <div class="mb-3 w-75 m-auto">
    <input class="w-100" type="text" id="username" name="username" placeholder="Välj användarnamn">
    </div>

    <div class="mb-3 w-75 m-auto">
    <input class="w-100" type="email" id="email" name="email" placeholder="Välj email-konto">
    </div>

    <div class="mb-3 w-75 m-auto">
    <input class="w-100 me-3" type="password" id="password" name="password" placeholder="Välj lösenord">    
    </div>

    <div class="mb-3 w-75 text-center m-auto">
    <label for="showPass">Visa lösenord: </label>
    <input type="checkbox" name="showPass" id="showPass" onclick="showPassword()"> 
    </div>

    <input type="submit" name="createUser" value="Registrera användare" class="w-100 m-auto text-center btn btn-primary">
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