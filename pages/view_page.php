<?php
require '../includes/auth.php';
include_once __DIR__ . '/includes/db_connect.php';

$page_id = (int)$_GET['id'];
$page = $db->query("SELECT * FROM pages WHERE id = $page_id")->fetch_assoc();
$comments = $db->query("SELECT * FROM comments WHERE page_id = $page_id AND is_visible = 1 ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = trim($_POST['comment']);
    $user_id = is_logged_in() ? $_SESSION['user_id'] : null;
    $stmt = $db->prepare("INSERT INTO comments (page_id, user_id, comment, is_visible) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iis", $page_id, $user_id, $comment);
    $stmt->execute();

    header("Location: view_page.php?id=$page_id");
    exit();
}

include '../includes/header.php';
?>

<h1><?= htmlspecialchars($page['title']) ?></h1>
<p><?= nl2br(htmlspecialchars($page['content'])) ?></p>

<?php if ($page['image_path']): ?>
    <img src="<?= $page['image_path'] ?>" alt="Page Image" style="max-width: 400px;">
<?php endif; ?>

<h2>Comments</h2>
<form method="POST">
    <textarea name="comment" placeholder="Add a comment" required></textarea>
    <button type="submit">Submit Comment</button>
</form>

<ul>
    <?php foreach ($comments as $comment): ?>
        <li><?= htmlspecialchars($comment['comment']) ?> - <?= $comment['created_at'] ?></li>
    <?php endforeach; ?>
</ul>

<?php include '../includes/footer.php'; ?>
