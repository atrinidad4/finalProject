<?php
// content_list.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_connect.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$category_condition = $category_id > 0 ? "WHERE category_id = :category_id" : '';

// Fetch posts with category filtering
try {
    $query = "SELECT id, title, content, image_path, created_at, updated_at FROM pages $category_condition ORDER BY created_at DESC";
    $stmt = $db->prepare($query);

    if ($category_id > 0) {
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching posts: ' . $e->getMessage();
}

// Fetch categories
try {
    $stmt = $db->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching categories: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="category-filter">
            <h2>Filter by Category</h2>
            <form method="get" action="content_list.php">
                <label for="category">Category:</label>
                <select id="category" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php if ($categories): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo $category_id === intval($category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>
        </section>

        <section class="posts">
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if ($posts): ?>
                <ul>
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <?php if ($post['image_path']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                            <p><em>Created on: <?php echo htmlspecialchars($post['created_at']); ?> | Updated on: <?php echo htmlspecialchars($post['updated_at']); ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No posts available for this category.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
