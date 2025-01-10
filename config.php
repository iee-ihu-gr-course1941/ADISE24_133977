<?php
$servername = "users.it.teithe.gr";
$username = "it133977";
$password = "21091995";
$dbname = "blokusdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection   
if ($conn->connect_error) {
    
    echo "<script>console.log($conn->connect_error);</script>";
    die("Connection failed: " . $conn->connect_error);
} 
