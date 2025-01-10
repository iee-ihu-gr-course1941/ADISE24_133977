<?php
// Connect to the database
include 'config.php';

// Function to end the game
function endGame($conn, $gameId, $winner) {
    // Check if the game status is already 'ended'
    $checkStatusSql = "SELECT gstatus FROM game_status WHERE game_id = ".$gameId;
    $checkStatusStmt = $conn->prepare($checkStatusSql);
    $checkStatusStmt->execute();
    $result = $checkStatusStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStatusStmt->close();

    if ($row) {
        if (strcasecmp($row['gstatus'], 'ended') === 0) {
            // Game is already ended
            return array('status' => 'success', 'message' => 'Game already ended.'); 
        }
    } else {
        return array('status' => 'error', 'message' => 'Game not found.'); 
    }

    // Update game status to "ended"
    $updateStatusSql = "UPDATE game_status SET gstatus = 'ended', updated = NOW() WHERE game_id = ". $gameId;
    $updateStatusStmt = $conn->prepare($updateStatusSql);

    if ($updateStatusStmt->execute()) {
        $updateStatusStmt->close();
        return array('status' => 'success');
    } else {
        $updateStatusStmt->close();
        return array('status' => 'error', 'message' => "Error updating game status: " . $updateStatusStmt->error); 
    }

    // Update the winner of the game
    $updateWinnerSql = "UPDATE game_status SET result = ". $winner ." WHERE game_id = ". $gameId;
    $updateWinnerStmt = $conn->prepare($updateWinnerSql);
    
    if ($updateWinnerStmt->execute()) {
        $updateWinnerStmt->close();
        return array('status' => 'success');
    } else {
        $updateWinnerStmt->close();
        return array('status' => 'error', 'message' => "Error updating winner: " . $updateWinnerStmt->error); 
    }
}

// Process form submission if data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['gameId']) && is_numeric($_POST['gameId'])) {  
        if (isset($_POST['action']) && $_POST['action'] === 'endingState') {
            $gameId = intval($_POST['gameId']);
            $winner = intval($_POST['winner']);
      
            $selectPlayerIDsql = "SELECT player1_id, player2_id FROM game WHERE game_id = ?";
            $selectPlayerIDstmt = $conn->prepare($selectPlayerIDsql);
            $selectPlayerIDstmt->bind_param("i", $gameId);
            $selectPlayerIDstmt->execute();
            $result = $selectPlayerIDstmt->get_result();
            $row = $result->fetch_assoc();
            $selectPlayerIDstmt->close();
      
            if ($row) { 
              if ($row['player1_id'] == $winner) {
                $winner = 'p1';
              } else if ($row['player2_id'] == $winner) {
                $winner = 'p2';
              } else if ($winner == null) {
                $winner = 'd'; 
              } else {
                return array('status' => 'error', 'message' => 'Invalid winner ID.'); 
              }
            } else {
              return array('status' => 'error', 'message' => 'Game not found.'); 
            }
      
            $result = endGame($conn, $gameId, $winner);
      
            echo json_encode($result); 
          } 

    } else if (isset($_POST['action']) && $_POST['action'] === 'returnEnd') {
        $gameId = intval($_POST['gameId']);
        // Update the game status in the database 
        $sql = "UPDATE game_status SET gstatus = 'aborted' WHERE game_id = " . $gameId; 
        var_dump($sql);
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Game aborted successfully.']); 
          } else {
            $stmt->close();
            echo json_encode(['status' => 'error', 'message' => 'Failed to abort the game.']); 
          }
      
        } else {
          echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        }
      
      } else {
        echo json_encode(['status' => 'error', 'message' => 'This is not a POST request.']);
      }
      ?>