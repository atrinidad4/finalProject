<?php
session_start();

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }
}
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

function logout() {
    session_unset();
    session_destroy();
}
?>