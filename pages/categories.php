<?php
include_once __DIR__ . '/includes/db_connect.php';
require_once '../auth.php'; 

check_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

    if (!empty($category_name)) {
        $query = "INSERT INTO categories (name) VALUES (:category_name)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $success_message = "Category created successfully.";
        } else {
            $error_message = "Failed to create category.";
        }
    } else {
        $error_message = "Category name cannot be empty.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

    if (!empty($category_name) && is_numeric($category_id)) {
        $query = "UPDATE categories SET name = :category_name WHERE id = :category_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $success_message = "Category updated successfully.";
        } else {
            $error_message = "Failed to update category.";
        }
    } else {
        $error_message = "Invalid category data.";
    }
}

if (isset($_GET['delete_id'])) {
    $category_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);

    if (is_numeric($category_id)) {
        $query = "DELETE FROM categories WHERE id = :category_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $success_message = "Category deleted successfully.";
        } else {
            $error_message = "Failed to delete category.";
        }
    }
}

$query = "SELECT * FROM categories";
$stmt = $db->query($query);
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

    <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

    <h2>Create Category</h2>
    <form action="categories.php" method="post">
        <input type="hidden" name="action" value="create">
        <label for="category_name">Category Name:</label><br>
        <input type="text" id="category_name" name="category_name" required><br>
        <input type="submit" value="Create Category">
    </form>

    <h2>Update Category</h2>
    <form action="categories.php" method="post">
        <input type="hidden" name="action" value="update">
        <label for="category_id">Category ID:</label><br>
        <input type="text" id="category_id" name="category_id" required><br>
        <label for="update_name">New Category Name:</label><br>
        <input type="text" id="update_name" name="category_name" required><br>
        <input type="submit" value="Update Category">
    </form>

    <h2>Categories List</h2>
    <ul>
        <?php foreach ($categories as $category) : ?>
            <li>
                <?php echo htmlspecialchars($category['name']); ?>
                <a href="categories.php?delete_id=<?php echo htmlspecialchars($category['id']); ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="../index.php">Back to Home</a>
</body>
</html>
