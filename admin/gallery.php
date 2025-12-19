<?php
// ============================================================================
// FILE: admin/gallery.php - Gallery Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/admin_utilities.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle form submissions using standardized system
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Gallery POST request received: " . print_r($_POST, true));
}

$result = handleAdminFormSubmission(
    'gallery',
    ['title', 'category', 'media_type'], // required fields
    ['caption', 'display_order'], // optional fields
    'image' // image field
);

if ($result['action']) {
    error_log("Gallery form result: " . print_r($result, true));
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$galleryImages = $db->fetchAll("SELECT * FROM gallery ORDER BY category, display_order ASC, created_at DESC");

// Function to clean up orphaned files
function cleanOrphanedGalleryFiles($db) {
    $dbImages = $db->fetchAll("SELECT image FROM gallery");
    $dbImageFiles = array_column($dbImages, 'image');
    
    $uploadDir = UPLOAD_PATH;
    if (is_dir($uploadDir)) {
        $files = array_diff(scandir($uploadDir), ['.', '..']);
        $galleryFiles = array_filter($files, function($file) {
            return strpos($file, 'gallery_') === 0;
        });
        
        foreach ($galleryFiles as $file) {
            if (!in_array($file, $dbImageFiles)) {
                $filePath = $uploadDir . $file;
                error_log("Found orphaned gallery file: $file");
                if (unlink($filePath)) {
                    error_log("Deleted orphaned file: $file");
                }
            }
        }
    }
}

// Uncomment the next line to clean orphaned files on page load (for testing)
// cleanOrphanedGalleryFiles($db);

// Add a repair button for admin to fix file mismatches
if (isset($_GET['repair_files']) && $_GET['repair_files'] === 'true') {
    $repairedCount = 0;
    $galleryItems = $db->fetchAll("SELECT id, image FROM gallery");
    
    foreach ($galleryItems as $item) {
        $imagePath = UPLOAD_PATH . $item['image'];
        if (!file_exists($imagePath)) {
            // Try to find a matching file by similar pattern
            $uploadDir = UPLOAD_PATH;
            if (is_dir($uploadDir)) {
                $files = array_diff(scandir($uploadDir), ['.', '..']);
                $galleryFiles = array_filter($files, function($file) {
                    return strpos($file, 'gallery_') === 0;
                });
                
                // Look for orphaned files that might match this record
                foreach ($galleryFiles as $file) {
                    $fileInDb = $db->fetchOne("SELECT id FROM gallery WHERE image = ?", [$file]);
                    if (!$fileInDb) {
                        // This is an orphaned file, associate it with this record
                        $db->update('gallery', ['image' => $file], 'id = ?', [$item['id']]);
                        $repairedCount++;
                        error_log("Repaired gallery item {$item['id']}: changed {$item['image']} to {$file}");
                        break;
                    }
                }
            }
        }
    }
    
    if ($repairedCount > 0) {
        $success = "Repaired $repairedCount gallery items with missing images.";
    } else {
        $success = "No repairs needed - all gallery images are properly linked.";
    }
    
    // Refresh the data after repair
    $galleryImages = $db->fetchAll("SELECT * FROM gallery ORDER BY category, display_order ASC, created_at DESC");
}

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Gallery Management</h1>
    <div>
        <button class="btn btn-primary" onclick="showAddModal()">+ Add New Image</button>
        <a href="?repair_files=true" class="btn btn-secondary" onclick="return confirm('This will attempt to fix image file mismatches. Continue?')" style="margin-left: 10px;">🔧 Repair Files</a>
    </div>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Search and Filter -->
<div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="searchGallery">Search Gallery</label>
            <input type="text" id="searchGallery" placeholder="Search by title, caption..." 
                   onkeyup="filterGallery()" style="margin-bottom: 0;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="filterCategory">Filter by Category</label>
            <select id="filterCategory" onchange="filterGallery()" style="margin-bottom: 0;">
                <option value="">All Categories</option>
                <option value="waste_collection">Waste Collection</option>
                <option value="recycling">Recycling</option>
                <option value="production">Production</option>
                <option value="installation">Installation</option>
                <option value="community">Community</option>
            </select>
        </div>
        <div>
            <button class="btn btn-secondary" onclick="resetGalleryFilters()">Reset Filters</button>
        </div>
    </div>
</div>

<div class="image-grid" id="galleryGrid">
    <?php foreach ($galleryImages as $image): 
        $imagePath = UPLOAD_PATH . $image['image'];
        $imageExists = file_exists($imagePath);
        if (!$imageExists) {
            error_log("Missing image file for gallery ID {$image['id']}: {$image['image']}");
        }
    ?>
    <div class="image-item" 
         data-title="<?php echo strtolower(htmlspecialchars($image['title'])); ?>"
         data-caption="<?php echo strtolower(htmlspecialchars($image['caption'] ?? '')); ?>"
         data-category="<?php echo $image['category']; ?>">
        <?php if ($imageExists): ?>
            <?php 
            $isVideo = isset($image['media_type']) && $image['media_type'] === 'video';
            if ($isVideo): ?>
                <video src="<?php echo UPLOAD_URL . $image['image']; ?>" muted style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;"></video>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,102,0,0.8); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                    <span style="color: white; font-size: 16px; margin-left: 2px;">▶</span>
                </div>
            <?php else: ?>
                <img src="<?php echo UPLOAD_URL . $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
            <?php endif; ?>
        <?php else: ?>
            <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666; border-radius: 5px;">
                <span>❌ Media not found<br><small><?php echo htmlspecialchars($image['image']); ?></small></span>
            </div>
        <?php endif; ?>
        <div class="image-actions">
            <div>
                <strong><?php echo htmlspecialchars($image['title']); ?></strong><br>
                <small><?php echo ucfirst($image['category']); ?> • <?php echo isset($image['media_type']) ? ucfirst($image['media_type']) : 'Image'; ?></small>
                <?php if (!$imageExists): ?>
                    <br><small style="color: red;">File missing</small>
                <?php endif; ?>
            </div>
            <div>
                <button class="btn-icon" onclick='editImage(<?php echo json_encode($image); ?>)' title="Edit">✏️</button>
                <button class="btn-icon" onclick="deleteImage(<?php echo $image['id']; ?>)" title="Delete">🗑️</button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($galleryImages)): ?>
