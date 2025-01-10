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
            $sql = "UPDATE game SET player1_color = '". $player1Color ."', player3_color = '". $player3Color ."', player1_ready = 1, updated = NOW() WHERE game_id = '". $gameId ."'";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute()) {
                // Check if initial pieces have already been inserted for this player and this game
                $checkSql = "SELECT COUNT(*) as count FROM player_pieces WHERE game_id = ". $gameId ." AND player_num = 'p1'";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $row = $checkResult->fetch_assoc();

                if ($row['count'] == 0) { 
                    // Call the stored procedure for player 1
                    $sql2 = "CALL insert_initial_player_pieces(". $gameId .", 'p1', '". $playerId ."')";
                    // var_dump($sql2);
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute();
                    $stmt2->close();
                }
                
                // Check if initial pieces have already been inserted for this player and this game
                $checkSql2 = "SELECT COUNT(*) as count FROM player_pieces WHERE game_id = ". $gameId ." AND player_num = 'p3'";
                $checkStmt2 = $conn->prepare($checkSql2);
                $checkStmt2->execute();
                $checkResult2 = $checkStmt2->get_result();
                $row2 = $checkResult2->fetch_assoc();

                if ($row2['count'] == 0) { 
                    // Call the stored procedure for player 3
                    $sql3 = "CALL insert_initial_player_pieces(". $gameId .", 'p3', '". $playerId ."')";
                    // var_dump($sql3);
                    $stmt3 = $conn->prepare($sql3);
                    $stmt3->execute();
                    $stmt3->close();
                }
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
            $sql = "UPDATE game SET player2_color = '". $player2Color ."', player4_color = '". $player4Color ."', player2_ready = 1, updated = NOW() WHERE game_id = '". $gameId ."'";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute()) {
                // Check if initial pieces have already been inserted for this player and this game
                $checkSql3 = "SELECT COUNT(*) as count FROM player_pieces WHERE game_id = ". $gameId ." AND player_num = 'p2'";
                $checkStmt3 = $conn->prepare($checkSql3);
                $checkStmt3->execute();
                $checkResult3 = $checkStmt3->get_result();
                $row3 = $checkResult3->fetch_assoc();

                if ($row3['count'] == 0) { 
                    // Call the stored procedure for player 2
                    $sql4 = "CALL insert_initial_player_pieces(". $gameId .", 'p2', '". $playerId ."')";
                    // var_dump($sql4);
                    $stmt4 = $conn->prepare($sql4);
                    $stmt4->execute();
                    $stmt4->close();
                }
                // Check if initial pieces have already been inserted for this player and this game
                $checkSql4 = "SELECT COUNT(*) as count FROM player_pieces WHERE game_id = ". $gameId ." AND player_num = 'p4'";
                $checkStmt4 = $conn->prepare($checkSql4);
                $checkStmt4->execute();
                $checkResult4 = $checkStmt4->get_result();
                $row4 = $checkResult4->fetch_assoc();

                if ($row4['count'] == 0) {
                    // Call the stored procedure for player 4
                    $sql5 = "CALL insert_initial_player_pieces(". $gameId .", 'p4', '". $playerId ."')";
                    // var_dump($sql5);
                    $stmt5 = $conn->prepare($sql5);
                    $stmt5->execute();
                    $stmt5->close();
                }
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