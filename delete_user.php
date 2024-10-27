<?php
session_start();
include 'connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if a username is provided for deletion
if (!isset($_GET['username'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$username = $_GET['username'];
$error_message = '';
$success_message = '';

// Prepare to delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $success_message = "User '{$username}' deleted successfully.";
} else {
    $error_message = "Failed to delete user '{$username}'.";
}

// Redirect back to the admin dashboard with a success/error message
$_SESSION['success_message'] = $success_message;
$_SESSION['error_message'] = $error_message;
header("Location: admin_dashboard.php");
exit();
