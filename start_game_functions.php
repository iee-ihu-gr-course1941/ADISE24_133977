<?php
// Connect to the database
include 'config.php';

// Function to start the game
function startGame($conn, $gameId) {
    // Check if the game status is already 'initialized'. If so, then join this game.
    $checkStatusSql = "SELECT gstatus FROM game_status WHERE game_id = ".$gameId;
    $checkStatusStmt = $conn->prepare($checkStatusSql);
    $checkStatusStmt->execute();
    $result = $checkStatusStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStatusStmt->close();

    if ($row) {
        if (strcasecmp($row['gstatus'], 'initialized') === 0) {
            // Game is already initialized
            $sql = "SELECT board_id FROM board WHERE game_id = ". $gameId;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            $board_id = $row['board_id']; 

            // Update game status to "started"
            $updateStatusSql = "UPDATE game_status SET gstatus = 'started', p_turn = 'p1', updated = NOW() WHERE game_id = ". $gameId;
            $updateStatusStmt = $conn->prepare($updateStatusSql);

            if ($updateStatusStmt->execute()) {
                $updateStatusStmt->close();
                return array('status' => 'board-ok', 'message' => 'Game already initialized', 'board_id' => $board_id);
            } else {
                $updateStatusStmt->close();
                return array('status' => 'error', 'message' => "Error updating game status: " . $updateStatusStmt->error); 
            }
        }
    } else {
        return array('status' => 'error', 'message' => 'Game not found.'); 
    }

    // Update game status to "initialized"
    $updateStatusSql = "UPDATE game_status SET gstatus = 'initialized', updated = NOW() WHERE game_id = ". $gameId;
    $updateStatusStmt = $conn->prepare($updateStatusSql);
    // $updateStatusStmt->bind_param("i", $gameId);

    if ($updateStatusStmt->execute()) {
        $updateStatusStmt->close();
        return array('status' => 'success');
    } else {
        $updateStatusStmt->close();
        return array('status' => 'error', 'message' => "Error updating game status: " . $updateStatusStmt->error); 
    }
}

// Process form submission if data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['gameId']) && is_numeric($_POST['gameId'])) { 
        $gameId = intval($_POST['gameId']); 
        
        $result = startGame($conn, $gameId);
        echo json_encode($result); 
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid game ID.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'This is not a POST request.']);

}


$conn->close();