<div style="text-align: center; padding: 4rem; background: white; border-radius: 10px; margin-top: 2rem;">
    <p style="font-size: 1.2rem; color: #666;">No images in gallery yet.</p>
    <button class="btn btn-primary" onclick="showAddModal()" style="margin-top: 1rem;">Add First Image</button>
</div>
<?php endif; ?>

<!-- Add/Edit Modal -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Image</h2>
        
        <form id="imageForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="imageId">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="waste_collection">Waste Collection</option>
                    <option value="recycling">Recycling Process</option>
                    <option value="production">Production</option>
                    <option value="installation">Installation</option>
                    <option value="community">Community Engagement</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="media_type">Media Type *</label>
                <select id="media_type" name="media_type" required onchange="updateMediaInputAccept()">
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="caption">Caption</label>
                <textarea id="caption" name="caption" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" value="0">
            </div>
            
            <div class="form-group">
                <label for="image"><span id="mediaLabel">Image</span> <span id="imageRequired">*</span></label>
                <input type="file" id="image" name="image" accept="image/*,video/*">
                <small class="form-help" id="mediaHelp">Images: JPG, PNG, GIF, WEBP | Videos: MP4, WEBM, OGG. Max size: 50MB</small>
                <div id="imagePreview"></div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Image</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 3% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover {
    color: #FF6600;
}

#imagePreview {
    margin-top: 1rem;
}

#imagePreview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 5px;
}

.form-help {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
    display: block;
}
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Media';
    document.getElementById('formAction').value = 'add';
    document.getElementById('imageForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('image').required = true;
    document.getElementById('imageRequired').textContent = '*';
    document.getElementById('media_type').value = 'image';
    document.getElementById('mediaLabel').textContent = 'Image';
    updateMediaInputAccept();
    document.getElementById('imageModal').style.display = 'block';
}

function editImage(image) {
    console.log('EditImage called with:', image);
    
    const isVideo = image.media_type === 'video';
    document.getElementById('modalTitle').textContent = isVideo ? 'Edit Video' : 'Edit Image';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('imageId').value = image.id;
    document.getElementById('title').value = image.title;
    document.getElementById('category').value = image.category || '';
    document.getElementById('caption').value = image.caption || '';
    document.getElementById('display_order').value = image.display_order;
    document.getElementById('media_type').value = image.media_type || 'image';
    document.getElementById('mediaLabel').textContent = isVideo ? 'Video' : 'Image';
    document.getElementById('image').required = false;
    document.getElementById('imageRequired').textContent = '(optional)';
    
    console.log('Form fields populated, showing modal');
    
    if (isVideo) {
        document.getElementById('imagePreview').innerHTML = 
            `<div style="margin-top: 1rem;">
                <strong>Current Video:</strong><br>
                <video src="<?php echo UPLOAD_URL; ?>${image.image}" controls style="max-width: 300px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;"></video>
                <br><small>Leave media field empty to keep current video, or select a new file to replace it.</small>
             </div>`;
    } else {
        document.getElementById('imagePreview').innerHTML = 
            `<div style="margin-top: 1rem;">
                <strong>Current Image:</strong><br>
                <img src="<?php echo UPLOAD_URL; ?>${image.image}" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">
                <br><small>Leave media field empty to keep current image, or select a new file to replace it.</small>
             </div>`;
    }
    
    updateMediaInputAccept();
    document.getElementById('imageModal').style.display = 'block';
}

