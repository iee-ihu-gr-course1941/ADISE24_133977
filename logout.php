<?php
session_start();

// Connect to the database   
include 'config.php';

if ($_POST['gameId']){
    $gameId = $_POST['gameId'];
} else {
    $gameId = $_SESSION['game_id'];
}

try {
    $sql = "UPDATE game_status SET gstatus = 'aborted' WHERE game_id = '" . $gameId . "'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Unset all session variables
    session_unset();

    // Delete the user_activity cookie
    setcookie('user_activity', '', time() - 3600);

    // Destroy the session
    session_destroy();

    // Redirect to the login page or another desired page
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}