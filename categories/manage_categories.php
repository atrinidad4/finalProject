<?php
require_once '../auth.php'; 
include_once __DIR__ . '/includes/db_connect.php';

check_login();

$stmt = $db->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

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
    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <a href="edit_category.php?id=<?php echo htmlspecialchars($category['id']); ?>">Edit</a>
                        <a href="delete_category.php?id=<?php echo htmlspecialchars($category['id']); ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="create_category.php">Create New Category</a>
    <a href="../index.php">Back to Home</a>
</body>
</html>
