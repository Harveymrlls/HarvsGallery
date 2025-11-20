<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch uploads with proper data structure
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);
$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map file extensions to MIME types for frontend
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg', 
    'png' => 'image/png',
    'gif' => 'image/gif',
    'mp4' => 'video/mp4',
    'mov' => 'video/quicktime',
    'avi' => 'video/x-msvideo',
    'mkv' => 'video/x-matroska'
];

$response = [];
foreach ($uploads as $upload) {
    $fileType = $upload['file_type'];
    $mimeType = $mimeTypes[$fileType] ?? $fileType;
    
    $response[] = [
        'id' => $upload['id'],
        'filename' => $upload['filename'],
        'original_name' => $upload['original_name'] ?? $upload['filename'],
        'filepath' => $upload['filepath'],
        'file_type' => $mimeType, // Convert to MIME type
        'file_size' => $upload['file_size'] ?? 0,
        'upload_date' => $upload['uploaded_at'] ?? ''
    ];
}

echo json_encode($response);
?>