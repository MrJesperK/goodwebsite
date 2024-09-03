<?php
require "dbconn.php";
?>

<?php
$supabaseUrl = 'https://emuaejyzofclcsxmilhr.supabase.co'; // Replace with your Supabase project URL
$bucketName = 'kastmyrensBilder'; // Your Supabase bucket name

$query = 'SELECT imageurls FROM products';

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row){
    $imageNames = explode(',', $row['imageurls']);

    $imageUrls = array_map(function ($imageName) use ($supabaseUrl, $bucketName) {
        return "$supabaseUrl/storage/v1/object/public/$bucketName/webbshop/$imageName";
    }, $imageNames);
    
    foreach ($imageUrls as $rows){
        echo $rows;
    }
}

?>
<body>
    <h1>Display Images from Supabase</h1>
    <?php foreach ($imageUrls as $imageUrl): ?>
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Image from Supabase">
    <?php endforeach; ?>
</body>


<?php


/*
// Data to be inserted
$name = 'Frisbee2'; 
$price = 434.99;
$description = 'hurb';
$imageurls = 'example.jpg';
// SQL query to insert data
$insertQuery = 'INSERT INTO products (name,price,description,imageurls) VALUES (:name, :price, :description, :imageurls)';

// Prepare the query
$stmt = $pdo->prepare($insertQuery);

// Bind parameters
$stmt->bindParam(':name', $name);
$stmt->bindParam(':price', $price);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':imageurls', $imageurls);

// Execute the query
$stmt->execute();
*/
    
    
/*   

$query = 'SELECT * FROM users';

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $rows) {
    echo $rows['name'], $rows['id'];
}
*/