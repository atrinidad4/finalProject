<?php

require_once '../includes/auth.php'; 
include '../includes/db_connect.php';

check_login();

$stmt = $db->query("SELECT * FROM pages ORDER BY title");
$pages = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css"> 
    <title>Manage Pages</title>
</head>
<body>
    <h1>Manage Pages</h1>
    
    <?php if (isset($_GET['success'])) echo "<p style='color:green;'>{$_GET['success']}</p>"; ?>
    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>{$_GET['error']}</p>"; ?>
    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($page['id']); ?></td>
                    <td><?php echo htmlspecialchars($page['title']); ?></td>
                    <td><?php 

                        $category_stmt = $db->prepare("SELECT name FROM categories WHERE id = ?");
                        $category_stmt->execute([$page['category_id']]);
                        $category = $category_stmt->fetch();
                        echo htmlspecialchars($category['name'] ?? 'N/A');
                    ?></td>
                    <td>
                        <a href="edit_page.php?id=<?php echo htmlspecialchars($page['id']); ?>">Edit</a>
                        <a href="delete_page.php?id=<?php echo htmlspecialchars($page['id']); ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="create_page.php">Create New Page</a>
    <a href="../index.php">Back to Home</a>
</body>
</html>
