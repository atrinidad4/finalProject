<?php
// list_pages.php

require_once '../auth.php'; // Include authentication script (if needed)
include_once __DIR__ . '/includes/db_connect.php';

// Check if user is logged in (if this is an authenticated page)
check_login();

// Get sorting criteria from query parameters (default to 'title')
$sort_by = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_STRING) ?? 'title';
$valid_sort_columns = ['title', 'created_at', 'updated_at'];

if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'title';
}

// Prepare the SQL query with sorting
$query = "SELECT pages.id, pages.title, pages.created_at, pages.updated_at, categories.name AS category_name
          FROM pages
          LEFT JOIN categories ON pages.category_id = categories.id
          ORDER BY pages.$sort_by";

// Execute the query using PDO
$stmt = $db->query($query);
$pages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Pages</title>
</head>
<body>
    <h1>List of Pages</h1>

    <?php if (isset($_GET['success'])) echo "<p style='color:green;'>{$_GET['success']}</p>"; ?>
    <?php if (isset($_GET['error'])) echo "<p style='color:red;'>{$_GET['error']}</p>"; ?>

    <table border="1">
        <thead>
            <tr>
                <th><a href="?sort_by=title">Title</a></th>
                <th><a href="?sort_by=created_at">Created At</a></th>
                <th><a href="?sort_by=updated_at">Updated At</a></th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($page['title']); ?></td>
                    <td><?php echo htmlspecialchars($page['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($page['updated_at']); ?></td>
                    <td><?php echo htmlspecialchars($page['category_name'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="view_page.php?id=<?php echo htmlspecialchars($page['id']); ?>">View</a>
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
