<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /admin/login.php");
        exit;
    }
}

function current_user_role() {
    return $_SESSION['role'] ?? null;
}
?>
