<?php
// view_page.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../includes/db_connect.php';

if (!$db) {
    die('Database connection failed.');
}

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($page_id > 0) {
    try {
        $stmt = $db->prepare("SELECT title, content, image_path, created_at, updated_at FROM pages WHERE id = :id");
        $stmt->execute([':id' => $page_id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page) {
            echo 'Page not found.';
            exit;
        }
    } catch (PDOException $e) {
        echo 'Error fetching page: ' . $e->getMessage();
        exit;
    }
} else {
    echo 'Invalid page ID.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?></title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <nav>
        <ul>
            <?php if ($is_logged_in): ?>
                <?php if ($is_admin): ?>
                    <li><a href="../dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="../auth/logout.php">Sign Out</a></li>
            <?php else: ?>
                <li><a href="../auth//login.php">Login</a></li>
                <li><a href="..auth/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <article>
            <h1><?php echo htmlspecialchars($page['title']); ?></h1>
            <p><?php echo htmlspecialchars($page['content']); ?></p>
            <?php if (!empty($page['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($page['image_path']); ?>" alt="Image for <?php echo htmlspecialchars($page['title']); ?>" style="max-width: 100%; height: auto;">
            <?php endif; ?>
            <p>Created on: <?php echo htmlspecialchars($page['created_at']); ?></p>
            <p>Updated on: <?php echo htmlspecialchars($page['updated_at']); ?></p>
        </article>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
