<?php
require_once '../auth.php'; 
include_once __DIR__ . '/includes/db_connect.php';

check_login();

$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$comment_id) {
    header('Location: moderate_comments.php?error=Invalid comment ID');
    exit;
}

$stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
if ($stmt->execute([$comment_id])) {
    header('Location: moderate_comments.php?success=Comment deleted successfully');
} else {
    header('Location: moderate_comments.php?error=Failed to delete comment');
}
?>
