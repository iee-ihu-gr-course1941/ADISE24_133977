<?php
// login.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Connect to the database   
    include 'config.php';

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password_hash"])) {
            $_SESSION['player_id'] = $row["id"]; // Generate a unique ID for the player
            $_SESSION['player_data'] = array(
                'username' => $_POST['username'], //Get player's name
                'score' => $row["score"] //Get player's score
                );
            header("Location: game.php");
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
    <?php include 'header.php' ?>
    <head>Login Page</head>
    <body>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username"><br><br>

            <label for="password">Password:</label>   

            <input type="password" id="password" name="password"><br><br>

            <input type="submit" value="Login">
        </form>

        <?php include 'footer.php' ?>
    </body>
</html>