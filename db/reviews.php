<?php
require 'dbconn.php';

header('Content-Type: application/json');

$data = file_get_contents('php://input');
$request = json_decode($data, true);

if ($request && isset($request['id']) && isset($request['text'])) {
    $itemId = filter_var($request['id'], FILTER_VALIDATE_INT);
    $revText = htmlspecialchars(trim($request['text']), ENT_QUOTES, 'UTF-8');
    $user = $_SESSION['userID'];
    $username = $_SESSION['username'];
    $stars = isset($request['stars']) ? intval($request['stars']) : NULL;

    try {
        $stmt = $pdo->prepare('INSERT INTO reviews (created_at, product_id, user_id, stars, text, username) VALUES (now(), :product_id, :user_id, :stars, :text, :username)');
        $stmt->bindParam(':product_id', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':text', $revText, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user, PDO::PARAM_INT);
        $stmt->bindParam(':stars', $stars, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $thisID = $pdo->lastInsertId();
        $time = date('Y-m-d H:i:s');

        echo " <div class='card w-75 m-auto mb-2' style='width: 18rem;'>
                          <div class='card-header'>
                            $username
                            <span class='float-end'>$time</span>
                          </div>
                          <ul class='list-group list-group-flush'>
                            <li class='list-group-item'>$revText</li>
                            <li class='list-group-item'>stars: $stars</li>
                          </ul>
                        </div>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request data";
}

