<?php
session_start();
require_once '../includes/db_connect.php'; // Adjust path as needed

// Initialize error variable
$error = '';

// Sanitize and validate input
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $username && $password) {
    try {
        // Prepare SQL query
        $stmt = $db->prepare('SELECT id, username, password, role FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store user role
            $_SESSION['logged_in'] = true; // Mark user as logged in

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../dashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../styles.css"> 
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <p><a href="register.php">Register</a></p>
</body>
</html>
