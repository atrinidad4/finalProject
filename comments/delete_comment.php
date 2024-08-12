<?php

require_once '../auth.php';
include_once __DIR__ . '../includes/db_connect.php';

check_login();

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $comment_id = intval($_GET['id']);

    try {
        $stmt = $db->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->execute([':id' => $comment_id]);
        header('Location: moderate_comment.php?success=Comment deleted successfully');
    } catch (PDOException $e) {
        header('Location: moderate_comment.php?error=Error deleting comment: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comment.php?error=No comment ID specified');
}

?>
