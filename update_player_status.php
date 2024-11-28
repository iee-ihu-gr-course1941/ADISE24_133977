<?php
// Connect to the database
include 'config.php';

// Process form submission if data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

// Get data from the POST request
$gameId = $_POST['gameId']  ?? null;
$playerId = $_POST['playerId']  ?? null;
$player1Id = $_POST['player1id']  ?? null;

// Check if required parameters are set
if (!$gameId || !$playerId || !$player1Id) {
    echo "Error: Missing required parameters: ";
    if (!$gameId) {
        echo "gameId, ";
    }
    if (!$playerId) {
        echo "playerId, ";
    }
    if (!$player1Id) {
        echo "player1Id";
    }
    exit;
}

// Method that selects all the players colors based on game_id
$sql = "SELECT player1_color, player2_color, player3_color, player4_color FROM game WHERE game_id = '". $gameId ."'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$p1DBColor = $row['player1_color'];
$p2DBColor = $row['player2_color'];
$p3DBColor = $row['player3_color'];
$p4DBColor = $row['player4_color'];


// Determine the current player's role
$currentPlayerRole = $player1Id == $playerId ? 'player1' : 'player2';

if ($currentPlayerRole == 'player1'){
    $player1Color = $_POST['player1Color'];
    $player3Color = $_POST['player3Color'];

    if($player1Color != $p2DBColor && $player1Color != $p4DBColor){
        if($player3Color != $p2DBColor && $player3Color != $p4DBColor){
            // Update the database
            $sql = "UPDATE game SET player1_color = '". $player1Color ."', player3_color = '". $player3Color ."', player1_ready = 1 WHERE game_id = '". $gameId ."'";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute()) {
                echo 'Your choices have been saved.';
            } else {
                echo 'Request failed.';
            }
        } else {
            echo 'Player 1 - Color 2 has same color as Player 2 Colors (' . $p2DBColor, $p4DBColor . ')';
        }
    } else {
        echo 'Player 1 - Color 1 has same color as Player 2 Colors (' . $p2DBColor, $p4DBColor . ')';
    }
} else {
    $player2Color = $_POST['player2Color'];
    $player4Color = $_POST['player4Color'];

    if($player2Color != $p1DBColor && $player2Color != $p3DBColor){
        if($player4Color != $p1DBColor && $player4Color != $p3DBColor){
            // Update the database
            $sql = "UPDATE game SET player2_color = '". $player2Color ."', player4_color = '". $player4Color ."', player2_ready = 1 WHERE game_id = '". $gameId ."'";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute()) {
                echo 'Your choices have been saved.';
            } else {
                echo 'Request failed.';
            }
        } else {
            echo 'Player 2 - Color 2 has same color as Player 1 Colors (' . $p1DBColor, $p3DBColor . ')';
        }
    } else {
        echo 'Player 2 - Color 1 has same color as Player 1 Colors (' . $p1DBColor, $p3DBColor . ')';
    }
}

$stmt->close();
$conn->close();

} else {
    echo 'This is not a POST request.';
}