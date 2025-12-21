<?php
// ============================================================================
// FILE: admin/subscribers.php - Newsletter Subscribers Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = intval($_POST['id']);
        $status = sanitizeInput($_POST['status']);
        $db->update('subscribers', ['status' => $status], 'id = ?', [$id]);
        $success = 'Subscriber status updated successfully';
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $db->delete('subscribers', 'id = ?', [$id]);
        $success = 'Subscriber deleted successfully';
    }
}

$subscribers = $db->fetchAll("SELECT * FROM subscribers ORDER BY subscribed_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Newsletter Subscribers</h1>
    <a href="?export=csv" class="btn btn-secondary">📥 Export to CSV</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-number"><?php echo count($subscribers); ?></div>
        <div class="stat-label">Total Subscribers</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-number"><?php echo count(array_filter($subscribers, fn($s) => $s['status'] == 'active')); ?></div>
        <div class="stat-label">Active</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🚫</div>
        <div class="stat-number"><?php echo count(array_filter($subscribers, fn($s) => $s['status'] == 'unsubscribed')); ?></div>
        <div class="stat-label">Unsubscribed</div>
    </div>
</div>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
                <th>Subscribed Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscribers)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 2rem;">No subscribers yet.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($subscribers as $subscriber): ?>
            <tr>
                <td><strong>#<?php echo $subscriber['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                <td><?php echo htmlspecialchars($subscriber['name'] ?: 'N/A'); ?></td>
                <td><?php echo date('M d, Y', strtotime($subscriber['subscribed_at'])); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $subscriber['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="status-badge status-<?php echo $subscriber['status']; ?>" style="border: none; background: transparent; cursor: pointer; font-weight: 500;">
                            <option value="active" <?php echo $subscriber['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="unsubscribed" <?php echo $subscriber['status'] == 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
                        </select>
                    </form>
                </td>
                <td>
                    <button class="btn-icon" onclick="deleteSubscriber(<?php echo $subscriber['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function deleteSubscriber(id) {
    if (confirm('Are you sure you want to delete this subscriber?')) {
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
</script>

<?php
// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email', 'Name', 'Status', 'Subscribed Date']);
    
    foreach ($subscribers as $subscriber) {
        fputcsv($output, [
            $subscriber['id'],
            $subscriber['email'],
            $subscriber['name'] ?: 'N/A',
            $subscriber['status'],
            $subscriber['subscribed_at']
        ]);
    }
    
    fclose($output);
    exit;
}

include 'includes/admin_footer.php';
?>