<?php
// ============================================================================
// FILE: admin/what-we-do.php - What We Do Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/admin_utilities.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle form submissions using standardized system
$result = handleAdminFormSubmission(
    'what_we_do',
    ['title', 'description', 'icon'], // required fields
    ['features'], // optional fields
    null // no image field
);

if ($result['action']) {
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Fetch all entries
$services = $db->fetchAll("SELECT * FROM what_we_do ORDER BY id ASC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>What We Do Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Service</button>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Icon</th>
                <th>Title</th>
                <th>Description</th>
                <th>Features</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['icon']); ?></td>
                <td><strong><?php echo htmlspecialchars($service['title']); ?></strong></td>
                <td><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</td>
                <td><?php echo htmlspecialchars(substr($service['features'], 0, 50)); ?>...</td>
                <td>
                    <button class="btn-icon" onclick='editService(<?php echo json_encode($service); ?>)' title="Edit">✏️</button>
                    <button class="btn-icon" onclick="deleteService(<?php echo $service['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div id="serviceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Service</h2>
        
        <form id="serviceForm" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="serviceId">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="icon">Icon (CSS class) *</label>
                <input type="text" name="icon" id="serviceIcon" required placeholder="e.g., fas fa-recycle">
            </div>

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" name="title" id="serviceTitle" required>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea name="description" id="serviceDescription" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="features">Features (separated by |)</label>
                <textarea name="features" id="serviceFeatures" rows="3" placeholder="Feature 1|Feature 2|Feature 3"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Service</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Service';
    document.getElementById('formAction').value = 'add';
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceModal').style.display = 'block';
}

function editService(service) {
    document.getElementById('modalTitle').textContent = 'Edit Service';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('serviceId').value = service.id;
    document.getElementById('serviceIcon').value = service.icon;
    document.getElementById('serviceTitle').value = service.title;
    document.getElementById('serviceDescription').value = service.description;
    document.getElementById('serviceFeatures').value = service.features || '';
    document.getElementById('serviceModal').style.display = 'block';
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service?')) {
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
    document.getElementById('serviceModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('serviceModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>