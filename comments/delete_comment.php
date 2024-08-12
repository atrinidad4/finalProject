<?php

require_once '../auth.php';
include_once __DIR__ . '/includes/db_connect.php';

check_login();

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
        if ($stmt->execute([$id])) {
            header('Location: moderate_comments.php?success=Comment deleted successfully');
        } else {
            header('Location: moderate_comments.php?error=Failed to delete comment');
        }
    } catch (PDOException $e) {
        header('Location: moderate_comments.php?error=Database error: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comments.php?error=Invalid comment ID');
}
?>
