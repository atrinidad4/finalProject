<?php
require_once '../auth.php'; 
include_once __DIR__ . '/includes/db_connect.php';

check_login();

$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$category_id) {
    header('Location: manage_categories.php?error=Invalid category ID');
    exit;
}

$stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
if ($stmt->execute([$category_id])) {
    header('Location: manage_categories.php?success=Category deleted successfully');
} else {
    header('Location: manage_categories.php?error=Failed to delete category');
}
?>
