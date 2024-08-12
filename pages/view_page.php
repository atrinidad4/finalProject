<?php

include_once '../includes/db_connect.php';

if (!$db) {
    die('Database connection failed.');
}

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
