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
        $stmt = $db->prepare("SELECT comment FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $comment = $stmt->fetchColumn();

        if ($comment !== false) {
            $disemvoweled_comment = preg_replace('/[aeiouAEIOU]/', '', $comment);
            $stmt = $db->prepare("UPDATE comments SET comment = ? WHERE id = ?");
            if ($stmt->execute([$disemvoweled_comment, $id])) {
                header('Location: moderate_comments.php?success=Comment disemvoweled successfully');
            } else {
                header('Location: moderate_comments.php?error=Failed to disemvowel comment');
            }
        } else {
            header('Location: moderate_comments.php?error=Comment not found');
        }
    } catch (PDOException $e) {
        header('Location: moderate_comments.php?error=Database error: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comments.php?error=Invalid comment ID');
}
?>
