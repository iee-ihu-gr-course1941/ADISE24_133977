<?php
session_start();

// Connect to the database   
include 'config.php';

echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : '') . '">';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {  // Is checking if the user is logged in. If not, it transfers the user to login.php
    header('Location: login.php');
    exit;
} else {
    if (!isset($_COOKIE['user_activity'])){  // Creates a user_activity cookie
        setcookie('user_activity', 1, time() + 3600);
    }

    $usernm = $_SESSION['username'];
    // Get the player ID from the session
    $playerId = $_SESSION['player_id'];
    echo '<input type="hidden" id="global_playerid" value="' . (isset($_SESSION['player_id']) && $_SESSION['player_id'] ? $_SESSION['player_id'] : '') . '">';

    if(isset($_GET['game_id']) && $_GET['game_id']){  //Checks if the game is aborted. If so, transfers the user to rules.php
        $gameId = $_GET['game_id'];

        echo '<input type="hidden" id="global_gameid" value="' . (isset($_GET['game_id']) && $_GET['game_id'] ? $_GET['game_id'] : '') . '">';

        $sql9 = "SELECT gstatus FROM game_status WHERE game_id = '". $gameId ."'";
        $stmt9 = $conn->prepare($sql9);
        $stmt9->execute();
        $result9 = $stmt9->get_result();

        if ($result9->num_rows > 0) {
            $row9 = $result9->fetch_assoc();

            if ($row9['gstatus'] == 'aborted') {
                $_SESSION['game_status'] = 'aborted';
                $_SESSION['player1_id'] = '';
                $_SESSION['player2_id'] = '';
                header("Location: rules.php");
                exit;
            }
        }
    }

    if(isset($_GET['board_id']) && $_GET['board_id']){  
        $boardId = $_GET['board_id'];

        echo '<input type="hidden" id="global_boardid" value="' . (isset($_GET['board_id']) && $_GET['board_id'] ? $_GET['board_id'] : '') . '">';
    }

    // Determine the current player's role
    $currentPlayerRole = $_SESSION['player1_id'] == $playerId ? 'player1' : 'player2';

}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
        <script src="game.js"></script>
    </head>
    <body>
        <div class="container main-content">
            <div class="row">
                <div class="col-md-12">
                    <h1>Game</h1>
                    <div id="results" class="hidden-score">
                        <div id="player1Scores">Player 1 Score: 0</div>
                        <div id="player2Scores">Player 2 Score: 0</div>
                        <div id="winnerID"></div>
                        <div id="winnerMessage"></div>
                    </div> 
                    <div id="gameStats">
                        <div id="statsContainer">
                            <div id="gameStatus"></div>
                            <div id="gamePlayers"></div>
                            <!-- <div id="gameMessage">Test3</div> -->
                        </div>
                        <button id="clearBoardSelection">Clear Board Selection</button>
                    </div>
                    <div id="gameInfo" class="game">
                        <div id="piecesHeaders">
                        <?php echo $currentPlayerRole == 'player1' ? '  <b>(You are player 1)</b>' : ''; ?>
                            <h3>Player 1 Available Pieces</h3>
                            <div id="player1pieces">
                                <div id="p1color1" class="pieceColumns"></div>
                                <div id="p1color2" class="pieceColumns"></div>
                            </div>
                        </div>
                        <div id="gameBoard" class="game"></div>
                        <div id="piecesHeaders">
                        <?php echo $currentPlayerRole == 'player2' ? '  <b>(You are player 2)</b>' : ''; ?>
                            <h3>Player 2 Available Pieces</h3>
                            <div id="player2pieces">
                                <div id="p2color1" class="pieceColumns"></div>
                                <div id="p2color2" class="pieceColumns"></div>
                            </div>
                        </div>
                    </div>

                    <div id="playerForms">
                        <div id="player1Form" class="player-form <?php echo $currentPlayerRole == 'player1' ? '' : 'disabledDiv'; ?>">
                            <h3>Player 1</h3>
                            <input type="number" id="player1PieceInput" placeholder="Enter Piece ID"> 
                            <button id="player1EndTurn">End Turn</button>
                        </div>

                        <div id="player2Form" class="player-form <?php echo $currentPlayerRole == 'player2' ? '' : 'disabledDiv'; ?>">
                            <h3>Player 2</h3>
                            <input type="number" id="player2PieceInput" placeholder="Enter Piece ID"> 
                            <button id="player2EndTurn">End Turn</button>
                        </div>
                    </div>
                </div>
            </div>
            <button id="game-return-btn">Exit Game</button>
        </div>

        <script>
            $(document).ready(function() {
                var gameId = $('#global_gameid').val();
                var playerId = $('#global_playerid').val();
                var username = $('#usernm').val();
                var boardId = $('#global_boardid').val();
                var box = [];

                if (gameId) {
                    var game = new Game(gameId, playerId, username, boardId);
                    game.init();
                    game.loadAvailablePieces(playerId);
                }

                document.getElementById('game-return-btn').addEventListener('click', function() {
                    game.returnEnd(gameId);
                });

                document.getElementById('gameBoard').addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default behavior
                    console.log(game.playerId == game.currentPlayerId);
                    if (game.playerId == game.currentPlayerId) { // Check if it's the player's turn
                        if (event.target.tagName === 'TD') { // Ensure the clicked element is a TD
                            const cell = event.target; // Get the clicked cell

                            // Clear previous selection
                            const cells = document.querySelectorAll('.temp-box-style');
                            cells.forEach(cell => cell.classList.remove('temp-box-style'));

                            const x = Array.from(cell.parentNode.children).indexOf(cell);
                            const y = Array.from(cell.parentNode.parentNode.children).indexOf(cell.parentNode);

                            // Add coordinates to the game object's array
                            game.boardSelectedCoordinates = [{ x: x, y: y }]; 

                            console.log(game.boardSelectedCoordinates);

                            // Add class to the clicked cell
                            cell.classList.add('temp-box-style');  
                            
                        } 
                    } else {
                            alert('It is not your turn!');
                    }
                });

                $('#clearBoardSelection').on('click', function() {
                    box = [];
                    game.reLoadBoard();
                });

                $('#player1EndTurn').on('click', function() {

                    if (game.playerId == game.currentPlayerId && game.currentPlayerId == game.p1id) { 
                        const player1PieceInput = document.getElementById('player1PieceInput');
                        const pieceId = player1PieceInput.value; 

                        console.log(pieceId);

                        // Get selected coordinates
                        const selectedCoordinates = game.boardSelectedCoordinates;

                        console.log('game.php selectedCoordinates', selectedCoordinates);

                        if (pieceId == '') {
                            alert('Please enter a valid piece ID');
                        } else {
                            if(game.addPiece(selectedCoordinates, pieceId, playerId, gameId, boardId)){
                                console.log('boardid: ', boardId);
                            // If piece placement is successful:
                                // - Clear selected coordinates
                                game.boardSelectedCoordinates = [];
                                // - Clear the input field
                                player1PieceInput.value = '';
                                // - Update available pieces for both players
                                game.loadAvailablePieces(game.p1id); 
                                game.loadAvailablePieces(game.p2id);
                                // Check if the game is over
                                if (!game.hasAvailableMoves(gameId, game.p2id)) {
                                    // Game over! 
                                    // Determine the winner (player with the most points)
                                    // Display a game over message to the players 
                                    game.calculateAndDisplayScores(gameId);
                                    const winnerID = document.getElementById('winnerID');
                                    if(winnerID == ''){
                                        console.warn('No winner yet!');
                                    } else {
                                        // Update the game state in the database
                                        game.endingState(gameId, winnerID);
                                    }
                                }
                            } else {
                                alert("Invalid placement! Please try again.");
                            }
                        }

                    //     // Validate piece ID, coordinates, and check if the piece is available
                    //     if (isValidPieceId(pieceId, playerId, colorNum, gameId) && isValidCoordinates(selectedCoordinates) && isPieceAvailableForPlayer(game.p1id, pieceId)) {

                    //         // Place the piece on the board
                    //         if (placePieceOnBoard(selectedCoordinates, pieceId)) { 
                    //             // If piece placement is successful:
                    //             // - Clear selected coordinates
                    //             game.boardSelectedCoordinates = [];
                    //             // - Clear the input field
                    //             player1PieceInput.value = '';
                    //             // - Update game state (switch turns, etc.)
                    //             // - Update available pieces for both players
                    //             loadAvailablePieces(game.p1id); 
                    //             loadAvailablePieces(game.p2id); 
                    //         } else {
                    //             alert("Invalid placement! Please try again.");
                    //         }

                    //     } else {
                    //         alert("Invalid piece ID or coordinates!");
                    //     }
                    }
                });

                $('#player2EndTurn').on('click', function() {
                    if (game.playerId == game.currentPlayerId && game.currentPlayerId == game.p2id) { 
                        const player2PieceInput = document.getElementById('player2PieceInput');
                        const pieceId = parseInt(player2PieceInput.value); 

                        // Get selected coordinates
                        const selectedCoordinates = game.boardSelectedCoordinates;

                        if (pieceId == '') {
                            alert('Please enter a valid piece ID');
                        } else {
                            if(game.addPiece(selectedCoordinates, pieceId, playerId, gameId, boardId)){
                            // If piece placement is successful:
                                // - Clear selected coordinates
                                game.boardSelectedCoordinates = [];
                                // - Clear the input field
                                player1PieceInput.value = '';
                                // - Update available pieces for both players
                                game.loadAvailablePieces(game.p1id); 
                                game.loadAvailablePieces(game.p2id);
                                // Check if the game is over
                                if (!game.hasAvailableMoves(gameId, game.p1id)) {
                                     // Game over! 
                                    // Determine the winner (player with the most points)
                                    // Display a game over message to the players 
                                    game.calculateAndDisplayScores(gameId);
                                    const winnerID = document.getElementById('winnerID');
                                    if(winnerID == ''){
                                        console.warn('No winner yet!');
                                    } else {
                                        // Update the game state in the database
                                        game.endingState(gameId, winnerID);
                                    }
                                }
                            } else {
                                alert("Invalid placement! Please try again.");
                            }
                        }
                    }
                });

            });
        </script>
    
    <?php include 'footer.php'; ?>
    </body>
</html>