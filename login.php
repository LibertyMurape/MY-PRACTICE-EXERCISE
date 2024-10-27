<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email']; // Assuming you have an email column
            $_SESSION['role'] = $user['role']; // Assuming you have a role column

            // Set a cookie for the user email, valid for 1 day
            setcookie('user_email', $user['email'], time() + (86400 * 1), "/"); // 86400 = 1 day

            // Redirect to the profile page or based on user role
            if ($user['role'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: profile.php");
            }
            exit();
        } else {
            echo "<p>Invalid password.</p>";
        }
    } else {
        echo "<p>Username not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p> <!-- Link to the registration page -->
    </div>
</body>

</html>