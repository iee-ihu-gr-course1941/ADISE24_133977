<?php
// Connect to the database
include 'config.php';

// Function for board initialization
function initializeBoard($conn, $gameId) {
    $board = array_fill(0, 20, array_fill(0, 20, 0)); 

    // Serialize the board array
    $serializedBoard = serialize($board);

    $sql = "INSERT INTO board (game_id, board, created, updated) VALUES (". $gameId .", '". $serializedBoard ."', NOW(), NOW())";
    $stmt = $conn->prepare($sql);

    $sql3 = "SELECT board_id FROM board WHERE game_id = ". $gameId;
    $stmt3 = $conn->prepare($sql3);

    // var_dump("SQL Report: ".$conn->error);

    if ($stmt->execute()) {
        $stmt->close();
        $stmt3->execute();
        $result = $stmt3->get_result();
        $row = $result->fetch_assoc();
        $stmt3->close();
        // Get the last inserted ID (board_id)
        $board_id = $row['board_id']; 
        return array('status' => 'success', 'board_id' => $board_id);
    } else {
        $stmt->close();
        $stmt3->close();
        return array('status' => 'error', 'message' => "Error initializing board: " . $stmt->error);
    }
}

// Function to load the board
function loadBoard($conn, $gameId, $boardId) {
    $sql = "SELECT board_id, board FROM board WHERE game_id = ". $gameId ." AND board_id = ". $boardId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        return array(
            'status' => 'success',
            'board' => unserialize($row['board']),
            'message' => $row['board_id']
        );
    } else {
        return array('status' => 'error', 'message' => "Board (board_id= $boardId ) not found for game ID: $gameId");
    }
}

// Function to update the board
function updateBoard($conn, $gameId, $board) {
    $serializedBoard = serialize($board);

    $sql = "UPDATE board SET board = '". $serializedBoard ."', updated = NOW() WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);

    if ($stmt->execute()) {
        $stmt->close();
        return array('status' => 'success');
    } else {
        $stmt->close();
        return array('status' => 'error', 'message' => "Error updating board: " . $stmt->error);
    }
}

function loadPlayerColors($conn, $gameId){
    $sql = "SELECT player1_id, player2_id, player1_color, player2_color, player3_color, player4_color FROM game WHERE game_id = ". $gameId ." AND player1_color IS NOT NULL AND player2_color IS NOT NULL AND player3_color IS NOT NULL AND player4_color IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        return array(
            'status' => 'success',
            'player1_id' => $row['player1_id'],
            'player2_id' => $row['player2_id'],
            'player1_color' => $row['player1_color'],
            'player2_color' => $row['player2_color'],
            'player3_color' => $row['player3_color'],
            'player4_color' => $row['player4_color'],
            'message' => 'Player colors loaded'
        );
    } else {
        return array('status' => 'error', 'message' => "Players not found or Players didn't choose colors for game ID: $gameId");
    }
}

function isPlacementValid($conn, $gameId, $coordinates, $board, $pieceId) {
    $playerTurn = getCurrentPlayerTurn($conn, $gameId); // Get the current player's turn

    if ($playerTurn === 'p1') {
      $playerId = getCurrentPlayerID($conn, $gameId)[0];
    } else if ($playerTurn === 'p2') {
      $playerId = getCurrentPlayerID($conn, $gameId)[1];
    } else if ($playerTurn === 'p3') {
      $playerId = getCurrentPlayerID($conn, $gameId)[0];
    } else if ($playerTurn === 'p4') {
      $playerId = getCurrentPlayerID($conn, $gameId)[1];
    }
  
    // Get the player's existing pieces on the board 
    $playerPieces = getPlayerPiecesOnBoard($conn, $gameId, $playerId, $playerTurn); 

    // Get the piece coordinates from the database
    $pieceCoordinates = getPieceCoordinatesFromDatabase($conn, $pieceId);

    // Calculate absolute coordinates of the piece based on placement
    $placedPieceCoordinates = [];
    foreach ($pieceCoordinates as $coord) {
        $placedPieceCoordinates[] = [
        $coord[0] + $coordinates['x'], 
        $coord[1] + $coordinates['y'] 
        ];
    }

    // Check if any placed block goes out of bounds
    foreach ($placedPieceCoordinates as $coord) {
        if ($coord[0] < 0 || $coord[0] > 19 || $coord[1] < 0 || $coord[1] > 19) {
        return false; // Out of bounds
        }
    }

    // var_dump($playerPieces);

    // If it's the first piece, allow placement without adjacency check
    if (empty($playerPieces)) { 
        return true; 
    }
    
    // Check for adjacency to at least one of the player's existing pieces
    if (!hasAdjacentPiece($placedPieceCoordinates, $playerPieces)) {
        return false; 
    }
  
    // Check for overlapping with any pieces (player's or opponent's)
    if (hasOverlappingPieces($placedPieceCoordinates, $board)) {
        return false;
    }
  
    return true; // Placement is valid
  }
  
