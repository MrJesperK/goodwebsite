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
                /*$updateCart = $pdo->prepare("UPDATE carts SET amount = amount + 1 WHERE product_id = :id AND user_id = :user_id");
                $updateCart->bindParam(":id", $itemId, PDO::PARAM_INT);
                $updateCart->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $updateCart->execute();
                // Update $cartItem to reflect the new amount
                $cartItem['amount']++;*/
            } else {
                // If the item is not in the cart, add it
                $stmt = $pdo->prepare("INSERT INTO carts (product_id, user_id, created_at, amount) VALUES (:id, :user_id, now(), 1)");
                $stmt->bindParam(":id", $itemId, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $stmt->execute();
                // Initialize $cartItem to reflect the new amount
                $cartItem = ['amount' => 1];
                
                echo "  <div class='bg-body-transparent ms-2 mb-3 border border-black rounded p-2'>
            <h3>{$item['name']}</h3>
            <form method='post'>
            Mängd: 
    <input class='bg-transparent border-0' type='number' name='quantity' value='{$cartItem['amount']}' min='1' max='{$item['stock']}'>
   
    <input type='hidden' name='product_id' value='{}'>
    <button class='btn' style='background-color:#A9B388 ;' type='submit' name='updateCart'>Update Quantity</button>
</form>

            <p>Price: " . $item['price'] * $cartItem['amount'] . "SEK</p>
        </div>";
            //$cartItem['product_id']
            }

         
            
            
    
        } else {
            echo "Item not found";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request data";
}
