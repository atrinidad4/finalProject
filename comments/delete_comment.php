<?php
// delete_comment.php

require_once '../auth.php'; // Include authentication script
include_once __DIR__ . '/includes/db_connect.php';

// Check if user is an admin
check_login();

// Get comment ID from URL
$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$comment_id) {
    header('Location: moderate_comments.php?error=Invalid comment ID');
    exit;
}

// Delete comment from the database using PDO
$stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
if ($stmt->execute([$comment_id])) {
    header('Location: moderate_comments.php?success=Comment deleted successfully');
} else {
    header('Location: moderate_comments.php?error=Failed to delete comment');
}
?>
