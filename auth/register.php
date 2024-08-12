<?php
session_start();
require_once '../includes/db_connect.php'; 

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $password = isset($_POST['password']) ? sanitize_input($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? sanitize_input($_POST['confirm_password']) : '';

    $error = '';


    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {

        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user_exists = $stmt->fetchColumn();

        if ($user_exists) {
            $error = 'Username already exists';
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            try {
                $stmt->execute([$username, $email, $hashed_password]);
                $_SESSION['success'] = 'Registration successful! You can now log in.';
                header('Location: login.php');
                exit();
            } catch (PDOException $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css"> 
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
