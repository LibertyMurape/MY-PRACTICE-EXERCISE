<?php
session_start();
include 'connect.php';

$error_message = ""; // Initialize an error message variable

// Handle form submission
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // New role field

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match."; // Set the error message
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check for existing username or email
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username or Email already exists."; // Set the error message
        } else {
            // Insert the user with the role
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login on success
                exit();
            } else {
                $error_message = "Error: " . $stmt->error; // Set error message for DB error
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form method="post" action="" class="p-4 shadow rounded bg-white" style="max-width: 400px;">
            <h1>Register</h1>
            <div class="mb-3">
                <input type="text" name="username" placeholder="Username" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" placeholder="Email" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role">Select Role:</label>
                <select name="role" class="form-control" required>
                    <option value="General User">General User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <!-- Display the error message here -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>