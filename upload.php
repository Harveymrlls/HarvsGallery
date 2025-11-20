<?php
session_start();

// Set PHP configuration for large files
ini_set('upload_max_filesize', '2G');
ini_set('post_max_size', '2G');
ini_set('max_execution_time', '0'); 
ini_set('max_input_time', '0');
ini_set('memory_limit', '1024M');

// For some hosts, you might need to set these too
if (function_exists('set_time_limit')) {
    set_time_limit(0);
}

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['file'];

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = time() . '_' . basename($file['name']);
$targetFile = $uploadDir . $fileName;

// Allowed file types
$allowed = ['jpg','jpeg','png','gif','mp4','mov','avi','mkv'];
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

if (!in_array($fileType, $allowed)) {
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    // Save metadata to DB
    $stmt = $pdo->prepare("INSERT INTO uploads (user_id, filename, filepath, file_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $fileName, $targetFile, $fileType]);

    echo json_encode(['success' => true, 'filename' => $fileName, 'filepath' => $targetFile, 'file_type' => $fileType]);
} else {
    echo json_encode(['error' => 'Failed to upload']);
}
?>