// Helper functions for isPlacementValid function

function getCurrentPlayerTurn($conn, $gameId) {
    $sql = "SELECT p_turn FROM game_status WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['p_turn'];
}

function getCurrentPlayerID($conn, $gameId) {
    $sql = "SELECT player1_id, player2_id FROM game WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $players = [ $row['player1_id'], $row['player2_id'] ];

    return $players;
}

function getPlayerPiecesOnBoard($conn, $gameId, $playerId, $playerTurn) {
    $sql = "SELECT player1_color, player2_color, player3_color, player4_color FROM game WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $pColors = [ $row['player1_color'], $row['player2_color'], $row['player3_color'], $row['player4_color'] ];

    if ($playerTurn === 'p1') {
        $pColor = $pColors[0];
        } else if ($playerTurn === 'p2') {
        $pColor = $pColors[1];
        } else if ($playerTurn === 'p3') {
        $pColor = $pColors[2];
        } else if ($playerTurn === 'p4') {
        $pColor = $pColors[3];
        }

    $playerPieces = [];

    // Get the player's pieces from the database
    $playerPiecesData = getPlayerPiecesFromDatabase($conn, $gameId, $playerId, $pColor); 

    foreach ($playerPiecesData as $pieceData) {
        $x = $pieceData['x'];
        $y = $pieceData['y'];

        // Check if the piece is within the board boundaries
        if ($x >= 0 && $x < 20 && $y >= 0 && $y < 20) {
        $playerPieces[] = [$x, $y]; 
        }
    }

    return $playerPieces;
}

function getPlayerPiecesFromDatabase($conn, $gameId, $playerId, $pColor) {  
    // $sql = "SELECT x, y, pc.groupNum FROM placed_pieces AS pp INNER JOIN pieces AS pc ON pc.piece_id = pp.piece_id WHERE game_id = " . $gameId . " AND player_id = " . $playerId . " AND piece_color = '" . $pColor . "'";
    $sql = "SELECT x, y, pc.groupNum 
            FROM placed_pieces AS pp 
            INNER JOIN pieces AS pc ON pc.piece_id = pp.piece_id 
            WHERE game_id = ? AND player_id = ? AND piece_color = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $gameId, $playerId, $pColor); 
    $stmt->execute();
    $result = $stmt->get_result();
    $pieces = [];

    while ($row = $result->fetch_assoc()) {
        $pieces[] = [
          'x' => $row['x'], 
          'y' => $row['y'], 
          'groupNum' => $row['groupNum'] 
        ];
      }

    $stmt->close();
    return $pieces;
}

function getPieceCoordinatesFromDatabase($conn, $pieceId) {
    $sql = "SELECT piece_coordinates FROM pieces WHERE piece_id = " . $pieceId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $coordinatesString = $row['piece_coordinates'];

        $coordinatesArray = explode(",", $coordinatesString); 

        // Convert string coordinates to 2D arrays
        $pieceCoordinates = [];
        foreach ($coordinatesArray as $coordStr) {
        $coord = explode(".", trim($coordStr)); 
        $pieceCoordinates[] = [intval($coord[0]), intval($coord[1])]; 
        }

        return $pieceCoordinates;
    } else {
        return []; // Handle case where piece ID is not found
    }
}

function hasAdjacentPiece($coordinates, $playerPieces) {
    // Get the x and y coordinates
    $x = $coordinates['x'];
    $y = $coordinates['y'];

    // Define possible adjacent cell coordinates
    $adjacentCells = [
        [$x - 1, $y - 1], [$x - 1, $y + 1], [$x + 1, $y - 1], [$x + 1, $y + 1]
    ];

    foreach ($adjacentCells as $adjCoords) {
        if (in_array($adjCoords, $playerPieces)) {
        // Found an adjacent piece
        return true;
        }
    }

    return false; 
}

