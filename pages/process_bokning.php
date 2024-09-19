<?php
require '../db/dbconn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit();
    }

    if(isset($_SESSION['username'])){
        try {
            $fetchStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $fetchStmt->bindParam(":id", $_SESSION['userID'], PDO::PARAM_INT);
            $fetchStmt->execute();
            $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

       
    }  catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage() . "<br />";
        die();   
    }

    // Retrieve form data
    $datum = $_POST['datum'];
    $time = $_POST['time'];
    $players = $_POST['players'];
     // Assuming you have stored the user ID in session

    // Validate form inputs (simple validation example)
    if (empty($datum) || empty($time) || empty($players)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Additional validation
    if (!is_numeric($players) || $players < 1 || $players > 8) {
        echo json_encode(['success' => false, 'message' => 'Number of players must be between 1 and 8.']);
        exit();
    }

    // Prepare and execute SQL statement to insert booking into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO bokningar (datum_, tid_, people, user_id) VALUES (:datum, :time_, :players, :user_id)");
        $stmt->bindParam(':datum', $datum);
        $stmt->bindParam(':time_', $time, PDO::PARAM_STR);
        $stmt->bindParam(':players', $players, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_INT);

        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Booking submitted successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}}
?>
