<?php
// categories.php

require_once '../auth.php'; // Include authentication script
include_once __DIR__ . '/includes/db_connect.php';
// Check if user is logged in
check_login();

// Fetch all categories from the database
$result = $db->query("SELECT * FROM categories ORDER BY category_name ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
</head>
<body>
    <h1>Manage Categories</h1>
    
    <?php if (isset($_GET['success'])) echo "<p style='color:green;'>{$_GET['success']}</p>"; ?>
    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>{$_GET['error']}</p>"; ?>
    
    <a href="create_category.php">Create New Category</a>
    
    <h2>Existing Categories</h2>
    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($category = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td>
                        <a href="edit_category.php?id=<?php echo $category['id']; ?>">Edit</a>
                        <a href="delete_category.php?id=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="../dashboard.php">Back to Dashboard</a>
</body>
</html>
