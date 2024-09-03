<?php
require 'dbconn.php';

if (isset($_POST['name'])) {
    $name = $_POST['name'];

    $stmt = $pdo->prepare('INSERT INTO users (name) VALUES (:name)');

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);

    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <input type="text" id="name" name="name">
        <input type="submit" id="submit" name="submit">
    </form>
</body>
</html>