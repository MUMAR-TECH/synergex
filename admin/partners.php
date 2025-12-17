<?php
// ============================================================================
// FILE: admin/partners.php - Partners Management
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
                $name = sanitizeInput($_POST['name'] ?? '');
                $website = sanitizeInput($_POST['website'] ?? '');
                $displayOrder = intval($_POST['display_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                $data = [
                    'name' => $name,
                    'website' => $website,
                    'display_order' => $displayOrder,
                    'is_active' => $isActive
                ];
                
                // Handle logo upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['logo'], 'partner');
                    if ($upload['success']) {
                        $data['logo'] = $upload['filename'];
                    } else {
                        $error = $upload['message'];
                    }
                }
                
                if (empty($error)) {
                    if ($_POST['action'] === 'add') {
                        $db->insert('partners', $data);
                        $success = 'Partner added successfully';
                    } else {
                        $id = intval($_POST['id']);
                        if (empty($data['logo'])) {
                            unset($data['logo']);
                        }
                        $db->update('partners', $data, 'id = ?', [$id]);
                        $success = 'Partner updated successfully';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $partner = $db->fetchOne("SELECT logo FROM partners WHERE id = ?", [$id]);
                if ($partner && !empty($partner['logo']) && file_exists(UPLOAD_PATH . $partner['logo'])) {
                    unlink(UPLOAD_PATH . $partner['logo']);
                }
                $db->delete('partners', 'id = ?', [$id]);
                $success = 'Partner deleted successfully';
                break;
        }
    }
}

$partners = $db->fetchAll("SELECT * FROM partners ORDER BY display_order ASC, created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Partners Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Partner</button>
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
            <label for="searchPartners">Search Partners</label>
            <input type="text" id="searchPartners" placeholder="Search by name, website..." 
                   onkeyup="filterPartners()" style="margin-bottom: 0;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="filterStatus">Filter by Status</label>
            <select id="filterStatus" onchange="filterPartners()" style="margin-bottom: 0;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div>
            <button class="btn btn-secondary" onclick="resetPartnerFilters()">Reset Filters</button>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Website</th>
                <th>Display Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="partnersTableBody">
            <?php if (empty($partners)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 2rem;">No partners yet. Add your first partner to get started.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($partners as $partner): ?>
            <tr data-name="<?php echo strtolower(htmlspecialchars($partner['name'])); ?>"
                data-website="<?php echo strtolower(htmlspecialchars($partner['website'] ?? '')); ?>"
                data-status="<?php echo $partner['is_active'] ? 'active' : 'inactive'; ?>">
                <td>
                    <?php if ($partner['logo']): ?>
                    <img src="<?php echo UPLOAD_URL . $partner['logo']; ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                         style="width: 80px; height: 60px; object-fit: contain; border-radius: 5px; background: #f5f5f5; padding: 5px;">
                    <?php else: ?>
                    <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #999;">No Logo</div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($partner['name']); ?></strong></td>
                <td>
                    <?php if ($partner['website']): ?>
                    <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank" style="color: var(--primary-blue);">
                        <?php echo htmlspecialchars($partner['website']); ?>
                    </a>
                    <?php else: ?>
                    <span style="color: #999;">No website</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $partner['display_order']; ?></td>
                <td>
                    <span class="status-badge status-<?php echo $partner['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $partner['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-icon" onclick='editPartner(<?php echo json_encode($partner); ?>)' title="Edit">✏️</button>
                    <button class="btn-icon" onclick="deletePartner(<?php echo $partner['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Partner Modal -->
<div id="partnerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Partner</h2>
        
        <form id="partnerForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="partnerId">
            
            <div class="form-group">
                <label for="name">Partner Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="website">Website URL</label>
                <input type="url" id="website" name="website" placeholder="https://example.com">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="0">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" id="is_active" checked>
                        Active (visible on website)
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="logo">Partner Logo</label>
                <input type="file" id="logo" name="logo" accept="image/*">
                <small style="color: #666; font-size: 0.85rem;">Recommended size: 200x100px or similar. PNG with transparent background preferred.</small>
                <div id="logoPreview"></div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Partner</button>
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

#logoPreview {
    margin-top: 1rem;
}

#logoPreview img {
    max-width: 200px;
    max-height: 150px;
    border-radius: 5px;
    object-fit: contain;
    background: #f5f5f5;
    padding: 10px;
}
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Partner';
    document.getElementById('formAction').value = 'add';
    document.getElementById('partnerForm').reset();
    document.getElementById('logoPreview').innerHTML = '';
    document.getElementById('partnerModal').style.display = 'block';
}

function editPartner(partner) {
    document.getElementById('modalTitle').textContent = 'Edit Partner';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('partnerId').value = partner.id;
    document.getElementById('name').value = partner.name || '';
    document.getElementById('website').value = partner.website || '';
    document.getElementById('display_order').value = partner.display_order || 0;
    document.getElementById('is_active').checked = partner.is_active == 1;
    
    if (partner.logo) {
        document.getElementById('logoPreview').innerHTML = 
            `<img src="<?php echo UPLOAD_URL; ?>${partner.logo}" alt="Current logo">`;
    }
    
    document.getElementById('partnerModal').style.display = 'block';
}

function deletePartner(id) {
    if (confirm('Are you sure you want to delete this partner?')) {
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
    document.getElementById('partnerModal').style.display = 'none';
}

document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 5px; object-fit: contain; background: #f5f5f5; padding: 10px;">`;
        };
        reader.readAsDataURL(file);
    }
});

window.onclick = function(event) {
    const modal = document.getElementById('partnerModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Search and Filter Functions
function filterPartners() {
    const searchTerm = document.getElementById('searchPartners').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#partnersTableBody tr');
    
    rows.forEach(row => {
        const name = row.dataset.name || '';
        const website = row.dataset.website || '';
        const status = row.dataset.status || '';
        
        const matchesSearch = name.includes(searchTerm) || website.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}

function resetPartnerFilters() {
    document.getElementById('searchPartners').value = '';
    document.getElementById('filterStatus').value = '';
    filterPartners();
}
</script>

<?php include 'includes/admin_footer.php'; ?>

