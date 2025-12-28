<?php
// ============================================================================
// FILE: admin/achievements.php - Achievements Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/admin_utilities.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle form submissions using standardized system
$result = handleAdminFormSubmission(
    'achievements',
    ['year', 'title', 'description'], // required fields
    ['detailed_content', 'gallery_images', 'category', 'display_order'], // optional fields
    'image' // image field
);

if ($result['action']) {
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
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

<!-- Search and Filter -->
<div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="searchAchievements">Search Achievements</label>
            <input type="text" id="searchAchievements" placeholder="Search by title, description..." 
                   onkeyup="filterAchievements()" style="margin-bottom: 0;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="filterCategory">Filter by Category</label>
            <select id="filterCategory" onchange="filterAchievements()" style="margin-bottom: 0;">
                <option value="">All Categories</option>
                <option value="grant">Grant</option>
                <option value="pilot">Pilot Project</option>
                <option value="recognition">Recognition</option>
                <option value="partnership">Partnership</option>
                <option value="media">Media Coverage</option>
            </select>
        </div>
        <div>
            <button class="btn btn-secondary" onclick="resetAchievementFilters()">Reset Filters</button>
        </div>
    </div>
</div>

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
        <tbody id="achievementsTableBody">
            <?php foreach ($achievements as $achievement): ?>
            <tr data-title="<?php echo strtolower(htmlspecialchars($achievement['title'])); ?>"
                data-description="<?php echo strtolower(htmlspecialchars($achievement['description'] ?? '')); ?>"
                data-category="<?php echo $achievement['category'] ?? ''; ?>">
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
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
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
            
            <div class="form-group">
                <label for="detailed_content">Detailed Content</label>
                <textarea id="detailed_content" name="detailed_content" rows="8" placeholder="Full details about this achievement (supports line breaks)"></textarea>
                <small style="color: #666; font-size: 0.85rem;">This content will be displayed on the achievement details page</small>
            </div>
            
            <div class="form-group">
                <label for="gallery_images">Gallery Images (URLs or upload multiple)</label>
                <input type="text" id="gallery_images" name="gallery_images" placeholder="Paste image URLs separated by commas or leave blank">
                <small style="color: #666; font-size: 0.85rem;">You can add multiple image URLs separated by commas for a gallery</small>
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
    document.getElementById('detailed_content').value = achievement.detailed_content || '';
    document.getElementById('gallery_images').value = achievement.gallery_images || '';
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
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
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

// Search and Filter Functions
function filterAchievements() {
    const searchTerm = document.getElementById('searchAchievements').value.toLowerCase();
    const categoryFilter = document.getElementById('filterCategory').value;
    const rows = document.querySelectorAll('#achievementsTableBody tr');
    
    rows.forEach(row => {
        const title = row.dataset.title || '';
        const description = row.dataset.description || '';
        const category = row.dataset.category || '';
        
        const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    });
}

function resetAchievementFilters() {
    document.getElementById('searchAchievements').value = '';
    document.getElementById('filterCategory').value = '';
    filterAchievements();
}
</script>

<?php include 'includes/admin_footer.php'; ?>