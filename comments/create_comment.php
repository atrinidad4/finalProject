<?php
// create_comment.php

require_once '../auth.php'; // Include authentication script
include_once __DIR__ . '/includes/db_connect.php';

// Check if user is logged in
check_login();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    $comment = sanitize_input($_POST['comment']);
    $captcha = sanitize_input($_POST['captcha']);
    $captcha_session = $_SESSION['captcha'];

    // Validate CAPTCHA
    if ($captcha !== $captcha_session) {
        $error = "Incorrect CAPTCHA.";
    } else {
        if (empty($comment)) {
            $error = "Comment cannot be empty.";
        } else {
            // Insert comment into the database using PDO
            $stmt = $db->prepare("INSERT INTO comments (post_id, comment, created_at, is_visible) VALUES (?, ?, NOW(), 1)");
            if ($stmt->execute([$post_id, $comment])) {
                header('Location: view_comments.php?success=Comment added successfully');
                exit;
            } else {
                $error = "Failed to add comment.";
            }
        }
    }
}

// Generate CAPTCHA
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
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="create_comment.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id']); ?>">
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" required></textarea>
        <br>
        <label for="captcha">CAPTCHA: <?php echo $captcha_code; ?></label>
        <input type="text" name="captcha" id="captcha" required>
        <button type="submit">Submit</button>
    </form>
    
    <a href="../index.php">Back to Home</a>
</body>
</html>