function updateMediaInputAccept() {
    const mediaType = document.getElementById('media_type').value;
    const fileInput = document.getElementById('image');
    const mediaLabel = document.getElementById('mediaLabel');
    const mediaHelp = document.getElementById('mediaHelp');
    
    if (mediaType === 'video') {
        fileInput.accept = 'video/*';
        mediaLabel.textContent = 'Video';
        mediaHelp.textContent = 'Supported formats: MP4, WEBM, OGG. Max size: 50MB';
    } else {
        fileInput.accept = 'image/*';
        mediaLabel.textContent = 'Image';
        mediaHelp.textContent = 'Supported formats: JPG, PNG, GIF, WEBP. Max size: 50MB';
    }
}

function deleteImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('imagePreview');
    const mediaType = document.getElementById('media_type').value;
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const formAction = document.getElementById('formAction').value;
            const isVideo = file.type.startsWith('video/');
            
            let previewHTML = '';
            if (isVideo) {
                previewHTML = `<video src="${e.target.result}" controls style="max-width: 300px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;"></video>`;
            } else {
                previewHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">`;
            }
            
            if (formAction === 'edit') {
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        <strong>New ${isVideo ? 'Video' : 'Image'} Preview:</strong><br>
                        ${previewHTML}
                        <br><small style="color: #ff6600;">This will replace the current ${mediaType} when you save.</small>
                    </div>`;
            } else {
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        ${previewHTML}
                    </div>`;
            }
        };
        reader.readAsDataURL(file);
    } else {
        // If file is cleared and we're editing, show the original media again
        const formAction = document.getElementById('formAction').value;
        if (formAction === 'edit') {
            const imageId = document.getElementById('imageId').value;
            const imageItems = document.querySelectorAll('.image-item');
            let originalMedia = null;
            let isOriginalVideo = false;
            
            imageItems.forEach(item => {
                const editButton = item.querySelector('[onclick*="editImage"]');
                if (editButton) {
                    const onclickStr = editButton.getAttribute('onclick');
                    if (onclickStr && onclickStr.includes('"id":' + imageId)) {
                        const video = item.querySelector('video');
                        const img = item.querySelector('img');
                        if (video) {
                            originalMedia = video.src;
                            isOriginalVideo = true;
                        } else if (img) {
                            originalMedia = img.src;
                            isOriginalVideo = false;
                        }
                    }
                }
            });
            
            if (originalMedia) {
                const mediaHTML = isOriginalVideo 
                    ? `<video src="${originalMedia}" controls style="max-width: 300px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;"></video>`
                    : `<img src="${originalMedia}" alt="Current" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">`;
                
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        <strong>Current ${isOriginalVideo ? 'Video' : 'Image'}:</strong><br>
                        ${mediaHTML}
                        <br><small>Leave media field empty to keep current ${isOriginalVideo ? 'video' : 'image'}, or select a new file to replace it.</small>
                    </div>`;
            }
        } else {
            previewDiv.innerHTML = '';
        }
    }
});

window.onclick = function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Add form submission debugging
document.getElementById('imageForm').addEventListener('submit', function(e) {
    console.log('Form submitted with action:', document.getElementById('formAction').value);
    console.log('Form data:', new FormData(this));
});

// Search and Filter Functions
function filterGallery() {
    const searchTerm = document.getElementById('searchGallery').value.toLowerCase();
    const categoryFilter = document.getElementById('filterCategory').value;
    const items = document.querySelectorAll('#galleryGrid .image-item');
    
    items.forEach(item => {
        const title = item.dataset.title || '';
        const caption = item.dataset.caption || '';
        const category = item.dataset.category || '';
        
        const matchesSearch = title.includes(searchTerm) || caption.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        item.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    });
}

function resetGalleryFilters() {
    document.getElementById('searchGallery').value = '';
    document.getElementById('filterCategory').value = '';
    filterGallery();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
