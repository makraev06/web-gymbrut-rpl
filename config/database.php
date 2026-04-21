<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'db_gymbrut';

$conn = @mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    $conn = null; // graceful fallback for UI preview without DB
}

function gymbrut_query_all($conn, $sql, $fallback = [])
{
    if (!$conn)
        return $fallback;
    $result = mysqli_query($conn, $sql);
    if (!$result)
        return $fallback;
    $rows = [];
    while ($row = mysqli_fetch_assoc($result))
        $rows[] = $row;
    return $rows;
}

function gymbrut_query_one($conn, $sql, $fallback = [])
{
    if (!$conn)
        return $fallback;
    $result = mysqli_query($conn, $sql);
    if (!$result)
        return $fallback;
    $row = mysqli_fetch_assoc($result);
    return $row ?: $fallback;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
