<?php 
require '../db/dbconn.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="crossorigin=""></script>
     
    <title>Kastmyrens Karta</title>
    <link rel="stylesheet" href="Cart.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles/shop.css">
  <link rel="stylesheet" href="../styles/footer.css">
  <link rel="stylesheet" href="../styles/sidomeny.css">
  <link rel="stylesheet" href="../styles/color.css">
  <link rel="stylesheet" href="../styles/index.css">
  <script src="../scripts/shop.js" defer></script>
  <script src="../scripts/sidomeny.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
    <style>
        #map {
        height: 90.5vh;
        }
        #hole_box {
            display: flex;
            gap: 20px; 
            padding: 20px;
            /*background-color: #f0f0f0;*/
            /*border-radius: 10px;  Rounded corners for the container */
            overflow: hidden; 
        }
        
        @media (max-width: 768px) {
          #hole_box {
            display: block;
          }

          .box {
            width: 100%; 
            margin-bottom: 10px; 
          }
            
          #map {height: 400px; }
        
            .table-list{
            overflow-y: auto;
            max-height: 30vh !important;
            margin: 0px;
            
            
        }
        }

        .box {
            flex: 1; 
            padding: 20px;
            /*background-color: #fff;*/
            border: 1px solid #ccc;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 10px; 
        }

        table {
            width: 100%;
            margin: 0px auto;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /*border-radius: 10px; */
           
        }
        

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: grey;
            color: white;
            /*border-top-left-radius: 10px; 
            border-top-right-radius: 10px;*/
        }

        td {
            border-bottom: 1px solid #ddd;
        }

        table tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        table tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

      
        tr:hover {
            background-color: #f1f1f1;
        }
        
        
        .highlight {
            background-color: lightgreen;
            transition: background-color 0.1s ease-in-out;
            
        }
        
        .table-list{
            overflow-y: auto;
            max-height: 80vh;
            margin: 0px;
            
            
        }
        
        .table-container {
        max-height: 90.5vh;  /*Set maximum height to 75% of the viewport height */
       /*  overflow-y: auto;Enable vertical scrolling when content exceeds the max height */
        border: 1px solid #ddd; /* Optional: add a border around the table container */
        }
        
        .table-list::-webkit-scrollbar {
        width: 12px; /* Width of the scrollbar */
        }

        /* Scrollbar track */
        .table-list::-webkit-scrollbar-track {
            background: #f1f1f1; /* Background color of the scrollbar track */
            border-radius: 10px; /* Rounded corners for the scrollbar track */
        }

        /* Scrollbar thumb */
        .table-list::-webkit-scrollbar-thumb {
            background-color: #888; /* Color of the scrollbar thumb */
            border-radius: 10px; /* Rounded corners for the scrollbar thumb */
            border: 2px solid #f1f1f1; /* Border around the scrollbar thumb */
        }

        /* Scrollbar thumb on hover */
        .table-list::-webkit-scrollbar-thumb:hover {
            background-color: #555; /* Color of the scrollbar thumb on hover */
        }
        
        .number-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: red;
            color: white;
            border-radius: 50%;
            font-size: 18px;                
            font-weight: 600;               
            
         }
        
        
    </style>
    
</head>
    
    <!--Navbar-->
    <?php require '../components/header.php' ?>
    <!--Navbar-->

<body class="font-tests d-flex flex-column min-vh-100">
    <div id="hole_box" class="dark-green">
        <div id="map" class="box"></div>
        <div id="hole_list" class="box light-green table-container">
            <table border="1">
                <tr>
                    <th>Hål</th>
                    <th>Distans</th>
                </tr>
            </table>
            <div class="table-list">
            <table border="1" id="myTable">
            </table>
            </div>
        </div>
    </div>
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
    
<script>


