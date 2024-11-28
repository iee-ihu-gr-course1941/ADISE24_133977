<?php
session_start();

// Connect to the database   
include 'config.php';

$gameId = $_SESSION['game_id'];

$sql9 = "SELECT gstatus FROM game_status WHERE game_id = '". $gameId ."'";
$stmt9 = $conn->prepare($sql9);
$stmt9->execute();
$result9 = $stmt9->get_result();

if ($result9->num_rows > 0) {
    $row9 = $result9->fetch_assoc();
    if ($row9['game_status'] == 'aborted') {
        session_destroy(); // Destroy the session
        header("Location: login.php");
        exit;
    }
}