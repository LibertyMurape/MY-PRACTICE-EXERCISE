<?php
session_start();
include 'connect.php';

// Check if the user is logged in using session or cookie
if (!isset($_SESSION['email']) && isset($_COOKIE['user_email'])) {
    $_SESSION['email'] = $_COOKIE['user_email'];
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$success_message = '';
$error_message = '';

// Update email and password if the form is submitted
if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    try {
        if ($new_password) {
            $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE username = ?");
            $stmt->bind_param("ssi", $email, $password_hash, $username);
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE username = ?");
            $stmt->bind_param("ss", $email, $username);
        }

        if ($stmt->execute()) {
            $success_message = 'Profile updated successfully!';
        } else {
            $error_message = 'Failed to update profile. Please try again.';
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Retrieve user information from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p>User information not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

        <!-- Display success or error message -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="email" name="email" placeholder="New Email" required>
            <input type="password" name="new_password" placeholder="New Password (leave blank to keep current)">
            <button type="submit" name="update">Update Profile</button>
        </form>

        <!-- Button to view detailed information -->
        <form method="post" action="view_details.php" style="margin-top: 20px;">
            <button type="submit" name="view_details">View Profile Details</button>
        </form>

        <!-- Role-based access control links -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <a href="admin_users.php" class="admin-link">View All Users</a>
            <a href="admin_dashboard.php" class="admin-link">Admin Dashboard</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>
    </div>
</body>

</html>