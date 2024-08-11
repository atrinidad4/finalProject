<?php
// edit_category.php

require_once '../auth.php'; // Include authentication script
include_once __DIR__ . '/includes/db_connect.php';

// Check if user is logged in
check_login();

// Get category ID from URL
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$category_id) {
    header('Location: manage_categories.php?error=Invalid category ID');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = sanitize_input($_POST['category_name']);
    
    if (empty($category_name)) {
        $error = "Category name cannot be empty.";
    } else {
        // Update category in the database using PDO
        $stmt = $db->prepare("UPDATE categories SET name = ? WHERE id = ?");
        if ($stmt->execute([$category_name, $category_id])) {
            header('Location: manage_categories.php?success=Category updated successfully');
            exit;
        } else {
            $error = "Failed to update category.";
        }
    }
}

// Fetch category details from the database
$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: manage_categories.php?error=Category not found');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
</head>
<body>
    <h1>Edit Category</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="edit_category.php?id=<?php echo htmlspecialchars($category_id); ?>" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        <button type="submit">Update Category</button>
    </form>
    
    <a href="manage_categories.php">Back to Categories</a>
</body>
</html>