var latlngs = [
    [[60.671784226286235, 17.1103799942964], [60.67118599027206, 17.111615157929396]],
    [[60.67145397972065, 17.112413731276625],[60.67204544350032, 17.11033025615419]],
    [[60.67210905566979, 17.10904259116835],[60.67127394449399, 17.10943510012214]],
    [[60.67179776090364, 17.109606290013765],[60.67108447852626, 17.11041591628423]],
    [[60.66954952223488, 17.109322707642562],[60.66935454866401, 17.1079651435478]],
    [[60.670547145334346, 17.109517639619273],[60.67022281059744, 17.11074335577617]],
    [[60.670275365731996, 17.11082253488879],[60.6711989050397, 17.113169748552732]],
    [[60.670657744158476, 17.11370056529265],[60.670348347715375, 17.11467582468626]],
    [[60.670225211142494, 17.115246406289728],[60.66985569116077, 17.11466807067411]],
    [[60.66976021311361, 17.114559613718],[60.670174459607495, 17.111378385737172]],
    [[60.67031313691757, 17.11172880099968],[60.67071811281523, 17.1135238812486]],
    [[60.67166671348708, 17.11335981474914],[60.672520745316966, 17.11292552114402]],
    [[60.6723269356567, 17.113919570959283],[60.67295563103943, 17.114550100939194]],
    [[60.67283745615275, 17.11209875479281],[60.67188571772872, 17.112694793513985]],
    [[60.67174006015992, 17.11287876772491],[60.672360777693186, 17.111207120815532]],
    [[60.67269479308537, 17.111409531153555],[60.6727117998503, 17.110749812991273]],
    [[60.67230208892353, 17.11044047146063],[60.67353584416043, 17.10982494496246]],
    [[60.67365643423952, 17.10918416609711],[60.67222418531065, 17.10946348633066]],
];

var dist = [100, 145, 72, 93, 94, 77, 208, 79, 77, 120, 107, 88, 73, 83, 110, 62, 186, 120];
    
var map = L.map('map').setView([60.671558778874054, 17.111507059977188], 17);
    
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    minZoom: 16,
}).addTo(map);
    

var customIconStart = L.icon({
    iconUrl: '../images/frisbee-player-svgrepo-com.svg',
    iconSize: [25, 25], 
    iconAnchor: [15, 25], 
});
    
var customIconEnd = L.icon({
    iconUrl: '../images/disc-golf-basket-svgrepo-com.svg',
    iconSize: [25, 25], 
    iconAnchor: [10, 15], 
});
    
let j = 1;    
latlngs.forEach(function(pair){
    let row = ('row'+j.toString())
    
    var lat1 = pair[0][0];
    var lat2 = pair[1][0];
                
    var lng1 = pair[0][1];
    var lng2 = pair[1][1];            
    var midpointLat = (lat1 + lat2) / 2;
    var midpointLng = (lng1 + lng2) / 2;
    
    var coords = [midpointLat, midpointLng]

    var numberIcon = L.divIcon({
      className: 'number-icon',
      html: (j).toString(), // Convert the index to a string to display as the marker label
      iconSize: [25, 25],           // Size of the icon
      iconAnchor: [15, 15]          // Center the icon
    });

    // Add the marker with the custom icon to the map
    L.marker(coords, { icon: numberIcon }).addTo(map).on('click', function(){highlightRow(row); map.setView(coords, 18);})
    
    L.marker(pair[0], { icon: customIconStart }).addTo(map).bindPopup("Hål " + j.toString() + " start").on('click', function(){highlightRow(row); map.setView(pair[0], 18);})
    L.marker(pair[1], { icon: customIconEnd }).addTo(map).bindPopup("Hål " + j.toString() + " slut").on('click', function(){highlightRow(row); map.setView(pair[1], 18);})
    j++;
})
    
var polyline = L.polyline(latlngs, {color: 'red', weight: 2, dashArray: '10, 10'}).addTo(map);
    

    
function highlightRow(rowId) {
        var row = document.getElementById(rowId);
        row.classList.add('highlight');
        row.scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
        setTimeout(function() {
            row.classList.remove('highlight');
        }, 1500);
    }


        for (var i = 0; i < latlngs.length; i++) {

            var newRow = document.createElement("tr");

            newRow.id = "row" + (i + 1);
            newRow.setAttribute("index", i);

            var cell1 = document.createElement("td");
            cell1.textContent = (i + 1);
            newRow.appendChild(cell1);

            var cell2 = document.createElement("td");
            cell2.textContent = dist[i] + "m";
            newRow.appendChild(cell2);

            document.getElementById("myTable").appendChild(newRow);
        }


document.querySelectorAll('table tr').forEach(function(row) {
            row.addEventListener('click', function() {
                
            var lat1 = latlngs[this.getAttribute('index')][0][0];
            var lat2 = latlngs[this.getAttribute('index')][1][0];
                
            var lng1 = latlngs[this.getAttribute('index')][0][1];
            var lng2 = latlngs[this.getAttribute('index')][1][1];

            
            var midpointLat = (lat1 + lat2) / 2;
            var midpointLng = (lng1 + lng2) / 2;

            map.setView([midpointLat, midpointLng], 18);
            });
        });
</script>
<script src="../scripts/sidomeny.js"></script>
</html>

