<?php
session_start();
include '../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM workouts WHERE workout_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: workouts.php");
exit;