<?php

session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css"> 
    <title>Logged Out</title>
</head>
<body>
    <h1>Logout Successful</h1>
    <p>You have been successfully logged out.</p>
    <a href="login.php">Login Again</a>
    <a href="../index.php">Back to Home</a>
</body>
</html>
