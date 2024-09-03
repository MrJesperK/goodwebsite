<?php
$dsn = 'pgsql:host=aws-0-eu-north-1.pooler.supabase.com;port=6543;dbname=postgres';
$user = 'postgres.emuaejyzofclcsxmilhr';  // Supabase provided username
$password = 'LdqbPBvHoCcbFeCz'; 

try {
    $pdo = new PDO($dsn, $user, $password);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


