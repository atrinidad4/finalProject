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
        $stmt = $db->prepare("SELECT comment FROM comments WHERE id = :id");
        $stmt->execute([':id' => $comment_id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment) {
            $disemvoweled_comment = preg_replace('/[aeiou]/i', '', $comment['comment']);
            $stmt = $db->prepare("UPDATE comments SET comment = :comment WHERE id = :id");
            $stmt->execute([
                ':comment' => $disemvoweled_comment,
                ':id' => $comment_id
            ]);
            header('Location: moderate_comment.php?success=Comment disemvoweled successfully');
        } else {
            header('Location: moderate_comment.php?error=Comment not found');
        }
    } catch (PDOException $e) {
        header('Location: moderate_comment.php?error=Error disemvoweled comment: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comment.php?error=No comment ID specified');
}

?>
