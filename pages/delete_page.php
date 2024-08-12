<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db_connect.php'; 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM pages WHERE id = :id");
            $stmt->execute([':id' => $id]);

            header('Location: ../index.php'); 
            exit;
        } catch (PDOException $e) {
            echo 'Error deleting page: ' . $e->getMessage();
        }
    } else {
        echo 'Invalid page ID.';
    }
} else {
    echo 'No page ID provided.';
}
