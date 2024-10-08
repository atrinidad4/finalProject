<?php
session_start();
include_once __DIR__ . '/includes/db_connect.php';

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_results = [];
$error_message = '';

if (!empty($search_query)) {
    try {
        $stmt = $db->prepare("SELECT id, title FROM pages WHERE title LIKE :query");
        $stmt->execute([':query' => '%' . $search_query . '%']);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = 'Error searching pages: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h1>Search Pages</h1>
        <form action="search.php" method="get" class="search-form">
            <input type="text" name="query" value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit">Search</button>
        </form>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($search_query): ?>
            <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
            <?php if ($search_results): ?>
                <ul>
                    <?php foreach ($search_results as $result): ?>
                        <li><a href="../pages/view_page.php?id=<?php echo htmlspecialchars($result['id']); ?>"><?php echo htmlspecialchars($result['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No results found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
