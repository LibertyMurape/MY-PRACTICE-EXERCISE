<?php
session_start();

// Clear the session
session_unset();
session_destroy();

// Clear the cookie for user email
setcookie('user_email', '', time() - 3600, "/"); // Set cookie to expire in the past

// Redirect to login page
header("Location: login.php");
exit();
