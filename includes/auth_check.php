<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

function requireRole($roles = [])
{
    if (!in_array($_SESSION['role'], $roles)) {
        header("Location: login_process.php");
        exit;
    }
}
?>