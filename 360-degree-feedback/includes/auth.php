<?php
function isLoggedIn() {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page.";
        header("Location: login.php");
        exit();
    }
}
?>
