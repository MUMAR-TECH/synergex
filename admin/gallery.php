<?php
// ============================================================================
// FILE: admin/gallery.php - Gallery Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
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
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['image'], 'gallery');
                    if ($upload['success']) {
                        $data['image'] = $upload['filename'];
                    } else {
                        $error = $upload['message'];
                    }
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
                        $id = intval($_POST['id']);
                        if (empty($data['image'])) {
                            unset($data['image']);
                        }
                        $db->update('gallery', $data, 'id = ?', [$id]);
                        $success = 'Image updated successfully';
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

<div class="image-grid">
    <?php foreach ($galleryImages as $image): ?>
    <div class="image-item">
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
                <label for="image">Image *</label>
                <input type="file" id="image" name="image" accept="image/*">
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
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Image';
    document.getElementById('formAction').value = 'add';
    document.getElementById('imageForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('image').required = true;
    document.getElementById('imageModal').style.display = 'block';
}

function editImage(image) {
    document.getElementById('modalTitle').textContent = 'Edit Image';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('imageId').value = image.id;
    document.getElementById('title').value = image.title;
    document.getElementById('category').value = image.category || '';
    document.getElementById('caption').value = image.caption || '';
    document.getElementById('display_order').value = image.display_order;
    document.getElementById('image').required = false;
    
    document.getElementById('imagePreview').innerHTML = 
        `<img src="<?php echo UPLOAD_URL; ?>${image.image}" alt="Current image">`;
    
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
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 5px;">`;
        };
        reader.readAsDataURL(file);
    }
});

window.onclick = function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
