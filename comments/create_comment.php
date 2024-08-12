<?php

require_once 'includes/db_connect.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $post_id = intval($_POST['post_id']);
    $comment = trim($_POST['comment']);
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;

    if (empty($comment)) {
        header('Location: index.php?error=Comment cannot be empty.');
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, comment, created_at, visible) VALUES (:post_id, :user_id, :comment, NOW(), 1)");
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            ':comment' => $comment
        ]);

        header('Location: index.php?success=Comment submitted successfully!');
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?error=Error submitting comment: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
