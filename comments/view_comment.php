<?php
// view_comments.php

include_once __DIR__ . '/includes/db_connect.php';

// Get post ID from URL
$post_id = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
if (!$post_id) {
    header('Location: ../index.php?error=Invalid post ID');
    exit;
}

// Fetch comments for the specific post using PDO
$stmt = $db->prepare("SELECT * FROM comments WHERE post_id = ? AND is_visible = 1 ORDER BY created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Comments</title>
</head>
<body>
    <h1>Comments for Post ID <?php echo htmlspecialchars($post_id); ?></h1>
    
    <?php foreach ($comments as $comment) : ?>
        <div>
            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
            <p><small>Posted on <?php echo htmlspecialchars($comment['created_at']); ?></small></p>
        </div>
    <?php endforeach; ?>
    
    <a href="create_comment.php?post_id=<?php echo htmlspecialchars($post_id); ?>">Add Comment</a>
    <a href="../index.php">Back to Home</a>
</body>
</html>
