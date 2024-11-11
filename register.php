<!DOCTYPE html>
<html>
<head>
    <title>Blokus Registration</title>
</head>
<body>
    <h1>Blokus Registration</h1>
    <form action="register.php" method="post">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br> 


        <input type="submit" value="Register">
    </form>

    <?php
    // Include your database connection configuration
    include 'config.php';

    // Process form submission if data is available
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $passwd_hash = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        // Prepare the SQL statement with placeholders to prevent SQL injection
        $sql = "INSERT INTO users (firstname, lastname, email, username, passwd, score) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssssi", $firstname, $lastname, $email, $username, $passwd_hash, 0); // Bind variables for secure insertion

            if ($stmt->execute()) {
                echo "Registration successful! You can now log in.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error: Failed to prepare SQL statement.";
        }

        $conn->close();
    }
    ?>
</body>
</html>