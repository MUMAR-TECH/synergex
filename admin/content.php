<?php
// ============================================================================
// FILE: admin/content.php - Page Content Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle content updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_content':
                $pageName = sanitizeInput($_POST['page_name'] ?? '');
                $sectionName = sanitizeInput($_POST['section_name'] ?? '');
                $content = $_POST['content'] ?? ''; // Allow HTML, so don't sanitize too aggressively
                
                if (empty($pageName) || empty($sectionName)) {
                    $error = 'Page name and section name are required';
                } else {
                    // Check if content exists
                    $existing = $db->fetchOne(
                        "SELECT id FROM page_content WHERE page_name = ? AND section_name = ?",
                        [$pageName, $sectionName]
                    );
                    
                    if ($existing) {
                        $db->update(
                            'page_content',
                            ['content' => $content],
                            'page_name = ? AND section_name = ?',
                            [$pageName, $sectionName]
                        );
                    } else {
                        $db->insert('page_content', [
                            'page_name' => $pageName,
                            'section_name' => $sectionName,
                            'content' => $content
                        ]);
                    }
                    
                    $success = 'Content updated successfully';
                }
                break;
                
            case 'delete_content':
                $id = intval($_POST['id'] ?? 0);
                if ($id > 0) {
                    $db->delete('page_content', 'id = ?', [$id]);
                    $success = 'Content deleted successfully';
                }
                break;
        }
    }
}

// Get all page content
$allContent = $db->fetchAll("SELECT * FROM page_content ORDER BY page_name, section_name");

// Group content by page
$contentByPage = [];
foreach ($allContent as $content) {
    $contentByPage[$content['page_name']][] = $content;
}

// Available pages
$availablePages = ['home', 'about', 'what-we-do', 'vision-sdgs', 'products', 'contact'];

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Page Content Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Content</button>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div style="display: grid; gap: 2rem;">
    <?php foreach ($availablePages as $page): ?>
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 style="text-transform: capitalize; margin-bottom: 1.5rem; color: var(--primary-blue);">
            <?php echo str_replace('-', ' ', $page); ?> Page
        </h2>
        
        <?php if (isset($contentByPage[$page]) && !empty($contentByPage[$page])): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>Content Preview</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contentByPage[$page] as $content): ?>
                    <tr>
                        <td><strong><?php echo ucfirst(str_replace('_', ' ', $content['section_name'])); ?></strong></td>
                        <td>
                            <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars(substr(strip_tags($content['content']), 0, 100)); ?>
                                <?php if (strlen($content['content']) > 100): ?>...<?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($content['updated_at'])); ?></td>
                        <td>
                            <button class="btn-icon" onclick='editContent(<?php echo json_encode($content); ?>)' title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteContent(<?php echo $content['id']; ?>)" title="Delete">🗑️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="color: #666; padding: 1rem 0;">No content sections defined for this page yet.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add/Edit Content Modal -->
<div id="contentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Content</h2>
        
        <form id="contentForm" method="POST">
            <input type="hidden" name="action" id="formAction" value="update_content">
            <input type="hidden" name="id" id="contentId">
            
            <div class="form-group">
                <label for="page_name">Page *</label>
                <select id="page_name" name="page_name" required>
                    <option value="">Select Page</option>
                    <?php foreach ($availablePages as $page): ?>
                    <option value="<?php echo $page; ?>"><?php echo ucfirst(str_replace('-', ' ', $page)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="section_name">Section Name *</label>
                <input type="text" id="section_name" name="section_name" 
                       placeholder="e.g., hero, mission, values" required>
                <small style="color: #666; font-size: 0.85rem;">Use lowercase with underscores (e.g., who_we_are, founder_note)</small>
            </div>
            
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" rows="10" required style="font-family: monospace;"></textarea>
                <small style="color: #666; font-size: 0.85rem;">HTML is allowed. Use &lt;p&gt; tags for paragraphs, &lt;strong&gt; for bold, etc.</small>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Content</button>
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
    max-width: 800px;
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

small {
    display: block;
    margin-top: 0.25rem;
}
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Content';
    document.getElementById('formAction').value = 'update_content';
    document.getElementById('contentForm').reset();
    document.getElementById('contentId').value = '';
    document.getElementById('contentModal').style.display = 'block';
}

function editContent(content) {
    document.getElementById('modalTitle').textContent = 'Edit Content';
    document.getElementById('formAction').value = 'update_content';
    document.getElementById('contentId').value = content.id;
    document.getElementById('page_name').value = content.page_name;
    document.getElementById('section_name').value = content.section_name;
    document.getElementById('content').value = content.content;
    document.getElementById('contentModal').style.display = 'block';
}

function deleteContent(id) {
    if (confirm('Are you sure you want to delete this content section?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_content">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('contentModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('contentModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>

