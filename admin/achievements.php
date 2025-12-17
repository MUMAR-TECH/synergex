<?php
// ============================================================================
// FILE: admin/achievements.php - Achievements Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $year = intval($_POST['year']);
                $title = sanitizeInput($_POST['title']);
                $description = sanitizeInput($_POST['description']);
                $category = sanitizeInput($_POST['category']);
                $displayOrder = intval($_POST['display_order']);
                
                $data = [
                    'year' => $year,
                    'title' => $title,
                    'description' => $description,
                    'category' => $category,
                    'display_order' => $displayOrder
                ];
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['image'], 'achievement');
                    if ($upload['success']) {
                        $data['image'] = $upload['filename'];
                    }
                }
                
                if ($_POST['action'] === 'add') {
                    $db->insert('achievements', $data);
                    $success = 'Achievement added successfully';
                } else {
                    $id = intval($_POST['id']);
                    if (empty($data['image'])) {
                        unset($data['image']);
                    }
                    $db->update('achievements', $data, 'id = ?', [$id]);
                    $success = 'Achievement updated successfully';
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $achievement = $db->fetchOne("SELECT image FROM achievements WHERE id = ?", [$id]);
                if ($achievement && $achievement['image'] && file_exists(UPLOAD_PATH . $achievement['image'])) {
                    unlink(UPLOAD_PATH . $achievement['image']);
                }
                $db->delete('achievements', 'id = ?', [$id]);
                $success = 'Achievement deleted successfully';
                break;
        }
    }
}

$achievements = getAchievements();

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Achievements Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Achievement</button>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Title</th>
                <th>Category</th>
                <th>Order</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($achievements as $achievement): ?>
            <tr>
                <td><strong><?php echo $achievement['year']; ?></strong></td>
                <td><?php echo htmlspecialchars($achievement['title']); ?></td>
                <td><?php echo ucfirst(str_replace('_', ' ', $achievement['category'])); ?></td>
                <td><?php echo $achievement['display_order']; ?></td>
                <td>
                    <?php if ($achievement['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $achievement['image']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                    <span style="color: #999;">No image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn-icon" onclick='editAchievement(<?php echo json_encode($achievement); ?>)'>✏️</button>
                    <button class="btn-icon" onclick="deleteAchievement(<?php echo $achievement['id']; ?>)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="achievementModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Achievement</h2>
        
        <form id="achievementForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="achievementId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="year">Year *</label>
                    <input type="number" id="year" name="year" min="2020" max="2050" value="<?php echo date('Y'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="grant">Grant</option>
                        <option value="pilot">Pilot Project</option>
                        <option value="recognition">Recognition</option>
                        <option value="partnership">Partnership</option>
                        <option value="media">Media Coverage</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="0">
                </div>
                
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>
            
            <div id="imagePreview"></div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Achievement</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Achievement';
    document.getElementById('formAction').value = 'add';
    document.getElementById('achievementForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('year').value = new Date().getFullYear();
    document.getElementById('achievementModal').style.display = 'block';
}

function editAchievement(achievement) {
    document.getElementById('modalTitle').textContent = 'Edit Achievement';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('achievementId').value = achievement.id;
    document.getElementById('year').value = achievement.year;
    document.getElementById('title').value = achievement.title;
    document.getElementById('description').value = achievement.description || '';
    document.getElementById('category').value = achievement.category || '';
    document.getElementById('display_order').value = achievement.display_order;
    
    if (achievement.image) {
        document.getElementById('imagePreview').innerHTML = 
            `<img src="<?php echo UPLOAD_URL; ?>${achievement.image}" style="max-width: 200px; border-radius: 5px; margin-top: 1rem;">`;
    }
    
    document.getElementById('achievementModal').style.display = 'block';
}

function deleteAchievement(id) {
    if (confirm('Are you sure you want to delete this achievement?')) {
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
    document.getElementById('achievementModal').style.display = 'none';
}

document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = 
                `<img src="${e.target.result}" style="max-width: 200px; border-radius: 5px; margin-top: 1rem;">`;
        };
        reader.readAsDataURL(file);
    }
});

window.onclick = function(event) {
    if (event.target == document.getElementById('achievementModal')) {
        closeModal();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>