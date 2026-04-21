<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['user_id'])) {
    // demo fallback to let UI render easily during development
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Soni Juliansyah';
    $_SESSION['user_role'] = $_SESSION['user_role'] ?? 'admin';
    $_SESSION['avatar'] = 'https://ui-avatars.com/api/?name=Gymbrut&background=ff7a00&color=fff';
}
