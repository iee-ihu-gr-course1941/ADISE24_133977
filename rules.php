

<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>

    <body>
        <div>
            <h1>
                Blokus Game Rules
            </h1>
        </div>
        <div>
            <p>Rules!</p>
            <br>
            <p id="userStatus"></p>
            <button id="logout-btn" style="display: none;" onclick="document.location = 'logout.php'">Logout</button>
            <button id="login-btn" onclick="document.location = 'login.php'">Login</button>
            <button id="register-btn" onclick="document.location = 'register.php'">Register</button>
            <br>
            <button onclick="document.location='game.php'">Start Game</button>
        </div>

        <?php
        session_start();

        // ... (login logic)

        // Output the hidden field
        echo '<input type="hidden" id="isLoggedIn" value="' . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false') . '">';
        echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : 'false') . '">';
        ?>

        <script>
            const isLoggedIn = document.getElementById('isLoggedIn').value;
            const UserName = document.getElementById('usernm').value;

            if (isLoggedIn === 'true') {
                document.getElementById('userStatus').textContent = 'Welcome, ' . UserName;
                document.getElementById('logout-btn').style.display = 'block';
                document.getElementById('login-btn').style.display = 'none';
                document.getElementById('register-btn').style.display = 'none';
            } else {
                document.getElementById('login-btn').style.display = 'block';
                document.getElementById('register-btn').style.display = 'block';
            }
        </script>

        <?php include 'footer.php'; ?>
    </body>
</html>
