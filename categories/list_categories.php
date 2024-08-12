<?php
require 'includes/db_connect.php'; 

try {
    $stmt = $db->query("SELECT id, name, description FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
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
    <?php include 'includes/header.php'; ?> 
    
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
    
    <?php include 'includes/footer.php'; ?> 
</body>
</html>
