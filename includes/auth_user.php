<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['user', 'member'])) {
    header("Location: ../login.php");
    exit;
}
?>