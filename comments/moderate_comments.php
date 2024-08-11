<?php
// moderate_comments.php

require_once '../auth.php'; // Include authentication script
include_once __DIR__ . '/includes/db_connect.php';

// Check if user is an admin
check_login();

// Fetch all comments from the database using PDO
$stmt = $db->query("SELECT * FROM comments");
$comments = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderate Comments</title>
</head>
<body>
    <h1>Moderate Comments</h1>
    
    <?php if (isset($_GET['success'])) echo "<p style='color:green;'>{$_GET['success']}</p>"; ?>
    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>{$_GET['error']}</p>"; ?>
    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Post ID</th>
                <th>Comment</th>
                <th>Created At</th>
                <th>Visible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($comment['id']); ?></td>
                    <td><?php echo htmlspecialchars($comment['post_id']); ?></td>
                    <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                    <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                    <td><?php echo $comment['is_visible'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="toggle_visibility.php?id=<?php echo htmlspecialchars($comment['id']); ?>">Toggle Visibility</a>
                        <a href="delete_comment.php?id=<?php echo htmlspecialchars($comment['id']); ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="../index.php">Back to Home</a>
</body>
</html>
