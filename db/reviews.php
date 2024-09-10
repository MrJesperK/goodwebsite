<?php
require 'dbconn.php';

$data = file_get_contents('php://input');
$request = json_decode($data, true);

if ($request && isset($request['id']) && isset($request['text'])) {
    $itemId = $request['id'];
    $revText = $request['text'];
    $user = $_SESSION['username'];

    try {
        $stmt = $pdo->prepare('INSERT INTO reviews (created_at, product_id, user_id, stars, text) VALUES (now(), :product_id, :user_id, :stars, :text)');
        $stmt->bindParam(':product_id', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':text', $revText, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user, PDO::PARAM_INT);
        $stmt->execute();

        $thisID = $pdo->lastInsertId();
        $time = date('Y-m-d H:i:s');

        echo "<div> $request[text] </div>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request data";
}
    
