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
    // Method that selects all the players colors based on game_id
    $sql = "SELECT player1_color, player2_color, player3_color, player4_color FROM game WHERE game_id = '". $gameId ."'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Extract player colors from the row
        $playerColors = array([
          "player1Color" => $row['player1_color'],
          "player2Color" => $row['player2_color'],
          "player3Color" => $row['player3_color'],
          "player4Color" => $row['player4_color'],
        ]);
        
        $json_data = json_encode($playerColors);
        echo $json_data;
      } else {
        echo "Error: Game ".$gameId." not found.";
      }
}
