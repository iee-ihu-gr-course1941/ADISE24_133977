<?php
session_start();

// Connect to the database   
include 'config.php';

echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : '') . '">';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) { // Is checking if the user is logged in. If not, it transfers the user to login.php
    header('Location: login.php');
    exit;
} else {
    if (!isset($_COOKIE['user_activity'])) { // Creates a user_activity cookie
        setcookie('user_activity', 1, time() + 3600);
    }
    $usernm = $_SESSION['username'];
    // Get the player ID from the session
    $playerId = $_SESSION['player_id'];
    echo '<input type="hidden" id="global_playerid" value="' . (isset($_SESSION['player_id']) && $_SESSION['player_id'] ? $_SESSION['player_id'] : '') . '">';
    // Determine the game type based on user input or default
    $gameType = '2p';

    if(isset($_SESSION['game_id']) && $_SESSION['game_id']){  //Checks if the game is aborted. If so, transfers the user to rules.php
        $gameId = $_SESSION['game_id'];
        echo '<input type="hidden" id="global_gameid" value="' . (isset($_SESSION['game_id']) && $_SESSION['game_id'] ? $_SESSION['game_id'] : '') . '">';

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

    // Check if the player is already in a game
    $sql = "SELECT * FROM game AS g INNER JOIN game_status AS gs ON gs.game_id = g.game_id WHERE (g.player1_id = '".$playerId."' AND gs.gstatus = 'not active') OR (g.player2_id IS NULL AND g.game_type = '".$gameType."' AND gs.gstatus = 'not active')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $sql2 = "SELECT * FROM game AS g INNER JOIN game_status AS gs ON gs.game_id = g.game_id WHERE (g.player2_id = '".$playerId."' AND gs.gstatus = 'not active')";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    
        if ($row['player1_id'] == $playerId) {
            // Player is already in a game, redirect to the game page
            $gameId = $row['game_id'];
            $_SESSION['game_id'] = $gameId;
            $_SESSION['player1_id'] = $row['player1_id'];
            $_SESSION['player2_id'] = $row['player2_id'];
            
            // header("Location: lobby.php?game_id=$gameId");
            // exit;
        } else {
            // Join an existing game
            $gameId = $row['game_id'];
            $_SESSION['game_id'] = $gameId;
            $_SESSION['player1_id'] = $row['player1_id'];
            $_SESSION['player2_id'] = $playerId;
            $sql = "UPDATE game SET player2_id = '" . $playerId . "' WHERE game_id = '" . $gameId . "'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            // header("Location: lobby.php?game_id=$gameId");
            // exit;
        }
        echo '<input type="hidden" id="global_gameid" value="' . $gameId . '">';
        echo '<input type="hidden" id="player1id_hidden" value="' . $row['player1_id'] . '">';
    } else if ($result->num_rows <= 0 && $result2->num_rows > 0){
        $row = $result2->fetch_assoc();
        // Join an existing game
        $gameId = $row['game_id'];
        $_SESSION['game_id'] = $gameId;
        $_SESSION['player1_id'] = $row['player1_id'];
        $_SESSION['player2_id'] = $playerId;
        echo '<input type="hidden" id="global_gameid" value="' . $gameId . '">';
        echo '<input type="hidden" id="player1id_hidden" value="' . $row['player1_id'] . '">';
    } else {
        // Insert a new game into the database
        // $sql = "CALL insert_game('".$playerId."',".$gameType.", 0, '')";
        $sql = "INSERT INTO game (player1_id, game_type) VALUES ('".$playerId."','".$gameType."')";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {

            // Get the ID of the newly inserted game
            $gameId = mysqli_insert_id($conn);

            $_SESSION['game_id'] = $gameId;
            $_SESSION['player1_id'] = $playerId;
            $_SESSION['player2_id'] = '';
            echo '<input type="hidden" id="global_gameid" value="' . $gameId . '">';
            echo '<input type="hidden" id="player1id_hidden" value="' . (isset($_SESSION['player1_id']) && $_SESSION['player1_id'] ? $_SESSION['player1_id'] : '') . '">';

            // Insert a new game status record
            $sql = "INSERT INTO game_status (game_id, gstatus) VALUES ('" . $gameId . "', 'not active')";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

        } else {
            die('Error: ' . mysqli_error($conn));
        }

    }

    // Determine the current player's role
    $currentPlayerRole = $_SESSION['player1_id'] == $playerId ? 'player1' : 'player2';

}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>
    <body>

    <div class="lobby main-content">
        <h2>Lobby</h2>
        <p><i>Your Game ID: <span id="game-id"><?php echo $_SESSION['game_id']; ?></span></i></p>
        <h3>Players</h3>
        <ul id="player-list">
            <li id="player1">
                <p><b>Player 1 -> </b><?php echo $_SESSION['player1_id']; ?><?php echo $currentPlayerRole == 'player1' ? '  <b>(You are player 1)</b>' : ''; ?></p> 
                <p><b>Player 1 Colors -> </b><span id="player1-choice"></span></p>
                <select id="player1-color" <?php echo $currentPlayerRole == 'player1' ? '' : 'disabled'; ?>>
                    <option class="color-button" value="r" data-display-value="Red">Red</option>
                    <option class="color-button" value="b" data-display-value="Blue">Blue</option>
                    <option class="color-button" value="g" data-display-value="Green">Green</option>
                    <option class="color-button" value="y" data-display-value="Yellow">Yellow</option>  
                </select>  
                <select id="player3-color" <?php echo $currentPlayerRole == 'player1' ? '' : 'disabled'; ?>>
                    <option value="r">Red</option>
                    <option value="b">Blue</option>
                    <option value="g">Green</option>
                    <option value="y">Yellow</option>  
                </select> 
                <!-- <div class="color-container">
                    <span>Color 1:</span>
                    <div class="color-buttons">
                        <button value="r" class="color-button" data-color="r"></button>
                        <button value="b" class="color-button" data-color="b"></button>
                        <button value="g" class="color-button" data-color="g"></button>
                        <button value="y" class="color-button" data-color="y"></button>
                    </div>
                </div>
                <div class="color-container">
                    <span>Color 2:</span>
                    <div class="color-buttons">
                        <button value="r" class="color-button" data-color="r"></button>
                        <button value="b" class="color-button" data-color="b"></button>
                        <button value="g" class="color-button" data-color="g"></button>
                        <button value="y" class="color-button" data-color="y"></button>
                    </div>
                </div> -->
                <button id="player1-ready" <?php echo $currentPlayerRole == 'player1' ? '' : 'disabled'; ?> >Ready</button>
            </li>
            <li id="player2">
                <p><b>Player 2 -> </b><?php echo $_SESSION['player2_id']; ?> <?php echo $currentPlayerRole == 'player2' ? '  <b>(You are player 2)</b>' : ''; ?></p>
                <p><b>Player 2 Colors -> </b><span id="player2-choice"></span></p>
                <select id="player2-color" <?php echo $currentPlayerRole == 'player2' ? '' : 'disabled'; ?>>
                    <option value="r">Red</option>
                    <option value="b">Blue</option>
                    <option value="g">Green</option>
                    <option value="y">Yellow</option>  
                </select>
                <select id="player4-color" <?php echo $currentPlayerRole == 'player2' ? '' : 'disabled'; ?>>
                    <option value="r">Red</option>
                    <option value="b">Blue</option>
                    <option value="g">Green</option>
                    <option value="y">Yellow</option>  
                </select>
                <button id="player2-ready" <?php echo $currentPlayerRole == 'player2' ? '' : 'disabled'; ?>>Ready</button>
            </li>
        </ul>

        <button id="startGameButton" disabled>Start Game</button>
        <button id="rules-btn" onclick="document.location='index.php'">Return Home</button>
    </div>
    
    <?php include 'footer.php'; ?>
    </body>
</html>