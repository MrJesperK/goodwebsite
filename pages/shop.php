<?php
require '../db/dbconn.php';

$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtdWFlanl6b2ZjbGNzeG1pbGhyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTcyNTI4MzIzOCwiZXhwIjoyMDQwODU5MjM4fQ.gO6JV_lsqSsu7GcPKCBXpkD5v_RPj9pxZto2JTm6u8M';

$supabaseUrl = 'https://emuaejyzofclcsxmilhr.supabase.co'; // Replace with your Supabase project URL
$bucketName = 'kastmyrensBilder'; // Your Supabase bucket name

if (isset($_POST['logout'])) {
  session_destroy();
  header('Location: index.php');
  exit();
}

if (isset($_POST['createProduct'])) {

  $pictures = "";

  $files = $_FILES['images'];
  $end = end($files['name']);

  // Loop through each file and upload
  foreach ($files['name'] as $key => $name) {
    $fileTmpName = $files['tmp_name'][$key];
    $fileType = $files['type'][$key];

    // Open the file in binary mode
    $fileData = file_get_contents($fileTmpName);

    // File path in the bucket (you can modify the path as needed)
    $filePath = "webbshop/" . $name;

    if ($end != $name)
      $pictures = $pictures . $name . ",";
    else
      $pictures = $pictures . $name;

    // cURL setup to send the file to Supabase storage
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "$supabaseUrl/storage/v1/object/$bucketName/$filePath",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: ' . $fileType
      ],
      CURLOPT_POSTFIELDS => $fileData,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    if ($error) {
      echo "cURL Error #:" . $error . "<br>";
    }
  }




  $name = htmlspecialchars($_POST['name']);
  $price = htmlspecialchars($_POST['price']);
  $description = htmlspecialchars($_POST['desc']);
  $stock = htmlspecialchars($_POST['stock']);



  $createStmt = $pdo->prepare('INSERT INTO products (name, price, description, imageurls, stock) VALUES (:name, :price, :description, :pictures, :stock)');

  $createStmt->bindParam(':name', $name, PDO::PARAM_STR);
  $createStmt->bindParam(':price', $price, PDO::PARAM_STR);
  $createStmt->bindParam(':description', $description, PDO::PARAM_STR);
  $createStmt->bindParam(':pictures', $pictures, PDO::PARAM_STR);
  $createStmt->bindParam(':stock', $stock, PDO::PARAM_INT);
  $createStmt->execute();

  header("refresh: 0");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
  $search = '%' . $_POST['search'] . '%';
  $searchStmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :search ORDER BY id DESC");
  $searchStmt->bindParam(':search', $search, PDO::PARAM_STR);
  $searchStmt->execute();
  $results = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
} else {

  $stmt = $pdo->prepare("SELECT * FROM products ORDER BY ID DESC");

  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['userID'])) {
  $cartStmt = $pdo->prepare('SELECT * FROM carts WHERE user_id = :user_id');
  $cartStmt->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_INT);
  $cartStmt->execute();

  $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['updateCart'])) {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  $user_id = $_SESSION['userID'];

  $updateCartStmt = $pdo->prepare("UPDATE carts SET amount = :quantity WHERE user_id = :user_id AND product_id = :product_id");
  $updateCartStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $updateCartStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $updateCartStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
  $updateCartStmt->execute();
}

