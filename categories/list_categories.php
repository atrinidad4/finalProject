<?php
// Include database connection
require 'includes/db_connect.php'; // Adjust the path as necessary

// Fetch categories from the database
try {
    $stmt = $db->query("SELECT id, name, description FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die(); // Stop execution on error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Adjust the path to your CSS file -->
</head>
<body>
    <?php include 'includes/header.php'; ?> <!-- Include the header -->
    
    <main>
        <h1>Category List</h1>
        
        <?php if (!empty($categories)): ?>
            <ul>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($category['name']); ?></strong><br>
                        <span><?php echo htmlspecialchars($category['description']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?> <!-- Include the footer -->
</body>
</html>
