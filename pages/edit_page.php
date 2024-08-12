<?php
require_once '../includes/auth.php'; 
include '../includes/db_connect.php';

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
check_login();

$page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$page_id) {
    header('Location: manage_pages.php?error=Invalid page ID');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $content = sanitize_input($_POST['content']);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $imagePath = null;
    $deleteImage = isset($_POST['delete_image']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imagePath = $uploadFile;
        } else {
            $error = "Failed to upload image. Check directory permissions.";
        }
    } elseif ($deleteImage) {

        $imagePath = null;
    } else {

        $imagePath = filter_input(INPUT_POST, 'existing_image', FILTER_SANITIZE_URL);
    }

    if (empty($title) || empty($content)) {
        $error = "Title and content cannot be empty.";
    } elseif (isset($error)) {

        $error = "Error: $error";
    } else {

        $stmt = $db->prepare("UPDATE pages SET title = ?, content = ?, category_id = ?, image_path = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$title, $content, $category_id, $imagePath, $page_id])) {
            header('Location: manage_pages.php?success=Page updated successfully');
            exit;
        } else {
            $error = "Failed to update page.";
        }
    }
}

$stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$page_id]);
$page = $stmt->fetch();

if (!$page) {
    header('Location: manage_pages.php?error=Page not found');
    exit;
}
$categories_stmt = $db->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css"> 
    <title>Edit Page</title>
</head>
<body>
    <h1>Edit Page</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="edit_page.php?id=<?php echo htmlspecialchars($page_id); ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($page['title']); ?>" required>
        
        <label for="content">Content:</label>
        <textarea name="content" id="content" rows="5" required><?php echo htmlspecialchars($page['content']); ?></textarea>
        
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php if ($category['id'] == $page['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="image">Upload New Image:</label>
        <input type="file" name="image" id="image" accept="image/*">
        
        <?php if ($page['image_path']) : ?>
            <p>Current Image: <img src="<?php echo htmlspecialchars($page['image_path']); ?>" alt="Page Image" style="max-width: 200px; height: auto;"></p>
            <label for="delete_image">Delete Existing Image:</label>
            <input type="checkbox" name="delete_image" id="delete_image">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($page['image_path']); ?>">
        <?php endif; ?>
        
        <button type="submit">Update Page</button>
    </form>
    
    <a href="manage_pages.php">Back to Pages</a>
</body>
</html>
