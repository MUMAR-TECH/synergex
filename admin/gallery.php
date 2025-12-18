<?php
// ============================================================================
// FILE: admin/gallery.php - Gallery Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Temporary debugging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST received: " . print_r($_POST, true));
    error_log("FILES received: " . print_r($_FILES, true));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Invalid security token. Please try again.';
        } else {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $title = sanitizeInput($_POST['title']);
                $category = sanitizeInput($_POST['category']);
                $caption = sanitizeInput($_POST['caption']);
                $displayOrder = intval($_POST['display_order']);
                
                $data = [
                    'title' => $title,
                    'category' => $category,
                    'caption' => $caption,
                    'display_order' => $displayOrder
                ];
                
                // Handle image upload
                $imageUploaded = false;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['image'], 'gallery');
                    if ($upload['success']) {
                        $data['image'] = $upload['filename'];
                        $imageUploaded = true;
                    } else {
                        $error = $upload['message'];
                    }
                } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Handle file upload errors other than no file
                    $uploadErrors = [
                        UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds upload_max_filesize)',
                        UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds MAX_FILE_SIZE)',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
                    ];
                    $error = $uploadErrors[$_FILES['image']['error']] ?? 'Unknown upload error';
                }
                
                if (empty($error)) {
                    if ($_POST['action'] === 'add') {
                        if (!empty($data['image'])) {
                            $db->insert('gallery', $data);
                            $success = 'Image added successfully';
                        } else {
                            $error = 'Please upload an image';
                        }
                    } else {
                        // Edit action
                        $id = intval($_POST['id']);
                        
                        // If a new image was uploaded, delete the old one
                        if ($imageUploaded) {
                            $oldImage = $db->fetchOne("SELECT image FROM gallery WHERE id = ?", [$id]);
                            if ($oldImage && file_exists(UPLOAD_PATH . $oldImage['image'])) {
                                unlink(UPLOAD_PATH . $oldImage['image']);
                            }
                        } else {
                            // If no new image uploaded, don't update the image field
                            unset($data['image']);
                        }
                        
                        $db->update('gallery', $data, 'id = ?', [$id]);
                        $success = 'Gallery item updated successfully';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $image = $db->fetchOne("SELECT image FROM gallery WHERE id = ?", [$id]);
                if ($image && file_exists(UPLOAD_PATH . $image['image'])) {
                    unlink(UPLOAD_PATH . $image['image']);
                }
                $db->delete('gallery', 'id = ?', [$id]);
                $success = 'Image deleted successfully';
                break;
        }
        }
    }
}

$galleryImages = $db->fetchAll("SELECT * FROM gallery ORDER BY category, display_order ASC, created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Gallery Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Image</button>
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
    <?php foreach ($galleryImages as $image): ?>
    <div class="image-item" 
         data-title="<?php echo strtolower(htmlspecialchars($image['title'])); ?>"
         data-caption="<?php echo strtolower(htmlspecialchars($image['caption'] ?? '')); ?>"
         data-category="<?php echo $image['category']; ?>">
        <img src="<?php echo UPLOAD_URL . $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
        <div class="image-actions">
            <div>
                <strong><?php echo htmlspecialchars($image['title']); ?></strong><br>
                <small><?php echo ucfirst($image['category']); ?></small>
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
                <label for="caption">Caption</label>
                <textarea id="caption" name="caption" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" value="0">
            </div>
            
            <div class="form-group">
                <label for="image">Image <span id="imageRequired">*</span></label>
                <input type="file" id="image" name="image" accept="image/*">
                <small class="form-help">Supported formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB</small>
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
    document.getElementById('modalTitle').textContent = 'Add New Image';
    document.getElementById('formAction').value = 'add';
    document.getElementById('imageForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('image').required = true;
    document.getElementById('imageRequired').style.display = '';
    document.getElementById('imageModal').style.display = 'block';
}

function editImage(image) {
    console.log('EditImage called with:', image);
    
    document.getElementById('modalTitle').textContent = 'Edit Image';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('imageId').value = image.id;
    document.getElementById('title').value = image.title;
    document.getElementById('category').value = image.category || '';
    document.getElementById('caption').value = image.caption || '';
    document.getElementById('display_order').value = image.display_order;
    document.getElementById('image').required = false;
    document.getElementById('imageRequired').style.display = 'none';
    
    console.log('Form fields populated, showing modal');
    
    document.getElementById('imagePreview').innerHTML = 
        `<div style="margin-top: 1rem;">
            <strong>Current Image:</strong><br>
            <img src="<?php echo UPLOAD_URL; ?>${image.image}" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">
            <br><small>Leave image field empty to keep current image, or select a new image to replace it.</small>
         </div>`;
    
    document.getElementById('imageModal').style.display = 'block';
}

function deleteImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
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
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const formAction = document.getElementById('formAction').value;
            if (formAction === 'edit') {
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        <strong>New Image Preview:</strong><br>
                        <img src="${e.target.result}" alt="New image preview" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">
                        <br><small style="color: #ff6600;">This will replace the current image when you save.</small>
                    </div>`;
            } else {
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        <img src="${e.target.result}" alt="Image preview" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                    </div>`;
            }
        };
        reader.readAsDataURL(file);
    } else {
        // If file is cleared and we're editing, show the original image again
        const formAction = document.getElementById('formAction').value;
        if (formAction === 'edit') {
            const imageId = document.getElementById('imageId').value;
            // Find the original image data from the page
            const imageItems = document.querySelectorAll('.image-item');
            let originalImage = null;
            imageItems.forEach(item => {
                const editButton = item.querySelector('[onclick*="editImage"]');
                if (editButton && editButton.onclick.toString().includes(imageId)) {
                    const imgSrc = item.querySelector('img').src;
                    originalImage = imgSrc;
                }
            });
            
            if (originalImage) {
                previewDiv.innerHTML = `
                    <div style="margin-top: 1rem;">
                        <strong>Current Image:</strong><br>
                        <img src="${originalImage}" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">
                        <br><small>Leave image field empty to keep current image, or select a new image to replace it.</small>
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
