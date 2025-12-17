<?php
// ============================================================================
// FILE: admin/messages.php - Contact Messages Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = intval($_POST['id']);
        $status = sanitizeInput($_POST['status']);
        $db->update('contact_messages', ['status' => $status], 'id = ?', [$id]);
        $success = 'Message status updated successfully';
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $db->delete('contact_messages', 'id = ?', [$id]);
        $success = 'Message deleted successfully';
    }
}

$messages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Contact Messages</h1>
    <a href="?export=csv" class="btn btn-secondary">📥 Export to CSV</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">No messages yet.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($messages as $message): ?>
            <tr style="<?php echo $message['status'] == 'unread' ? 'background: #f0f8ff;' : ''; ?>">
                <td><strong>#<?php echo $message['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($message['name']); ?></td>
                <td><?php echo htmlspecialchars($message['email']); ?></td>
                <td><?php echo htmlspecialchars($message['subject'] ?: 'No Subject'); ?></td>
                <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="status-badge status-<?php echo $message['status']; ?>" style="border: none; background: transparent; cursor: pointer; font-weight: 500;">
                            <option value="unread" <?php echo $message['status'] == 'unread' ? 'selected' : ''; ?>>Unread</option>
                            <option value="read" <?php echo $message['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="responded" <?php echo $message['status'] == 'responded' ? 'selected' : ''; ?>>Responded</option>
                        </select>
                    </form>
                </td>
                <td>
                    <button class="btn-icon" onclick='viewMessage(<?php echo json_encode($message); ?>)' title="View">👁️</button>
                    <button class="btn-icon" onclick="deleteMessage(<?php echo $message['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- View Message Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Message Details</h2>
        <div id="messageDetails"></div>
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
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.detail-row {
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.detail-row strong {
    color: #1A3E7F;
    display: block;
    margin-bottom: 0.25rem;
}
</style>

<script>
function viewMessage(message) {
    const html = `
        <div class="detail-row">
            <strong>From:</strong>
            ${message.name}
        </div>
        <div class="detail-row">
            <strong>Email:</strong>
            ${message.email}
        </div>
        <div class="detail-row">
            <strong>Subject:</strong>
            ${message.subject || 'No Subject'}
        </div>
        <div class="detail-row">
            <strong>Message:</strong>
            <p style="white-space: pre-wrap; margin-top: 0.5rem;">${message.message}</p>
        </div>
        <div class="detail-row">
            <strong>Date:</strong>
            ${new Date(message.created_at).toLocaleString()}
        </div>
        <div class="detail-row">
            <strong>Status:</strong>
            <span class="status-badge status-${message.status}">${message.status.charAt(0).toUpperCase() + message.status.slice(1)}</span>
        </div>
        <div style="margin-top: 1.5rem;">
            <a href="mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject || 'Your message')}" class="btn btn-primary">📧 Reply via Email</a>
        </div>
    `;
    
    document.getElementById('messageDetails').innerHTML = html;
    document.getElementById('messageModal').style.display = 'block';
}

function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
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
    document.getElementById('messageModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('messageModal')) {
        closeModal();
    }
}
</script>

<?php
// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contact_messages_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Subject', 'Message', 'Status', 'Date']);
    
    foreach ($messages as $message) {
        fputcsv($output, [
            $message['id'],
            $message['name'],
            $message['email'],
            $message['subject'],
            $message['message'],
            $message['status'],
            $message['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

include 'includes/admin_footer.php';
?>
