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
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body>
<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            
            <h1 class="text-2xl font-bold text-black-700 flex items-center gap-2">
                <i class="fas fa-images text-black-1000 "></i>
                My HarvsGallery
            </h1>

            <!-- Mobile toggle + menu must be together -->
            <div class="md:hidden relative">
                <input type="checkbox" id="menu-toggle" class="hidden peer" />

                <label for="menu-toggle" class="cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </label>

                <!-- Mobile menu -->
                <nav id="menu" class="absolute right-0 mt-3 bg-white shadow-lg rounded-lg
                    hidden peer-checked:block px-4 py-3 text-gray-600">
                    
                    <div class="flex items-center gap-2 py-2">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span class="font-medium">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                    </div>

                    <a href="logout.php" class="flex items-center gap-2 font-semibold hover:text-red-500 py-2">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Desktop menu -->
            <nav class="hidden md:flex md:items-center md:gap-8 text-black-600">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-circle text-2xl"></i>
                    <span class="font-medium">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>

                <a href="logout.php"
                    class="flex items-center gap-2 font-semibold hover:text-red-500">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>

        </div>
    </div>
</header>


    <div class="container">
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
            <h1 class="text-2xl font-bold text-black-700">Your Media</h1>
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