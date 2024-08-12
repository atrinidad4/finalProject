<?php

session_start(); // Ensure the session is started
require_once '../auth.php';
include_once __DIR__ . '/includes/db_connect.php';

check_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    $name = sanitize_input($_POST['name']);  // Field for name
    $comment = sanitize_input($_POST['comment']);
    $captcha = sanitize_input($_POST['captcha']);
    $captcha_session = $_SESSION['captcha'];

    if ($captcha !== $captcha_session) {
        $error = "Incorrect CAPTCHA.";
    } else {
        if (empty($name) || empty($comment)) {
            $error = "Name and comment cannot be empty.";
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO comments (post_id, name, comment, created_at, is_visible) VALUES (?, ?, ?, NOW(), 1)");
                if ($stmt->execute([$post_id, $name, $comment])) {
                    header('Location: view_comments.php?success=Comment added successfully');
                    exit;
                } else {
                    $error = "Failed to add comment.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

$captcha_code = rand(1000, 9999);
$_SESSION['captcha'] = $captcha_code;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
</head>
<body>
    <h1>Add Comment</h1>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="create_comment.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id'], ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <br>
        
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" required></textarea>
        <br>
        
        <label for="captcha">CAPTCHA: <?php echo htmlspecialchars($captcha_code, ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="text" name="captcha" id="captcha" required>
        <button type="submit">Submit</button>
    </form>
    
    <a href="../index.php">Back to Home</a>
</body>
</html>
