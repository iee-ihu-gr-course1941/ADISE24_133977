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

    <button onclick="document.location='index.php'">Return Home</button> 


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
        
        $passwd_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        //SQL Statement to check if the user exists in database
        $sqlUserCheck = "SELECT * FROM users WHERE username = '".$username."'";

        $UsrCheck = $conn->prepare($sqlUserCheck);
        if($UsrCheck){
            $UsrCheck->execute();
            $check = $UsrCheck->get_result();
        }

        if($check->num_rows == 1){
            echo 'Username already exists. Please choose another username.';
            $check->close();
        } else {

            // Prepare the SQL statement with placeholders to prevent SQL injection
            $sql = "INSERT INTO users (firstname, lastname, email, username, passwd, score) VALUES ('".$firstname."','".$lastname."','".$email."','".$username."','".$passwd_hash."',0)";

            // Use var_dump to inspect the values
            var_dump($sql);

            $stmt = $conn->prepare($sql);

            if ($stmt) {
                //$stmt->bind_param("sssssi", $firstname, $lastname, $email, $username, $password, 0); // Bind variables for secure insertion

                if ($stmt->execute()) {
                    echo "Registration successful! You can now login.";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error: Failed to prepare SQL statement.";
            }
        }

        $conn->close();

    }
    ?>
</body>
</html>