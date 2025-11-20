<?php 
session_start(); 
if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit; 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-images"></i> My Gallery</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                 <a href="logout.php" style="font-weight: bold; margin-left: 15px; color: #666; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </header>

        <section class="upload-section">
            <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                <div class="file-input-container">
                    <label for="fileInput" class="file-input-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choose files to upload</span>
                        <small>Supports images and videos (Max: 10MB)</small>
                    </label>
                    <input type="file" id="fileInput" accept="image/*,video/*" required>
                    <div class="file-info" id="fileInfo"></div>
                </div>
                
                <button type="submit" class="upload-btn" id="uploadBtn">
                    <i class="fas fa-upload"></i> Upload File
                </button>
                
                <div id="progressContainer">
                    <div id="progressBar"></div>
                    <div class="progress-text" id="progressText">0%</div>
                </div>
            </form>
        </section>

        <section class="gallery-section">
            <h2>Your Media</h2>
            <div id="gallery" class="gallery">
                <!-- Gallery items will be loaded here -->
            </div>
        </section>
    </div>

    <!-- Modal for full-size media view -->
    <div class="modal" id="mediaModal">
        <button class="close-modal" id="closeModal">&times;</button>
        <div class="modal-content" id="modalContent"></div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>