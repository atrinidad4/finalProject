<?php
include_once __DIR__ . '/includes/db_connect.php';
require '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_id = intval($_POST['page_id']);
    $image = $_FILES['image'];

    if (getimagesize($image['tmp_name'])) {
        $filename = time() . '_' . basename($image['name']);
        $target = '../uploads/' . $filename;
        move_uploaded_file($image['tmp_name'], $target);

        $stmt = $db->prepare("INSERT INTO images (page_id, filename) VALUES (?, ?)");
        $stmt->execute([$page_id, $filename]);

        header('Location: ../pages/view_page.php?id=' . $page_id);
        exit;
    } else {
        echo "Invalid image.";
    }
}
?>
