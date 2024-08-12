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
        $stmt = $db->prepare("UPDATE comments SET is_visible = NOT is_visible WHERE id = ?");
        if ($stmt->execute([$id])) {
            header('Location: moderate_comments.php?success=Visibility toggled successfully');
        } else {
            header('Location: moderate_comments.php?error=Failed to toggle visibility');
        }
    } catch (PDOException $e) {
        header('Location: moderate_comments.php?error=Database error: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comments.php?error=Invalid comment ID');
}
?>
