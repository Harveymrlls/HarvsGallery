const uploadForm = document.getElementById('uploadForm');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const uploadBtn = document.getElementById('uploadBtn');
const gallery = document.getElementById('gallery');
const progressContainer = document.getElementById('progressContainer');
const progressBar = document.getElementById('progressBar');
const progressText = document.getElementById('progressText');
const mediaModal = document.getElementById('mediaModal');
const modalContent = document.getElementById('modalContent');
const closeModal = document.getElementById('closeModal');

// File input change handler
fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        const file = this.files[0];
        const fileSize = (file.size / (1024 * 1024 * 1024)).toFixed(2);
        const fileType = file.type.split('/')[0]; // 'image' or 'video'
        fileInfo.textContent = `Selected: ${file.name} (${fileSize} MB, ${fileType})`;
    } else {
        fileInfo.textContent = '';
    }
});

// Upload form submission
uploadForm.addEventListener('submit', function(e){
    e.preventDefault();

    const file = fileInput.files[0];
    if(!file) {
        alert('Please select a file to upload.');
        return;
    }

    // Check file size (max 10MB)
    if (file.size > 10 * 1024 * 1024 * 1024) {
        alert('File size exceeds 10MB limit. Please choose a smaller file.');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php', true);

    // Disable upload button and show progress
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';

    // Track upload progress
    xhr.upload.addEventListener('progress', function(e){
        if(e.lengthComputable){
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        }
    });

    xhr.onload = function(){
        progressContainer.style.display = 'none';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload File';
        
        if(xhr.status === 200){
            try {
                const data = JSON.parse(xhr.responseText);
                if(data.success){
                    fileInput.value = '';
                    fileInfo.textContent = '';
                    showNotification('File uploaded successfully!', 'success');
                    loadGallery();
                }else{
                    showNotification(data.error || 'Upload failed!', 'error');
                }
            } catch (e) {
                showNotification('Error parsing server response', 'error');
            }
        }else{
            showNotification('Upload failed! Server error.', 'error');
        }
    };

    xhr.onerror = function() {
        progressContainer.style.display = 'none';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload File';
        showNotification('Network error. Please try again.', 'error');
    };

    xhr.send(formData);
});

// Load gallery items
function loadGallery(){
    fetch('gallery_fetch.php')
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        gallery.innerHTML = '';
        
        if (data.length === 0) {
            gallery.innerHTML = `
                <div class="empty-gallery">
                    <i class="fas fa-folder-open"></i>
                    <h3>No media files yet</h3>
                    <p>Upload your first image or video to get started</p>
                </div>
            `;
            return;
        }
        
        data.forEach(item => {
            // Better video detection
            const isVideo = item.file_type.includes('video') || 
                           item.file_type.includes('mp4') || 
                           item.file_type.includes('mov') || 
                           item.file_type.includes('avi') || 
                           item.file_type.includes('mkv') ||
                           item.file_type.includes('webm') ||
                           (item.original_name && (
                               item.original_name.toLowerCase().endsWith('.mp4') ||
                               item.original_name.toLowerCase().endsWith('.mov') ||
                               item.original_name.toLowerCase().endsWith('.avi') ||
                               item.original_name.toLowerCase().endsWith('.mkv') ||
                               item.original_name.toLowerCase().endsWith('.webm')
                           ));
            
            const fileSize = item.file_size ? formatFileSize(item.file_size) : 'Unknown size';
            const fileName = item.original_name || item.filename || 'Unknown file';
            
            let html = `
                <div class="gallery-item" data-id="${item.id || ''}">
                    <div class="media-container">
            `;
            
            if(isVideo) {
                html += `
                    <video preload="metadata" style="width:100%;height:100%;object-fit:cover;">
                        <source src="${item.filepath}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-overlay">
                        <div class="play-button" onclick="openModal('${item.filepath}', 'video')">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <img src="${item.filepath}" alt="${fileName}" onclick="openModal('${item.filepath}', 'image')">
                `;
            }
            
            html += `
                    </div>
                    <div class="item-info">
                        <div class="item-name" title="${fileName}">${fileName}</div>
                        <div class="item-meta">
                            <span>${fileSize}</span>
                            <span>${item.upload_date || ''}</span>
                        </div>
                        <div class="item-actions">
                            <a href="${item.filepath}" download="${fileName}" class="download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <button class="delete-btn" onclick="deleteItem(${item.id || '0'})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            gallery.innerHTML += html;
        });
        
        // Initialize video elements after adding to DOM
        initializeVideos();
    })
    .catch(error => {
        console.error('Error loading gallery:', error);
        gallery.innerHTML = `
            <div class="empty-gallery">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error loading gallery</h3>
                <p>Please try again later</p>
            </div>
        `;
    });
}

// Initialize video elements
function initializeVideos() {
    const videos = document.querySelectorAll('.gallery-item video');
    videos.forEach(video => {
        // Set poster to first frame if needed
        video.addEventListener('loadeddata', function() {
            if (video.readyState >= 2) {
                // Video is loaded enough to show frames
            }
        });
        
        // Prevent modal opening when clicking video controls
        video.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}

// Format file size
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Open modal for full-size view
function openModal(src, type) {
    modalContent.innerHTML = '';
    
    if (type === 'video') {
        modalContent.innerHTML = `
            <video controls autoplay style="width:100%;max-width:800px;">
                <source src="${src}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        `;
    } else {
        modalContent.innerHTML = `<img src="${src}" alt="Full size view" style="width:100%;max-width:800px;">`;
    }
    
    mediaModal.style.display = 'flex';
}

// Close modal
closeModal.addEventListener('click', function() {
    mediaModal.style.display = 'none';
    // Pause any playing video
    const video = modalContent.querySelector('video');
    if (video) {
        video.pause();
    }
});

// Close modal when clicking outside content
mediaModal.addEventListener('click', function(e) {
    if (e.target === mediaModal) {
        mediaModal.style.display = 'none';
        // Pause any playing video
        const video = modalContent.querySelector('video');
        if (video) {
            video.pause();
        }
    }
});

// Delete item function
function deleteItem(id) {
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }
    
    fetch('delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Item deleted successfully!', 'success');
            loadGallery();
        } else {
            showNotification(data.error || 'Failed to delete item', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting item:', error);
        showNotification('Error deleting item', 'error');
    });
}

// Show notification
function showNotification(message, type) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Mobile menu toggle functionality
const menuToggle = document.getElementById('menu-toggle');
const menu = document.getElementById('menu');

menuToggle.addEventListener('change', function() {
    if (this.checked) {
        menu.classList.remove('hidden');
        menu.style.maxHeight = '100vh';
    } else {
        menu.classList.add('hidden');
        menu.style.maxHeight = '0';
    }
});

// Initial load
loadGallery();