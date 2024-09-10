<?php 
require "dbconn.php";

$data = file_get_contents('php://input');
$request = json_decode($data, true);

if ($request && isset($request['id'])) {
    $itemId = $request['id'];
    $userId = $_SESSION['userID'];

    try {
        $fetch = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $fetch->bindParam(':id', $itemId, PDO::PARAM_INT);
        $fetch->execute();
        $item = $fetch->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("INSERT INTO carts (product_id, user_id, created_at, amount) VALUES (:id, :user_id, now(), 1)");
        $stmt->bindParam(":id", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();

        $thisID = $pdo->lastInsertId();
        $time = date("Y-m-d H:i:s");

        echo "<div class='bg-white'>
        <h3>$item[name]</h3>
         </div";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request data";
}
