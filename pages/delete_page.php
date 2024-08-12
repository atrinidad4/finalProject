<?php

require_once '../auth.php'; 
include_once __DIR__ . '/includes/db_connect.php';

check_login();
$page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$page_id) {
    header('Location: manage_pages.php?error=Invalid page ID');
    exit;
}

$stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
if ($stmt->execute([$page_id])) {
    header('Location: manage_pages.php?success=Page deleted successfully');
} else {
    header('Location: manage_pages.php?error=Failed to delete page');
}
?>
