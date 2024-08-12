
<?php
session_start();
include 'includes/db_connect.php';

$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($page_id <= 0) {
    die('Invalid page ID.');
}

try {
    $stmt = $db->prepare("SELECT title, content, image_path FROM pages WHERE id = :page_id");
    $stmt->execute([':page_id' => $page_id]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching page: ' . $e->getMessage();
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
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-view">
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if ($page): ?>
                <h1><?php echo htmlspecialchars($page['title']); ?></h1>
                <div class="content">
                    <p><?php echo nl2br(htmlspecialchars($page['content'])); ?></p>
                    <?php if (!empty($page['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($page['image_path']); ?>" alt="Image for <?php echo htmlspecialchars($page['title']); ?>" style="max-width: 100%; height: auto;">
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>Page not found.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