if (isset($_POST['checkout'])) {
  $user_id = $_SESSION['userID'];

  $checkOrderIdStmt = $pdo->prepare('SELECT order_id FROM orders ORDER BY id DESC LIMIT 1');
  $checkOrderIdStmt->execute();

  $orderExists = $checkOrderIdStmt->fetch(PDO::FETCH_ASSOC);

  if ($orderExists){
    $order_id = $orderExists['order_id'] + 1;
  }

  foreach ($cartItems as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['amount']; 
    $fetchProdPrice = $pdo->prepare('SELECT price, stock FROM products WHERE id = :id');
    $fetchProdPrice->bindParam(':id', var: $product_id);
    $fetchProdPrice->execute();
    $priceAndStock = $fetchProdPrice->fetch(PDO::FETCH_ASSOC);  
    $price = $quantity * $priceAndStock['price']; 

 

  $insertOrderStmt = $pdo->prepare('INSERT INTO orders (created_at, user_id, total_price, item_amount, item_id, order_id) VALUES (now(), :user, :price, :amount, :item, :order_id)');
  $insertOrderStmt->bindParam(':item', $product_id, PDO::PARAM_INT);  
  $insertOrderStmt->bindParam(':amount', $quantity, PDO::PARAM_INT);
  $insertOrderStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
  $insertOrderStmt->bindParam(':price', $price, PDO::PARAM_INT);
  $insertOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
  $insertOrderStmt->execute();

  $newStock = $priceAndStock['stock'] - $quantity;

  $updateProductsStmt = $pdo->prepare('UPDATE products SET stock = :newStock WHERE id = :product_id');
  $updateProductsStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
  $updateProductsStmt->bindParam(':newStock', $newStock, PDO::PARAM_INT);
  $updateProductsStmt->execute();
  }

  $cartDeleteStmt = $pdo->prepare('DELETE FROM carts WHERE user_id = :user');
  $cartDeleteStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
  $cartDeleteStmt->execute();
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

if (isset($_POST['deleteCart'])){
  $user_id = $_SESSION['userID'];
  $cartDeleteStmt = $pdo->prepare('DELETE FROM carts WHERE user_id = :user');
  $cartDeleteStmt->bindParam(':user', $user_id, PDO::PARAM_INT);
  $cartDeleteStmt->execute();
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

$lowestPriceStmt = $pdo->prepare('SELECT price FROM products ORDER BY price ASC');
$lowestPriceStmt->execute();
$lowestPrice = $lowestPriceStmt->fetch(PDO::FETCH_ASSOC);

$highestPriceStmt = $pdo->prepare('SELECT price FROM products ORDER BY price DESC');
$highestPriceStmt->execute();
$highestPrice = $highestPriceStmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['filterItems'])){
$priceLowest = $_POST['priceLowest'];
$priceHighest = $_POST['priceHighest'];

$filterStmt = $pdo->prepare('SELECT * FROM products WHERE price >= :lowestPrice AND price <= :highestPrice');
$filterStmt->bindParam(':lowestPrice', $priceLowest, PDO::PARAM_INT);
$filterStmt->bindParam(':highestPrice', $priceHighest, PDO::PARAM_INT);
$filterStmt->execute();

$results = $filterStmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['clearFilters'])){
  $refetchProds=$pdo->prepare('SELECT * FROM products');
  $refetchProds->execute();
  $results = $refetchProds->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SHOP</title>
  <link rel="stylesheet" href="Cart.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles/shop.css">
  <link rel="stylesheet" href="../styles/footer.css">
  <link rel="stylesheet" href="../styles/sidomeny.css">
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="d-flex flex-column min-vh-100 m-0 p-0">

  <?php require '../components/header.php' ?>


  <div class="p-5 shop_img w-100 position-absolute"></div>
  <div class="w-75 p-3 border border-black rounded m-auto mb-5 bg-white filter_box m">
    <?php
    if (isset($_SESSION['admin']) && $_SESSION['admin'] == true): ?>

      <form method="post" class="m-auto flex-column w-75 d-flex" enctype="multipart/form-data">
        <h1 class="mb-3 w-75 m-auto text-center">Lägg till produkt</h1>
        <div class="mb-3 w-75 m-auto m">
          <input class="w-100 me-3" type="text" id="" name="name" placeholder="namn">
        </div>

        <div class="mb-3 w-75 m-auto m">
          <input class="w-100" type="number" id="price" step="0.01" name="price" placeholder="Pris">
        </div>

        <div class="mb-3 w-75 m-auto m">
          <input class="w-100" type="text" id="description" name="desc" placeholder="Beskrivning">
        </div>

        <div class="mb-3 w-75 m-auto m">
          <input class="w-100" type="number" id="stock" name="stock" placeholder="stock">
        </div>

        <div class="mb-3 w-75 m-auto">
          <input type="file" name="images[]" multiple>
        </div>

        <input type="submit" name="createProduct" value="Lägg till produkt"
          class="mb-3 w-50 m-auto text-center btn btn-primary t">
      </form>
    <?php endif; ?>

    <h4 class="text-center text-decoration-underline m-auto">filtrera</h4>

    <div class="row flex-column gap-3 m g " id="filters_always_shown">
      <form method="post" id="searchForm" role="search" onsubmit="return searching(event)" class="m g te ">
        <input type="text" name="search" id="search" class="w-25 t" placeholder="Sök">
        <button type="submit" name="search" class="btn btn-success r mt-3">Sök</button>
      </form>

      <div>
       
      </div>
    </div>

    <hr>

    <div id="filters_hideable" class="m g te">
    <form class="d-flex flex-column  m g te" method="post">
          <span class="multi-range w-25 t g">
            <span id="range_lowest">lägsta pris: <?php echo $lowestPrice['price'] ?>kr</span>
            <input name="priceLowest" type="range" min="<?php echo $lowestPrice['price'] ?>" max="<?php echo $highestPrice['price'] ?>" value="<?php echo $lowestPrice['price'] ?>" id="lower" onchange="lowerRange()">

          </span>

          <span class="multi-range w-25 t g">
            <span id="range_highest">högsta pris: <?php echo $highestPrice['price'] ?>kr</span>
            <input name="priceHighest" type="range" min="<?php echo $lowestPrice['price'] ?>" max="<?php echo $highestPrice['price'] ?>" value="<?php echo $highestPrice['price'] ?>" id="higher" onchange="higherRange()">

          </span>

          <button type="submit" class="btn btn-primary w-25 r" name="filterItems">Filtrera</button>
        </form>
        <br>
        <form method="post">
          <button type="submit" class="btn btn-danger r" name="clearFilters">Rensa filter</button>
        </form>
    </div>

    <div class="w-25 m-auto justify-content-center m mt-4"> 
      <div class="col text-center">
        <input  type="checkbox" checked class="col" id="filter_button" onclick="filtersHideShow()"></input>
        <label for="filter_button" id="filter_label" class="col btn btn-secondary w-100 r">Göm filter &uarr;</label>
      </div>
    </div>
  </div>

  <h1 class="text-center text-decoration-underline">Shop</h1>

  <div class=" w-100 row justify-content-center gap-5 item-list-padding">
    <?php foreach ($results as $row):

      $imageNames = explode(',', $row['imageurls']);

      $imageUrls = array_map(function ($imageName) use ($supabaseUrl, $bucketName) {
        return "$supabaseUrl/storage/v1/object/public/$bucketName/webbshop/$imageName";
      }, $imageNames);

      $revFetch = $pdo->prepare("SELECT * FROM reviews WHERE product_id = :id ORDER BY id DESC");
      $revFetch->bindParam(":id", $row['id'], PDO::PARAM_INT);
      $revFetch->execute();

      $reviews = $revFetch->fetchAll(PDO::FETCH_ASSOC);

      ?>

      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == true): ?>
        <div class="modal fade" id="item_<?php echo $row['id'] ?>_deleteModal" tabindex="-1"
          aria-labelledby="item_<?php echo $row['id'] ?>_deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="item_<?php echo $row['id'] ?>_deleteModalLabel"><?php echo $row['name'] ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                DELETE THIS ITEM?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form method="post">
                  <button name="delete_<?php echo $row['id'] ?>" type="submit" class="btn btn-danger">DELETE</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php
      if (isset($_POST['delete_' . $row['id']]) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $product_to_delete = $row['id'];

        $deleteStmt = $pdo->prepare('DELETE FROM products where id = :id');
        $deleteStmt->bindParam(':id', $product_to_delete);
        $deleteStmt->execute();


        echo "<script>
    window.location.reload();
</script>
";
      }
      ?>



      <div type="button" class="card p-0 col-3 ItemCard" style="width: 18rem;" id="item_<?php echo $row['id'] ?>">
        <img src="<?php echo $imageUrls['0'] ?>" class="card-img-top" alt="...">
        <div class="card-body w-100 m-auto d-flex flex-column">
        <span class="float-end"> I lager: <?php echo $row['stock'] ?></span>
          <h5 class="card-title m-auto"><?php echo $row['name'] ?></h5>
          <p class="card-text m-auto mb-2"><?php echo $row['price'] ?> SEK </p>
          <div class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
            data-bs-target="#item_<?php echo $row['id'] ?>_modal">visa</div>
          <?php if (isset($_SESSION['userID'])): ?>
            <?php if ($row['stock'] > 0): ?>
            <form method="post" id="cartForm_<?php echo $row['id']; ?>"
              onsubmit="return cartStuff(event, <?php echo $row['id'] ?>)">
              <button class="btn btn-primary w-100 mb-2" id="addToCart" name="addToCart">
                Lägg i korg
              </button>
            </form>
            <?php endif; ?>
            
            <?php if ($row['stock'] <= 0): 
              $setZeroStmt = $pdo->prepare('UPDATE products SET stock = 0 WHERE id = :prod_id');
              $setZeroStmt->bindParam(':prod_id', $row['id'], PDO::PARAM_INT);
              $setZeroStmt->execute();
              ?>
              <button class="btn btn-secondary w-100 mb-2">
                Inte i lager
              </button>
            <?php endif; ?>

            <?php else: ?>
              <button class="btn btn-secondary w-100 mb-2">
                vänligen logga in för att köpa varor
              </button>
            
          <?php endif; ?>
          <?php
