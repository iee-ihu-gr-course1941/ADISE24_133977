<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>alert('Please login first!');</script>";
    header('Location: login.php');
    exit;
}
?>