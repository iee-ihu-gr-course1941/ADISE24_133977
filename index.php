<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>

    <body>
        <div class="title">
            <h1>Welcome! Let's Play Blokus!</h1>
        </div>
        <div class="main-content">
            <p>Please log in or create an account to play.</p>
            <p id="userStatus"></p>
            <div id="login-bar">
                <button id="logout-btn" style="display: none;" onclick="document.location = 'logout.php'">Logout</button>
                <button id="login-btn" onclick="document.location = 'login.php'">Login</button>
                <button id="register-btn" onclick="document.location = 'register.php'">Register</button>
            </div>
            <p>Ready to learn the rules? Click below!</p>
            <p><b>Remember, you'll need a login to play!</b></p>
            <br>
            <button onclick="document.location='rules.php'">Game Rules</button>
        </div>

        <?php
        session_start();

        // Output the hidden field
        echo '<input type="hidden" id="isLoggedIn" value="' . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false') . '">';
        echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : NULL) . '">';
        ?>

        <script>
            const isLoggedIn = document.getElementById('isLoggedIn').value;
            const UserName = document.getElementById('usernm').value;

            if (isLoggedIn === 'true') {
                console.log(UserName);
                document.getElementById('userStatus').textContent = 'Welcome, ' + UserName;
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