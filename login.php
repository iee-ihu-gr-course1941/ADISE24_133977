<?php
// login.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $user = $_POST["username"];
    $passwd = $_POST["password"];

    // Connect to the database   
    include 'config.php';

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username = '".$user."'";
    var_dump($sql);
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($passwd, $row["passwd"])) {
            $_SESSION['player_id'] = $row["id"]; // Generate a unique ID for the player
            $_SESSION['logged_in'] = 'true';
            $_SESSION['username'] = $user;  //Get player's name
            $_SESSION['score'] = $row["score"]; //Get player's score
            setcookie('user_activity', 1, time() + 3600);
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password
            echo "Incorrect password";
        }
    } else {
        // User not found
        echo 'There is no user with this username. Please try again with correct user or register.';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'header.php' ?>
</head> 
<body>
    <div class="title">
        <h1>Login Page</h1>
    </div>
    <div class="main-content">
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username"><br><br>

            <label for="password">Password:</label>  

            <input type="password" id="password" name="password"><br><br>

            <input type="submit" value="Login">
        </form>

        <button onclick="document.location='index.php'">Return Home</button>
    </div>

    <?php include 'footer.php' ?>
</body>
</html>