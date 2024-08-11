<?php
// toggle_visibility.php

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

// Fetch current visibility status
$stmt = $db->prepare("SELECT is_visible FROM comments WHERE id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch();

if ($comment) {
    // Toggle visibility
    $new_visibility = $comment['is_visible'] ? 0 : 1;
    $stmt = $db->prepare("UPDATE comments SET is_visible = ? WHERE id = ?");
    if ($stmt->execute([$new_visibility, $comment_id])) {
        header('Location: moderate_comments.php?success=Comment visibility updated');
    } else {
        header('Location: moderate_comments.php?error=Failed to update comment visibility');
    }
} else {
    header('Location: moderate_comments.php?error=Comment not found');
}
?>
