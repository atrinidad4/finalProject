<?php

require_once '../auth.php'; 
include_once __DIR__ . '/includes/db_connect.php';

check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = sanitize_input($_POST['category_name']);
    
    if (empty($category_name)) {
        $error = "Category name cannot be empty.";
    } else {

        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$category_name])) {
            header('Location: manage_categories.php?success=Category created successfully');
            exit;
        } else {
            $error = "Failed to create category.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Category</title>
</head>
<body>
    <h1>Create Category</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="create_category.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" required>
        <button type="submit">Create Category</button>
    </form>
    
    <a href="manage_categories.php">Back to Categories</a>
</body>
</html>
