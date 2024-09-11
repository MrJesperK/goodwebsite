<?php 
require '../db/dbconn.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the 'players' field is set
    if (isset($_POST['players'])) {
        // Sanitize and validate integer input
        $players = filter_var($_POST['players'], FILTER_VALIDATE_INT);
        if ($players !== false && $players >= 1 && $players <= 8) {
            // Input is valid, process it
            echo "Number of players: " . $players;
        } else {
            echo "Invalid number of players. Please enter a number between 1 and 8.";
        }
    } else {
        echo "No players data received.";
    }
}

var_dump($_POST);
$DateTimeData = implode(' ', $_POST);
var_dump($_SESSION['userID']);
var_dump($DateTimeData);

$Split = explode(" ", $DateTimeData, 5);
var_dump($Split);

$dateWork = $Split[0] . " " . $Split[1] . " " . $Split[2];
$timeWork = $Split[3];
$players = $Split[4];

$dateTimeObj = DateTime::createFromFormat('d F Y', $dateWork);
if ($dateTimeObj) {
    $formattedDateWork = $dateTimeObj->format('Y-m-d');  // Convert to 'Y-m-d'
}
var_dump($dateWork);
var_dump($timeWork);  
var_dump($formattedDateWork);
var_dump($players);
  

if(isset($_SESSION['username'])){
    try {
        $fetchStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $fetchStmt->bindParam(":id", $_SESSION['userID'], PDO::PARAM_INT);
        $fetchStmt->execute();
        $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        $name = $user['name'];
        $email = $user['email'];
   
}  catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage() . "<br />";
    die();
    
}


if(isset($_POST['SubmitButton'])){
try{  
    if (empty($formattedDateWork) || empty($timeWork) || empty($players)) {
        die("Error: player is empty");
    }

    $inserStmt = $pdo->prepare('INSERT INTO bokningar(user_id, datum_, tid_, people) VALUES (:User, :Datum, :Tid, :People)');
    $inserStmt->bindParam(':User', $_SESSION['userID'], PDO::PARAM_INT);
    $inserStmt->bindParam(':Datum', $formattedDateWork);
    $inserStmt->bindParam(':Tid', $timeWork,PDO::PARAM_STR);
    $inserStmt->bindParam(':People', $players, PDO::PARAM_INT);
    $inserStmt->execute();


   
    } catch(PDOException $e) {
             echo 'Connection failed: '.$e->getMessage()."<br />";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>


<div>
<div style="
margin:auto;
display:flex;
position:relative;
justify-content:center;
width:300px;
top: 120px; ">
    <h1 style="
    font-weight:300;">Verfiera Bokning</h1>

</div>

<br>
    <p style="
    display:flex; 
    margin:auto; 
    width: 500px; 
    top:100px; 
    
    padding: 5px;
   
    position:relative; ">På den här sidan kan du antigen Ångra din bokning eller Verifiera din bokning och då kommer den tiden att låsas som upptagen. Curabitur gravida vulputate orci, id mollis enim suscipit a. Etiam sodales lacus vitae lorem tincidunt rutrum. Ut pretium turpis eu quam laoreet mollis. Integer ornare condimentum mauris ac vestibulum. Donec aliquam laoreet lorem. Etiam non eros neque. Nam scelerisque sapien at sapien tincidunt fringilla. Sed consectetur consequat ex. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec in metus semper, ullamcorper leo in, tincidunt elit.</p>
    
<div>
<button onclick="window.location.href='bokning_sida.php'"
style="
display:flex;
margin: auto; 
position:relative;
width:300px;
top:100px;
font-weight: 500;
justify-content:center;


">Ångra Bokning</button>

<div>
<div style="
  width: 100%;
  justify-content: center;
  margin: auto;
  margin-top: 100px;">
  
  <div 
  style="
  align-items: left;
  display: flex;
  font-weight: 300;
  justify-content: center;
  
  width: 400px;
  margin:auto;
 "
  
  >
  
  Date: <?php echo $dateWork; ?>
  <br>
  Time: <?php echo $timeWork;?> CET
  <br>
  Amount: <?php echo $players; ?> Players
  <br>
  User: <?php echo $_SESSION['username']; ?>
  <br> Email: <?php echo $user['email']; ?>
  </div>




</div>
</div>
<form method="POST">
    <input type="hidden" name="date" value="<?= $dateWork?>">
    <input type="hidden" name="time" value="<?= $timeWork?>">
    <input type="hidden" name="people" value="<?= $players?>">
<button name="SubmitButton" type="submit" 
style="display: flex;
margin:auto;
width:300px;
font-weight: 500;
justify-content: center;
"> Verifiera Bokning </button>

</form>
</div>
</div>
    
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>