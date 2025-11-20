<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "Access denied.";
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT filename, filepath FROM uploads WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$upload = $stmt->fetch();

if ($upload) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $upload['filename'] . '"');
    readfile($upload['filepath']);
} else {
    echo "File not found.";
}
?>