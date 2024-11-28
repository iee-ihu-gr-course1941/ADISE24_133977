<?php
session_start();

echo '<input type="hidden" id="usernm" value="' . (isset($_SESSION['username']) && $_SESSION['username'] ? $_SESSION['username'] : '') . '">';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
} else {
    if (isset($_COOKIE['user_activity']) || $_COOKIE['user_activity']){
        setcookie('user_activity', 1, time() + 3600);
    }
}
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>


    
    <?php include 'footer.php'; ?>
    </body>
</html>