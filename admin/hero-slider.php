<?php
// ============================================================================
// FILE: admin/hero-slider.php - Hero Slider Management
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
                $title = sanitizeInput($_POST['title'] ?? '');
                $subtitle = sanitizeInput($_POST['subtitle'] ?? '');
                $buttonText = sanitizeInput($_POST['button_text'] ?? '');
                $buttonLink = sanitizeInput($_POST['button_link'] ?? '');
                $displayOrder = intval($_POST['display_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                $data = [
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'button_text' => $buttonText,
                    'button_link' => $buttonLink,
                    'display_order' => $displayOrder,
                    'is_active' => $isActive
                ];
                
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['image'], 'hero');
                    if ($upload['success']) {
                        $data['image'] = $upload['filename'];
                    } else {
                        $error = $upload['message'];
                    }
                }
                
                if (empty($error)) {
                    if ($_POST['action'] === 'add') {
                        if (!empty($data['image'])) {
                            $db->insert('hero_slider', $data);
                            $success = 'Hero slide added successfully';
                        } else {
                            $error = 'Please upload an image';
                        }
                    } else {
                        $id = intval($_POST['id']);
                        if (empty($data['image'])) {
                            unset($data['image']);
                        }
                        $db->update('hero_slider', $data, 'id = ?', [$id]);
                        $success = 'Hero slide updated successfully';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $slide = $db->fetchOne("SELECT image FROM hero_slider WHERE id = ?", [$id]);
                if ($slide && !empty($slide['image']) && file_exists(UPLOAD_PATH . $slide['image'])) {
                    unlink(UPLOAD_PATH . $slide['image']);
                }
                $db->delete('hero_slider', 'id = ?', [$id]);
                $success = 'Hero slide deleted successfully';
                break;
        }
    }
}

$slides = $db->fetchAll("SELECT * FROM hero_slider ORDER BY display_order ASC, created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Hero Slider Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Slide</button>
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
            <label for="searchSlides">Search Slides</label>
            <input type="text" id="searchSlides" placeholder="Search by title, subtitle..." 
                   onkeyup="filterSlides()" style="margin-bottom: 0;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="filterStatus">Filter by Status</label>
            <select id="filterStatus" onchange="filterSlides()" style="margin-bottom: 0;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div>
            <button class="btn btn-secondary" onclick="resetSlideFilters()">Reset Filters</button>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Subtitle</th>
                <th>Button Text</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="slidesTableBody">
            <?php if (empty($slides)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">No slides yet. Add your first slide to get started.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($slides as $slide): ?>
            <tr data-title="<?php echo strtolower(htmlspecialchars($slide['title'] ?? '')); ?>"
                data-subtitle="<?php echo strtolower(htmlspecialchars($slide['subtitle'] ?? '')); ?>"
                data-status="<?php echo $slide['is_active'] ? 'active' : 'inactive'; ?>">
                <td>
                    <?php if ($slide['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $slide['image']; ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" 
                         style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                    <div style="width: 100px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">No Image</div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($slide['title']); ?></strong></td>
                <td><?php echo htmlspecialchars(substr($slide['subtitle'], 0, 50)); ?><?php echo strlen($slide['subtitle']) > 50 ? '...' : ''; ?></td>
                <td><?php echo htmlspecialchars($slide['button_text']); ?></td>
                <td><?php echo $slide['display_order']; ?></td>
                <td>
                    <span class="status-badge status-<?php echo $slide['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $slide['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-icon" onclick='editSlide(<?php echo json_encode($slide); ?>)' title="Edit">✏️</button>
                    <button class="btn-icon" onclick="deleteSlide(<?php echo $slide['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div id="slideModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Slide</h2>
        
        <form id="slideForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="slideId">
            
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="subtitle">Subtitle</label>
                <textarea id="subtitle" name="subtitle" rows="2"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="button_text">Button Text</label>
                    <input type="text" id="button_text" name="button_text" placeholder="e.g., Get Started">
                </div>
                
                <div class="form-group">
                    <label for="button_link">Button Link</label>
                    <input type="text" id="button_link" name="button_link" placeholder="e.g., /products.php">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="0">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" id="is_active" checked>
                        Active (visible on homepage)
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="image">Image *</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small style="color: #666; font-size: 0.85rem;">Recommended size: 1920x1080px or similar wide format</small>
                <div id="imagePreview"></div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Slide</button>
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
    max-width: 700px;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

#imagePreview {
    margin-top: 1rem;
}

#imagePreview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 5px;
    object-fit: cover;
}
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Slide';
    document.getElementById('formAction').value = 'add';
    document.getElementById('slideForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('image').required = true;
    document.getElementById('slideModal').style.display = 'block';
}

function editSlide(slide) {
    document.getElementById('modalTitle').textContent = 'Edit Slide';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('slideId').value = slide.id;
    document.getElementById('title').value = slide.title || '';
    document.getElementById('subtitle').value = slide.subtitle || '';
    document.getElementById('button_text').value = slide.button_text || '';
    document.getElementById('button_link').value = slide.button_link || '';
    document.getElementById('display_order').value = slide.display_order || 0;
    document.getElementById('is_active').checked = slide.is_active == 1;
    document.getElementById('image').required = false;
    
    if (slide.image) {
        document.getElementById('imagePreview').innerHTML = 
            `<img src="<?php echo UPLOAD_URL; ?>${slide.image}" alt="Current image">`;
    }
    
    document.getElementById('slideModal').style.display = 'block';
}

function deleteSlide(id) {
    if (confirm('Are you sure you want to delete this slide?')) {
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
    document.getElementById('slideModal').style.display = 'none';
}

document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 5px; object-fit: cover;">`;
        };
        reader.readAsDataURL(file);
    }
});

window.onclick = function(event) {
    const modal = document.getElementById('slideModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Search and Filter Functions
function filterSlides() {
    const searchTerm = document.getElementById('searchSlides').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#slidesTableBody tr');
    
    rows.forEach(row => {
        const title = row.dataset.title || '';
        const subtitle = row.dataset.subtitle || '';
        const status = row.dataset.status || '';
        
        const matchesSearch = title.includes(searchTerm) || subtitle.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}

function resetSlideFilters() {
    document.getElementById('searchSlides').value = '';
    document.getElementById('filterStatus').value = '';
    filterSlides();
}
</script>

<?php include 'includes/admin_footer.php'; ?>

