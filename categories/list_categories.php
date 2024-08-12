<?php
require '../includes/db_connect.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

try {
    $stmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($category_id > 0) {
        $stmt = $db->prepare("SELECT p.id, p.title, p.content, p.image_path, p.created_at, p.updated_at
                              FROM pages p
                              WHERE p.category_id = :category_id
                              ORDER BY p.created_at DESC");
        $stmt->execute([':category_id' => $category_id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $posts = [];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include '../includes/header.php'; ?> 
    
    <main>
        <h1>Category List</h1>
        
        <form action="list_categories.php" method="get">
            <label for="category">Select Category:</label>
            <select id="category" name="category_id" onchange="this.form.submit()">
                <option value="">-- Select a Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"
                        <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($category_id > 0): ?>
            <?php if (!empty($posts)): ?>
                <h2>Posts in <?php echo htmlspecialchars($categories[array_search($category_id, array_column($categories, 'id'))]['name']); ?></h2>
                <ul>
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Image for <?php echo htmlspecialchars($post['title']); ?>" style="max-width: 100%; height: auto;">
                            <?php endif; ?>
                            <p class="post-date">Created on: <?php echo htmlspecialchars($post['created_at']); ?></p>
                            <p class="post-date">Updated on: <?php echo htmlspecialchars($post['updated_at']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No posts found for this category.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Please select a category to see posts.</p>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?> 
</body>
</html>
