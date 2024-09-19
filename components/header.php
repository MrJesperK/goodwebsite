<?php
require '../db/dbconn.php';


?>

<style>
        .nav-item{
        margin-top: 0.5rem !important;
    }

    

    @media screen and (max-width: 768px) {
    .notmobile {
        display: none;
    }
    .phonenav, .phonenavbox {
        display: block;
    }
    .phonenavbox{
        margin-right:.25rem !important ;
    }
    .nav{
        justify-content: flex-end !important;
    }
    .list-fix {
        height: 3rem !important;
    }
    .nav-link {
        margin-top: -.25rem !important;
    }
    .cartIcon{
        margin-right: 1rem !important;
    }

    }
</style>

<ul class="list-fix nav nav-underline bg-body-tertiary border-bottom">



    <div class="nav-link phonenavbox text-black">
          <span class="fw-bold fs-1" onclick="openNav()">&#9776;</span>
        </div>
  <!-- hamburger phone navbar -->
        <div id="mySidenav" class="sidenav">
          <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="phonenav nav nav-underline">
            <?php if (isset($_SESSION['username'])): ?>
    <li class="nav-item">
      <a href="user.php" class="btn nav-link" style="color:black; font-size: 20px;">Mina sidor</a>
    </li>
    <?php endif; ?>
          <li class="nav-item">
        <a class="btn nav-link" style="color:black; font-size: 20px;" aria-current="page" href="index.php">Hem</a>
      </li>
      <li class="nav-item">
        <a class="btn nav-link" style="color: black; font-size: 20px;" href="shop.php">Webbshop</a>
      <li class="nav-item">
        <a class="btn nav-link" style="color: black; font-size: 20px;" href="ban_sida.php">Karta</a>
      </li>
      <li class="nav-item">
        <a class="btn nav-link" style="color:black; font-size: 20px;"  href="bokning_sida.php">Bokning</a>
      </li>     
      <?php if (!isset($_SESSION['username'])):?>
      <li class="nav-item">
        <a class="btn nav-link" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
      </li> 
      <?php endif;?>
      <?php if (isset($_SESSION['username'])):?>
        <hr>
      <li class="nav-item">
        <form method="post">
        <button type="submit" name="logout" class="btn nav-link m-auto" style="color:black; font-size: 20px;">Logga ut</button>
        </form>
      </li> 
      <?php endif;?>
            </div>
        </div>

      <!-- end of phone nav -->
<?php if (isset($_SESSION['username'])): ?>
    <div class="nav-link fs-1 text-black cartIcon" id="cartMain">
      <span class="fa fa-shopping-cart" onclick="openCart()"></span>

    </div>

    <div id="myCart" class="sidenav">
      <a href="javascript:void(0)" class="closebtn" onclick="closeCart()">&times;</a>

      <h2 class="text-white text-center border-bottom border-white position-relative">CART</h2>

      <div id="cart" class="p-2">
    <?php foreach ($cartItems as $cartItem):
        $prodID = $cartItem['product_id'];
        $prodFetch = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $prodFetch->bindParam(':id', $prodID, PDO::PARAM_INT);
        $prodFetch->execute();
        $prod = $prodFetch->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="bg-body-transparent ms-2 mb-3 border border-black rounded p-2" id="<?php echo $cartItem['id'] ?>">
            <h3><?php echo $prod['name'] ?></h3>
            <form method="post">
            Mängd: 
    <input class="bg-transparent border-0" type="number" name="quantity" value="<?php echo $cartItem['amount']; ?>" min="1" max="<?php echo $prod['stock'] ?>">
   
    <input type="hidden" name="product_id" value="<?php echo $cartItem['product_id']; ?>">
    <button class="btn" style="background-color:#A9B388 ;" type="submit" name="updateCart">Update Quantity</button>
</form>

            <p>Price: <?php echo $prod['price'] * $cartItem['amount']; ?> SEK</p>
        </div>
    <?php endforeach; ?>
</div>


<hr>

<div id="cartTotal" class="cart-total text-white p-2">
    <strong>Total: </strong> 
    <span id="cartTotalPrice">
        <?php 
        $totalPrice = 0;
        foreach ($cartItems as $cartItem) {
            $prodID = $cartItem['product_id'];
            $prodFetch = $pdo->prepare('SELECT price FROM products WHERE id = :id');
            $prodFetch->bindParam(':id', $prodID, PDO::PARAM_INT);
            $prodFetch->execute();
            $prod = $prodFetch->fetch(PDO::FETCH_ASSOC);
            $totalPrice += $prod['price'] * $cartItem['amount'];
        }
        echo $totalPrice . " SEK"; 
        ?>
    </span>
</div>
      <form method="post" class="m-auto ms-2 mb-3" id="checkoutForm">
        <input type="hidden" name="cart" id="cartInput" value="">
        <button type="submit" name="checkout" class="btn w-75 m-auto" style="background-color:#A9B388 ;">Checkout</button>
      </form>

      <form method="post" class="m-auto ms-2" id="DeleteCart">
        <button type="submit" name="deleteCart" class="btn w-75 m-auto btn-danger">rensa varukorg</button>
      </form>
    </div>
    <?php endif; ?>
    <div class="notmobile nav nav-underline">
    <?php if (isset($_SESSION['username'])): ?>
      <li class="nav-item">
        <a href="user.php" class="nav-link" style="color:black; font-size: 20px;">Mina sidor</a>
      </li>
    <?php endif; ?>
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
      <a class="nav-link" style="color:black; font-size: 20px;" href="bokning_sida.php">Bokning</a>
    </li>
    <?php if (!isset($_SESSION['username'])): ?>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;" href="login.php">Logga in</a>
      </li>
    <?php endif; ?>
    <?php if (isset($_SESSION['username'])): ?>
      <li class="nav-item">
        <form method="post">
          <button type="submit" name="logout" class="nav-link" style="color:black; font-size: 20px;">Logga ut</button>
        </form>
      </li>
    <?php endif; ?>
    </div>
  </ul>