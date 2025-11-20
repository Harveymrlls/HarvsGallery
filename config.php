<?php
$host = 'sql100.infinityfree.com';
$dbname = 'if0_40396748_gallery_db';
$username = 'if0_40396748';  // Change to your MySQL username
$password = 'harveY2578';      // Change to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>
