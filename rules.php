<?php
session_start();

echo '<input type="hidden" id="isLoggedIn" value="' . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false') . '">';
echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : NULL) . '">';
echo '<input type="hidden" id="gstatus" value="' . (isset($_SESSION['game_status']) && $_SESSION['game_status'] ? $_SESSION['game_status'] : NULL) . '">';

?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>

    <body onload="checkSession()">
        <div class="title">
            <h1>
                Blokus Game Rules
            </h1>
        </div>
        <div class="main-content">
            <div id="game-rules">
                <h3>How to play Blokus</h3>
                <br>
                <p><b>Distribute Pieces:</b> Each player receives a set of 21 colored pieces. Each set is a unique color.</p>
                <br>
                <p><b>Total number of colored pieces:</b> 84 </p>
                <br>
                <p><b>Available Playable Colors:</b> <span style="color:yellow;background:grey;">yellow</span>, <span style="color:red">red</span>, <span style="color:green">green</span>, <span style="color:blue">blue</span></p>
                <br>
                <p>In this version of the game, it's a 2-player game.</p>
                <p>In a two-player game, <u>each player</u> takes <u>two colors</u> and plays them as if they were two separate players. </p>
                <p>This adds a layer of strategic complexity as you have to balance the needs of both your colors.</p>
                <br>
                <h3>Gameplay:</h3>
                <ul class="inner-box">
                    <li><b>First Move:</b> The first player places one of their pieces in a corner square of the board.</li>
                    <li><b>Subsequent Turns:</b></li> 
                    <ul>
                        <li>Each player, in turn, places one of their pieces on the board.</li>
                        <li><span style="color:red;font-weight:700;">The Key Rule:</span> A newly placed piece must touch at least one piece of the same color, but only at the corners.</li>
                    </ul>
                    <li ><b>Passing:</b> If a player cannot place a piece, they must pass their turn.</li>
                    <li ><b>Game End:</b> The game ends when a player cannot place any more pieces.</li>
                </ul>
                <br>
                <h3>Scoring:</h3>
                <p>The player with the fewest pieces remaining at the end of the game wins.</p>
                <br>
                <h3>Additional Notes:</h3>
                <br>
                <p><b>No Side-to-Side Contact:</b> Pieces of the same color cannot touch each other along their sides.</p>
                <br>
                <p><b>Different Color Contact:</b> Pieces of different colors can touch each other along their sides or corners.</p>
                <br>
            </div>
            <br>
            <span id="hidden-abborted" style="color:red"></span>
            <p id="userStatus"></p>
            <div id="login-bar">
                <button id="logout-btn" style="display: none;" onclick="document.location = 'logout.php'">Logout</button>
            </div>
            <button id="rules-btn" onclick="document.location='lobby.php'">Start Game</button>
            <button id="rules-btn" onclick="document.location='index.php'">Return Home</button>
        </div>

        <script>
            if (document.getElementById('gstatus').value == 'aborted'){
                document.getElementById('hidden-abborted').textContent = 'Game was aborted by another player. You must relogin to join in a new game (This is temporary step).';
            }
        </script>

        <?php include 'footer.php'; ?>
    </body>
</html>
