<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if the user is logged in and if they are an admin
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft Blocks CMS</title>
    <link rel="stylesheet" href="../path/to/your/styles.css"> <!-- Include your CSS file -->
</head>
<body>
    <header>
        <h1>Directory</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <?php if (!$is_logged_in): ?>
                    <li><a href="/auth/login.php">Login</a></li>
                    <li><a href="/auth/register.php">Register</a></li>
                <?php else: ?>
                    <li><a href="/auth/logout.php">Logout</a></li>
                    <?php if ($is_admin): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