function hasOverlappingPieces($placedPieceCoordinates, $board) {
    foreach ($placedPieceCoordinates as $coord) {
        $x = $coord[0];
        $y = $coord[1];

        if ($board[$x][$y] !== 0) { 
        // Cell is already occupied by another piece
        return true; 
        }
    }

    return false; // No overlaps found
}

function hasAvailableMoves($conn, $gameId, $playerId) {
    $playerTurn = getCurrentPlayerTurn($conn, $gameId); // Get the current player's turn

    $sql = "SELECT player1_color, player2_color, player3_color, player4_color FROM game WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $pColors = [ $row['player1_color'], $row['player2_color'], $row['player3_color'], $row['player4_color'] ];

    if ($playerTurn === 'p1') {
        $pColor = $pColors[0];
        } else if ($playerTurn === 'p2') {
        $pColor = $pColors[1];
        } else if ($playerTurn === 'p3') {
        $pColor = $pColors[2];
        } else if ($playerTurn === 'p4') {
        $pColor = $pColors[3];
        }

    // Get the current player's pieces
    $playerPieces = getPlayerPiecesFromDatabase($conn, $gameId, $playerId, $pColor); 

    // Get the game board state
    $board = getBoardStateFromDatabase($conn, $gameId); 

    foreach ($playerPieces as $pieceId) {
        // Get the piece coordinates
        // $pieceCoordinates = getPieceCoordinatesFromDatabase($conn, $pieceId);

        // Generate possible placement positions based on existing player pieces 
        $potentialPositions = getPotentialPositions($board, $playerPieces); 

        foreach ($potentialPositions as $position) {
            $coordinates = ['x' => $position[0], 'y' => $position[1]];

            // Check if the piece can be placed at these coordinates
            if (isPlacementValid($conn, $gameId, $coordinates, $board, $pieceId)) {
            return true; // Found a valid placement
            }
        }
    }

    return false; // No valid moves found
}

function getBoardStateFromDatabase($conn, $gameId) {
    $sql = "SELECT board FROM board WHERE game_id = ". $gameId;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return unserialize($row['board']);
}

function getPotentialPositions($board, $playerPieces) {
    $potentialPositions = [];
    
    foreach ($playerPieces as $pieceCoord) {
        // Get adjacent cells to existing player pieces
        $adjacentCells = getAdjacentCells($pieceCoord); 
    
        foreach ($adjacentCells as $adjCoord) {
        $x = $adjCoord[0];
        $y = $adjCoord[1];
    
        // Check if the cell is empty and within board boundaries
        if ($x >= 0 && $x < 20 && $y >= 0 && $y < 20 && $board[$x][$y] === 0) {
            $potentialPositions[] = [$x, $y];
        }
        }
    }
    return $potentialPositions;
}

function getAdjacentCells($coordinates) {
    $x = $coordinates[0];
    $y = $coordinates[1];
    
    $adjacentCells = [
        [$x - 1, $y], [$x + 1, $y], [$x, $y - 1], [$x, $y + 1], 
        [$x - 1, $y - 1], [$x - 1, $y + 1], [$x + 1, $y - 1], [$x + 1, $y + 1]
    ];
    
    return $adjacentCells;
}

function placePieceOnBoard($board, $pieceCoordinates, $placementCoordinates, $playerColor) {
  // Validate inputs
  if (!is_array($board) || !is_array($pieceCoordinates) || !is_array($placementCoordinates)) {
    return array('status' => 'error', 'message' => 'Invalid input parameters.');
  }

  // Extract placement coordinates
  $placementX = $placementCoordinates[0]['x']; 
  $placementY = $placementCoordinates[0]['y']; 

  // Create a new board to avoid modifying the original board
  $newBoard = $board; 

  // Place the piece on the board
  foreach ($pieceCoordinates as $pieceCoord) {
    $pieceX = $pieceCoord[0];
    $pieceY = $pieceCoord[1];

    $newX = $placementX + $pieceX; 
    $newY = $placementY + $pieceY; 

    // Check if the new coordinates are within the board boundaries
    if ($newX < 0 || $newX >= 20 || $newY < 0 || $newY >= 20) {
      return array('status' => 'error', 'message' => 'Piece placement is out of bounds.');
    }

    // Check if the new position is empty
    if (!isset($newBoard[$newY][$newX]) || $newBoard[$newY][$newX] !== 0) {
      return array('status' => 'error', 'message' => 'Invalid placement: Position ' . $newY . ', ' . $newX . ' already occupied.');
    }

    // Place the piece on the board with the player's color
    $newBoard[$newY][$newX] = $playerColor;
  }

  return array('status' => 'success', 'board' => $newBoard);
}


