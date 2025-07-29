<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$audiobook_id = $_GET['id'];
$progress = isset($_GET['progress']) ? $_GET['progress'] : 0;

$stmt = $conn->prepare("INSERT INTO user_library (user_id, audiobook_id, progress) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress = ?");
$stmt->execute([$user_id, $audiobook_id, $progress, $progress]);

if (!isset($_GET['progress'])) {
    echo "<script>window.location.href='dashboard.php';</script>";
}
?>
