<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$stmt = $conn->prepare("DELETE FROM user_library WHERE user_id = ? AND audiobook_id = ?");
$stmt->execute([$_SESSION['user_id'], $_GET['id']]);

echo "<script>window.location.href='dashboard.php';</script>";
?>