if (isset($_POST['EditProduct_' . $row['id']]) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $product_id = $_POST['productID'];

    // Fetch the current values from the database
    $fetchProductStmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $fetchProductStmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $fetchProductStmt->execute();
    $existingProduct = $fetchProductStmt->fetch(PDO::FETCH_ASSOC);

    // If input is empty, keep the current value from the database
    $newName = !empty($_POST['Editname']) ? $_POST['Editname'] : $existingProduct['name'];
    $newPrice = !empty($_POST['Editprice']) ? $_POST['Editprice'] : $existingProduct['price'];
    $newDesc = !empty($_POST['Editdesc']) ? $_POST['Editdesc'] : $existingProduct['description'];
    $newStock = !empty($_POST['Editstock']) ? $_POST['Editstock'] : $existingProduct['stock'];

    // Prepare the update query
    $updateProductStmt = $pdo->prepare('UPDATE products SET name = :newName, price = :newPrice, description = :newDesc, stock = :newStock WHERE id = :id');
    $updateProductStmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $updateProductStmt->bindParam(':newName', $newName, PDO::PARAM_STR);
    $updateProductStmt->bindParam(':newPrice', $newPrice, PDO::PARAM_STR);
    $updateProductStmt->bindParam(':newDesc', $newDesc, PDO::PARAM_STR);
    $updateProductStmt->bindParam(':newStock', $newStock, PDO::PARAM_INT);
    $updateProductStmt->execute();
}
?>
          <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
           
          <button class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $row['id'] ?>">Edit</button>
          <?php endif; ?>

          <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == true): ?>
            <button class="btn btn-danger" data-bs-toggle="modal"
              data-bs-target="#item_<?php echo $row['id'] ?>_deleteModal">DELETE</button>
          <?php endif; ?>

        </div>
      </div>
      

      <div class="modal fade" id="editModal_<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="editModal_<?php echo $row['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editModal_<?php echo $row['id'] ?>">Edit item "<?php echo $row['name'] ?>"</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form method="post" name="EditProduct_<?php echo $row['id'] ?>" class="m-auto flex-column w-75 d-flex">
        <h1 class="mb-3 w-75 m-auto text-center">Ändra produkt</h1>
        <div class="mb-3 w-75 m-auto">
          <input class="w-100 me-3" type="text" name="Editname" placeholder="namn">
        </div>

        <div class="mb-3 w-75 m-auto">
          <input class="w-100" type="number" id="price" step="0.01" name="Editprice" placeholder="Pris">
        </div>

        <div class="mb-3 w-75 m-auto">
          <input class="w-100" type="text" id="description" name="Editdesc" placeholder="Beskrivning">
        </div>

        <div class="mb-3 w-75 m-auto">
          <input class="w-100" type="number" id="stock" name="Editstock" placeholder="stock">
        </div>
        <input type="hidden" name="productID" value="<?php echo $row['id'] ?>">

        
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="EditProduct_<?php echo $row['id'] ?>" 
          class=" text-center btn btn-primary">Ändra</button>
      </div>
      </form>
    </div>
  </div>
</div>

      <div class="modal fade" id="item_<?php echo $row['id']; ?>_modal" tabindex="-1"
        aria-labelledby="exampleModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel<?php echo $row['id']; ?>"><?php echo $row['name']; ?>
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row m-0 p-0">
              <div class="w-50">
                <div class="ms-2 picture-box bg-transparent my-2">
                  <div id="carouselExampleInterval<?php echo $row['id']; ?>" class="carousel slide shadow"
                    data-bs-ride="carousel">
                    <div class="carousel-indicators">
                      <button type="button" data-bs-target="#carouselExampleInterval<?php echo $row['id']; ?>"
                        data-bs-slide-to="0" aria-current="true" class="active" aria-label="Slide 1"></button>
                      <?php for ($i = 1; $i != sizeof($imageUrls); $i++): ?>
                        <button type="button" data-bs-target="#carouselExampleInterval<?php echo $row['id']; ?>"
                          data-bs-slide-to="<?php echo $i ?>" aria-label="Slide <?php echo $i ?>"></button>
                      <?php endfor; ?>
                    </div>
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <img src="<?php echo $imageUrls['0'] ?>" class="d-block w-100 showcase-images" alt="...">
                      </div>
                      <?php for ($i = 1; $i != sizeof($imageUrls); $i++): ?>
                        <div class="carousel-item">
                          <img src="<?php echo $imageUrls[$i] ?>" class="d-block w-100 showcase-images" alt="...">
                        </div>
                      <?php endfor; ?>
                    </div>
                    <button class="carousel-control-prev" type="button"
                      data-bs-target="#carouselExampleInterval<?php echo $row['id']; ?>" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                      data-bs-target="#carouselExampleInterval<?php echo $row['id']; ?>" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
                  </div>
                </div>
              </div>

              <div class="col border-start">
                <h2 class="text-decoration-underline text-center"><?php echo $row['name'] ?></h2>
                <h3 class="position-absolute end-0 me-2">
                <strong>
                 I lager:  <?php echo $row['stock'] ?>
                </strong>
              </h3>
                <p class="fs-4 fw-semibold">
                  <?php echo $row['description'] ?>
                </p>
                <hr>
                <div class="col overflow-y-scroll overflow-x-hidden reviews">
                  <h3 class="text-decoration-underline text-center">Recensioner</h3>

                  <form class="w-100 row justify-content-center" method="post" id="revForm_<?php echo $row['id'] ?>"
                    onsubmit="return reviewItem(event, <?php echo $row['id'] ?>)">
                    <input type="text" name="review" id="revText_<?php echo $row['id'] ?>" class="rounded w-50 me-2"
                      placeholder="Skriv en recension">
                    <button class="btn btn-primary w-25">Skicka!</button>
                  </form>

                  <hr>

                  <div id="reviews_<?php echo $row['id'] ?>" class="overflow-y-auto w-75 border border-black rounded m-auto reviewBox">
                    <?php if (!empty($reviews)): ?>
                      <?php foreach ($reviews as $review):

                        if (isset($_POST['delete_' . $review['id']]) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                          $revToDel = $review['id'];
                          $deleteRevStmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
                          $deleteRevStmt->bindParam(":id", $revToDel, PDO::PARAM_INT);
                          $deleteRevStmt->execute();

                          echo "<script>
    window.location.reload();
</script>
";
                        }
                        ?>

                        <div class="card w-75 m-auto mb-2" style="width: 18rem;">
                          <div class="card-header">
                            <?php echo $review['username'] ?>
                            <span class="float-end"><?php echo $review['created_at'] ?></span>
                          </div>
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item"><?php echo $review['text'] ?>
                              <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == true || $_SESSION['userID'] === $review['user_id']): ?>
                                <form method="post">
                                    <button name="delete_<?php echo $review['id'] ?>" type="submit"
                                      class="btn btn-danger" data-bs-dismiss="modal">DELETE</button>
                                  </form>
                              <?php endif; ?>
                            </li>
                          </ul>
                        </div>

                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="p-3">No reviews available</div>
                    <?php endif; ?>
                  </div>

                </div>

              </div>
              <div class="modal-footer">
                <h3 class="me-4 ">
                  <strong>
                    <?php echo $row['price'] ?> SEK
                  </strong>
                </h3>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
                <?php if (isset($_SESSION['userID'])): ?>
            <?php if ($row['stock'] > 0): ?>
            <form method="post" id="cartForm_<?php echo $row['id']; ?>"
              onsubmit="return cartStuff(event, <?php echo $row['id'] ?>)">
              <button class="btn btn-primary w-100 mb-2" id="addToCart" name="addToCart">
                Lägg i korg
              </button>
            </form>
            <?php endif; ?>
            
            <?php if ($row['stock'] <= 0): 
              $setZeroStmt = $pdo->prepare('UPDATE products SET stock = 0 WHERE id = :prod_id');
              $setZeroStmt->bindParam(':prod_id', $row['id'], PDO::PARAM_INT);
              $setZeroStmt->execute();
              ?>
              <button class="btn btn-secondary w-100 mb-2">
                Inte i lager
              </button>
            <?php endif; ?>

            <?php else: ?>
              <button class="btn btn-secondary w-100 mb-2">
                vänligen logga in för att köpa varor
              </button>
            
          <?php endif; ?>
          
              </div>
            </div>
          </div>
        </div>



      </div>
    <?php endforeach; ?>

</body>

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

</html>