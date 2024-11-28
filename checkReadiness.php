<?php
session_start();

// Connect to the database   
include 'config.php';

// Get data from the POST request
$gameId = $_POST['gameId']  ?? null;

// Check if required parameters are set
if (!$gameId) {
    echo "Error: Missing required parameters: gameId";
    exit;
} else {
    $sql = "SELECT player1_ready, player2_ready FROM game WHERE game_id = '". $gameId ."'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Extract player colors from the row
        $playerReady = array([
          "player1ready" => $row['player1_ready'],
          "player2ready" => $row['player2_ready'],
        ]);
        
        // Return an array (or JSON if needed)
        $json_data = json_encode($playerReady);
        echo $json_data;
      } else {
        echo "Error: Game ".$gameId." not found.";
      }
}
?>