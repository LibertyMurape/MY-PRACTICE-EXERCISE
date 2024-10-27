<?php
session_start();
include 'connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if a username is provided
if (!isset($_GET['username'])) {
    echo "No user specified.";
    exit();
}

$username = $_GET['username'];
$error_message = '';
$success_message = '';

// Retrieve user information
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission for updating user info
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE username = ?");
    $stmt->bind_param("sss", $email, $role, $username);

    if ($stmt->execute()) {
        $success_message = "User updated successfully!";
    } else {
        $error_message = "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Edit User: <?php echo htmlspecialchars($username); ?></h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <select name="role">
                <option value="General User" <?php echo $user['role'] === 'General User' ? 'selected' : ''; ?>>General User</option>
                <option value="Admin" <?php echo $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <button type="submit">Update User</button>
        </form>

        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>

</html>