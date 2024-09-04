<?php
require '../db/dbconn.php';

$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtdWFlanl6b2ZjbGNzeG1pbGhyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTcyNTI4MzIzOCwiZXhwIjoyMDQwODU5MjM4fQ.gO6JV_lsqSsu7GcPKCBXpkD5v_RPj9pxZto2JTm6u8M';

$supabaseUrl = 'https://emuaejyzofclcsxmilhr.supabase.co'; // Replace with your Supabase project URL
$bucketName = 'kastmyrensBilder'; // Your Supabase bucket name


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
  

  
  $createStmt = $pdo->prepare('INSERT INTO products (name, price, description, imageurls) VALUES (:name, :price, :description, :pictures)');
    
  $createStmt->bindParam(':name', $name, PDO::PARAM_STR);
  $createStmt->bindParam(':price', $price, PDO::PARAM_STR);
  $createStmt->bindParam(':description', $description, PDO::PARAM_STR);
  $createStmt->bindParam(':pictures', $pictures, PDO::PARAM_STR);
  $createStmt->execute();
    

  
    
    
    

}


$query = 'SELECT * FROM products';

$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP</title>
    <link rel="stylesheet" href="Cart.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/shop.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/sidomeny.css">


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
        <a class="nav-link active" style="color: black; font-size: 20px;" href="shop.php">Webbshop</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="ban_sida.php">Karta</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="bokning_sida.php">Bokning</a>
      </li>     
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
      </li> 
</ul>
      <div class="container">

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
    
      </div>

      <div class="p-5 shop_img w-100 position-absolute"></div>
      <div class="w-75 p-3 border border-black rounded m-auto mb-5 bg-white filter_box" >
    <?php  
        if (isset($_SESSION['admin'])): ?>
                  <form method="post" class="m-auto flex-column w-75" enctype="multipart/form-data">
                    <div class="mb-3 w-75 m-auto">
                    <input class="w-100 me-3" type="text" id="" name="name" placeholder="namn">    
                    </div>          

                    <div class="mb-3 w-75 m-auto">
                    <input class="w-100" type="number" id="price"  step="0.01" name="price" placeholder="Pris">
                    </div>

                    <div class="mb-3 w-75 m-auto">
                    <input class="w-100" type="text" id="description" name="desc" placeholder="Beskrivning">
                    </div>
                      
                    <div class="mb-3 w-75 m-auto">  
                    <input type="file" name="images[]" multiple>
                    </div>
                      
                    <input type="submit" name="createProduct" value="Lägg in produkt" class="w-100 m-auto text-center btn btn-primary">
                  </form>
    <?php endif;?>

        <h4 class="text-center text-decoration-underline m-auto">filtrera</h4>

        <div class="row flex-column gap-3" id="filters_always_shown">
            <form>
                <input type="text" name="search" id="search" class="w-25" placeholder="Sök">
                <button type="submit" class="btn btn-success">Sök</button>
            </form>

            <div>
                <form>
                    <span class="multi-range w-25">
                        <span id="range_lowest">lägsta pris: 0kr</span>
                        <input type="range" min="0" max="50" value="0" id="lower" onchange="test()">

                    </span>
                </form>
            </div>
        </div>
       
        <hr>

        <div id="filters_hideable">
            <span>[hideable filters here]</span>
        </div>

        <div class="w-25 m-auto row justify-content-center">
            <div class="col text-center">
            <input type="checkbox" checked class="col" id="filter_button" onclick="filtersHideShow()"></input>
            <label for="filter_button" id="filter_label" class="col btn btn-secondary w-100">Göm filter &uarr;</label>
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

        
     
       ?>
    

        <button type="button" class="card p-0 col-3 ItemCard" style="width: 18rem;" data-bs-toggle="modal" data-bs-target="#item_<?php echo $row['id'] ?>_modal">
            <img src="../images/example.jpg" class="card-img-top" alt="...">
            <div class="card-body m-auto">
              <h5 class="card-title"><?php echo $row['name'] ?></h5>
              <p class="card-text">Nå toppen!</p>
              <div class="btn btn-primary w-100">visa</div>
            </div>
          </button>
          
          
    
      <div class="modal fade " id="item_<?php echo $row['id']; ?>_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Vara #1</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row m-0 p-0">
            <div class="w-50">
                <div class="ms-2 picture-box bg-transparent my-2">
       <div id="carouselExampleInterval" class="carousel slide shadow" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <?php for ($i = 1; $i != sizeof($imageUrls); $i++): ?> 
                <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="<?php echo $i ?>" aria-label="Slide <?php echo $i ?>"></button>
            <?php endfor; ?>
          </div>
           <div class="carousel-inner">
            <?php foreach ($imageUrls as $imageurl): ?>
             <div class="carousel-item active">
               <img src="<?php echo $imageurl?>" class="d-block w-100 showcase-images" alt="...">
             </div>
            <?php endforeach; ?>
           </div>
           <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
             <span class="carousel-control-prev-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Previous</span>
           </button>
           <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
             <span class="carousel-control-next-icon" aria-hidden="true"></span>
             <span class="visually-hidden">Next</span>
           </button>
         </div>
        </div>
            </div>

         <div class="col border-start">
            <h2 class="text-decoration-underline text-center"><?php echo $row['name'] ?></h2>
            <p class="fs-4 fw-semibold">           
                 <?php echo $row['description']?>
            </p>
            <hr>
            <div class="col overflow-y-scroll overflow-x-hidden reviews">
                <h3 class="text-decoration-underline text-center">Recensioner</h3>

                <form class="w-100 row justify-content-center">
                    <input type="text" name="review" id="review" class="rounded w-50 me-2" placeholder="Skriv en recension">
                    <button type="submit" class="btn btn-primary w-25">Skicka!</button>
                </form>

                <hr>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>

                  <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare 2
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>

                  <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare 3
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>
            </div>
         </div>
         
        </div>
        <div class="modal-footer">
          <h3 class="me-4">
           <strong>
            <?php echo $row['price'] ?>
           </strong>
           </h3>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
          <button type="button" class="btn btn-primary">Lägg till i kundvagn</button>
        </div>
      </div>
    </div>
  </div>
            
      <?php endforeach; ?>
        
    </div>
  <script src="../scripts/shop.js"></script>
  <script src="../scripts/sidomeny.js"></script>
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

