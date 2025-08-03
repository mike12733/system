<?php
require_once 'includes/functions.php';

// Redirect to dashboard if already logged in, otherwise go to login
if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>