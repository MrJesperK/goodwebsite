<?php 
require "dbconn.php";

$data = file_get_contents('php://input');
$request = json_decode($data, true);

if ($request && isset($request['id'])) {
    $itemId = $request['id'];
    $userId = $_SESSION['userID'];

    try {
        // Fetch product details from products table
        $fetch = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $fetch->bindParam(':id', $itemId, PDO::PARAM_INT);
        $fetch->execute();
        $item = $fetch->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Check if the product is already in the user's cart
            $checkCart = $pdo->prepare("SELECT * FROM carts WHERE product_id = :id AND user_id = :user_id");
            $checkCart->bindParam(":id", $itemId, PDO::PARAM_INT);
            $checkCart->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $checkCart->execute();
            $cartItem = $checkCart->fetch(PDO::FETCH_ASSOC);

            if ($cartItem) {
                // If the item exists in the cart, increment the amount
                $updateCart = $pdo->prepare("UPDATE carts SET amount = amount + 1 WHERE product_id = :id AND user_id = :user_id");
                $updateCart->bindParam(":id", $itemId, PDO::PARAM_INT);
                $updateCart->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $updateCart->execute();
            } else {
                // If the item is not in the cart, add it
                $stmt = $pdo->prepare("INSERT INTO carts (product_id, user_id, created_at, amount) VALUES (:id, :user_id, now(), 1)");
                $stmt->bindParam(":id", $itemId, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $stmt->execute();
            }

            echo "<div class='bg-tertiary'>
            <h3>$item[name]</h3>
            <p>Quantity: $cartItem[amount]</p>
            <p>Price: " . $item['price'] * $cartItem['amount'] . "SEK</p>
        </div>";
        } else {
            echo "Item not found";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request data";
}