// Process form submission if data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['gameId']) && is_numeric($_POST['gameId'])) { 
        $gameId = intval($_POST['gameId']); 

        if (isset($_POST['action']) && $_POST['action'] === 'initialize') {
            $result = initializeBoard($conn, $gameId);
            echo json_encode($result);

        } else if (isset($_POST['action']) && $_POST['action'] === 'load') {
            if (isset($_POST['boardId']) && is_numeric($_POST['boardId'])) {
                $boardId = intval($_POST['boardId']);
            }  else {
                $boardId = 0;
            }
            $boardData = loadBoard($conn, $gameId, $boardId);
            echo json_encode($boardData);

        // } else if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // if (isset($_POST['board']) && is_array($_POST['board'])) { 
            //     $board = $_POST['board'];
            //     $playerId = intval($_POST['playerId']);
            //     $placedPieces = $_POST['placedPieces']; // Assuming the placedPieces array is received as a JSON string
            //     $piece_color = $_POST['pieceColor']; // Assuming the piece color is received as a string

            //     $result = updateBoard($conn, $gameId, $board);

            //     // Insert or update placed pieces in the 'placed_pieces' table
            //     foreach ($placedPieces as $pieceData) {
            //         $pieceId = $pieceData['pieceId'];
            //         $x = $pieceData['position']['x'];
            //         $y = $pieceData['position']['y'];
            //         $playerId = $playerId; // Get the current player ID from the session
            //         $pieceColor = $piece_color; // Get the piece color from the session or from the database

            //         // Insert or update the record in the 'placed_pieces' table
            //         $sql = "INSERT INTO placed_pieces (game_id, piece_id, player_id, piece_color, x, y) 
            //                 VALUES (". $gameId .", ". $pieceId .", ". $playerId .", '". $pieceColor ."', ". $x .", ". $y .") 
            //                 ON DUPLICATE KEY UPDATE player_id = VALUES(player_id)"; // Update existing record if it exists
            //         $stmt = $conn->prepare($sql);
            //         $stmt->execute();
            //         $stmt->close();
            //     }

            //     echo json_encode($result); 
            // } else {
            //     echo json_encode(['status' => 'error', 'message' => 'Invalid board data']);
            // }
        } else if (isset($_POST['action']) && $_POST['action'] === 'update') {
                if (isset($_POST['gameId']) && isset($_POST['pieceId']) && isset($_POST['playerId']) && isset($_POST['coordinates']) && isset($_POST['boardId'])) {
                  $gameId = intval($_POST['gameId']);
                  $pieceId = intval($_POST['pieceId']);
                  $playerId = intval($_POST['playerId']);
                  $boardId = intval($_POST['boardId']);
                  $coordinates = $_POST['coordinates'];
                //   $coordinates = json_decode($_POST['coordinates'], true); // Decode coordinates from JSON string
                  $currentColor = $_POST['currentColor'];
              
                  // Get the current board state (if needed)
                  $currentBoard = loadBoard($conn, $gameId, $boardId); // Implement getBoardState() function
              
                  // Get piece coordinates
                  $pieceCoordinates = getPieceCoordinatesFromDatabase($conn, $pieceId); 
              
                  // Create a new board with the piece placed
                  $newBoard = placePieceOnBoard($currentBoard, $pieceCoordinates, $coordinates, $currentColor); 

                  // Update the board in the database
                  $result = updateBoard($conn, $gameId, $newBoard); 
              
                  if ($result['status'] === 'success') {
                    // Insert the placed piece into the 'placed_pieces' table
                    $insertSql = "INSERT INTO placed_pieces (game_id, piece_id, player_id, x, y) 
                                 VALUES (".$gameId.", ".$pieceId.", ".$playerId.", '".$coordinates[0]."', '".$coordinates[1]."')";
                    $insertStmt = $conn->prepare($insertSql);
              
                    if ($insertStmt->execute()) {
                      $insertStmt->close();
              
                      // Remove the placed piece from the player_pieces table
                      $deleteSql = "DELETE FROM player_pieces WHERE game_id = ".$gameId." AND piece_id = ".$pieceId." AND player_id = " .$playerId;
                      $deleteStmt = $conn->prepare($deleteSql);
                      $deleteStmt->execute();
                      $deleteStmt->close();
              
                      echo json_encode(['status' => 'success', 'message' => 'Piece placed successfully.']);
                    } else {
                      echo json_encode(['status' => 'error', 'message' => "Failed to insert placed piece: " . $insertStmt->error]);
                    }
                  } else {
                    echo json_encode($result); // Return the error message from updateBoard()
                  }
              
                } else {
                  echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
                }
        
        } else if (isset($_POST['action']) && $_POST['action'] === 'loadPlayerColors') {
            $result = loadPlayerColors($conn, $gameId);
            echo json_encode($result);

        } else if (isset($_POST['action']) && $_POST['action'] === 'loadPieces') {
            $sql = "SELECT player_num, user_id, available_piece_id, pc.piece_name FROM player_pieces AS pp INNER JOIN pieces AS pc ON pp.available_piece_id = pc.piece_id WHERE game_id = ". $gameId;    
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $pieces = array();
            while ($row = $result->fetch_assoc()) {
                $pieces[] = $row;
            }

            echo json_encode(['status' => 'success', 'pieces' => $pieces]);

        } else if (isset($_POST['action']) && $_POST['action'] === 'pieceValidation') {
            $playerId = intval($_POST['playerId']);
            $colorNum = $_POST['colorNum'];

            $sql = "SELECT available_piece_id FROM player_pieces AS pp WHERE game_id = ". $gameId ." AND user_id = ". $playerId ." AND player_num = '". $colorNum ."'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $availablePieces = array();
            while ($row = $result->fetch_assoc()) {
                $availablePieces[] = $row;
            }

            echo json_encode(['status' => 'success', 'pieces' => $availablePieces]);

        } else if (isset($_POST['action']) && $_POST['action'] === 'validateCoordinates') {
            $board_id = intval($_POST['boardId']);
            $coordinates = json_decode($_POST['coordinates'], true); // Decode JSON string to array
            $piece_id = intval($_POST['pieceId']);

            // Validate input (optional)
            if (!is_array($coordinates) || count($coordinates) !== 1) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid coordinates format.']);
                exit;
            }

            $coordinates = $coordinates[0]; // Get the first (and only) coordinate from the array

            $sql = "SELECT board FROM board WHERE game_id = ". $gameId ." AND board_id = ". $board_id;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $board = unserialize($row['board']);

                if ($coordinates['x'] >= 0 && $coordinates['x'] < 20 && $coordinates['y'] >= 0 && $coordinates['y'] < 20) { 
                    // Check if the cell is empty 
                    if ($board[$coordinates['x']][$coordinates['y']] == 0) {
                        $answer = isPlacementValid($conn, $gameId, $coordinates, $board, $piece_id);
                        if ($answer) { // Check placement validity
                            echo json_encode(['status' => 'success', 'debug' => $answer]);
                          } else {
                            echo json_encode(['status' => 'error', 'message' => 'Invalid placement']);
                          }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Cell is already occupied']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid coordinates']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Board not found']);
            }

        } else if (isset($_POST['action']) && $_POST['action'] === 'getPieceCoordinates'){
            $piece_id = intval($_POST['pieceId']);
            $pieceCoordinates = [];

            $pieceCoordinates = getPieceCoordinatesFromDatabase($conn, $piece_id);

            echo json_encode(['status' => 'success', 'pieceCoordinates' => $pieceCoordinates]);
        
        } else if (isset($_POST['action']) && $_POST['action'] === 'getPlacedPieces'){
            $sql = "SELECT piece_id, piece_color, x, y FROM placed_pieces WHERE game_id = ". $gameId;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $placedPieces = array();
            while ($row = $result->fetch_assoc()) {
                $placedPieces[] = $row;
            }

            echo json_encode(['status' => 'success', 'placedPieces' => $placedPieces]);

        } else if (isset($_POST['action']) && $_POST['action'] === 'calculateScores'){
            $sql = "SELECT game_id, player_num, user_id, COUNT(available_piece_id) AS pieces_count, SUM(pc.groupNum) AS score FROM player_pieces AS pp INNER JOIN pieces AS pc ON pc.piece_id = pp.available_piece_id WHERE game_id = ". $gameId ." GROUP BY player_num";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $scores = array();
            while ($row = $result->fetch_assoc()) {
                $scores[] = [
                  'player_num' => $row['player_num'], 
                  'player_id' => $row['user_id'], 
                  'pieces_count' => $row['pieces_count'],
                    'score' => $row['score']
                ];
              }
              echo json_encode(['status' => 'success', 'playerScores' => $scores]);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters (action).']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid game ID.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'This is not a POST request.']);
}

