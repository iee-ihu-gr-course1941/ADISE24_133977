<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "blokusdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection   
if ($conn->connect_error) {
    
    echo "<script>console.log($conn->connect_error);</script>";
    die("Connection failed: " . $conn->connect_error);
} 
