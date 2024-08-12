<?php
require_once '../includes/auth.php';
include '../includes/db_connect.php'; 

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $content = sanitize_input($_POST['content']);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/'; 
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        
        $directory = '../images/'; 
        $targetWidth = 300;
        $targetHeight = 300;
        
        $images = glob($directory . "*.{jpg,png,gif}", GLOB_BRACE);
        
    }

    if (empty($title) || empty($content)) {
        $error = "Title and content cannot be empty.";
    } elseif (isset($error)) {

        $error = "Error: $error";
    } else {

        $stmt = $db->prepare("INSERT INTO pages (title, content, category_id, image_path, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        if ($stmt->execute([$title, $content, $category_id, $imagePath])) {
            header('Location: manage_pages.php?success=Page created successfully');
            exit;
        } else {
            $error = "Failed to create page.";
        }
    }
}
$categories_stmt = $db->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Create Page</title>
    <link rel="stylesheet" href="../styles.css"> 
</head>
<body>
    <h1>Create Page</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="create_page.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        
        <label for="content">Content:</label>
        <textarea name="content" id="content" rows="5" required></textarea>
        
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*">
        
        <button type="submit">Create Page</button>
    </form>
    
    <a href="manage_pages.php">Back to Pages</a>
</body>
</html>
