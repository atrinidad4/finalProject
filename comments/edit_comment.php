<?php

require_once '../auth.php';
include_once __DIR__ . '/includes/db_connect.php';

check_login();

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_POST['new_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $new_comment = trim($_POST['new_comment']);

    try {
        $stmt = $db->prepare("UPDATE comments SET comment = :new_comment WHERE id = :id");
        $stmt->execute([
            ':new_comment' => $new_comment,
            ':id' => $comment_id
        ]);
        header('Location: moderate_comment.php?success=Comment updated successfully');
    } catch (PDOException $e) {
        header('Location: moderate_comment.php?error=Error updating comment: ' . $e->getMessage());
    }
} elseif (isset($_GET['id'])) {
    $comment_id = intval($_GET['id']);

    try {
        $stmt = $db->prepare("SELECT id, comment FROM comments WHERE id = :id");
        $stmt->execute([':id' => $comment_id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        header('Location: moderate_comment.php?error=Error fetching comment: ' . $e->getMessage());
    }
} else {
    header('Location: moderate_comment.php?error=No comment ID specified');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
</head>
<body>
    <h1>Edit Comment</h1>
    <form action="edit_comment.php" method="post">
        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
        <textarea name="new_comment" rows="4" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <button type="submit">Update Comment</button>
    </form>
    <a href="moderate_comment.php">Back to Moderate Comments</a>
</body>
</html>
