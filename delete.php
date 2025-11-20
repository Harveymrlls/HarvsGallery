<?php
session_start();
require 'config.php';

// Set header for JSON response
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'No file ID provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$file_id = intval($_POST['id']); // Sanitize input

try {
    // Get file info before deleting
    $stmt = $pdo->prepare("SELECT filepath FROM uploads WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        // Delete file from filesystem
        $file_path = $file['filepath'];
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                error_log("Failed to delete file: " . $file_path);
                echo json_encode(['success' => false, 'error' => 'Could not delete file from server']);
                exit;
            }
        }
        
        // Delete record from database
        $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$file_id, $user_id]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'File not found in database']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File not found or you do not have permission']);
    }
} catch (PDOException $e) {
    error_log("Database error in delete.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error in delete.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
}
?>