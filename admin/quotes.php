<?php
// ============================================================================
// FILE: admin/quotes.php - Quote Request Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = intval($_POST['id']);
    $status = sanitizeInput($_POST['status']);
    $db->update('quote_requests', ['status' => $status], 'id = ?', [$id]);
    $success = 'Quote status updated successfully';
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $db->delete('quote_requests', 'id = ?', [$id]);
    $success = 'Quote deleted successfully';
}

// Fetch all quotes
$quotes = $db->fetchAll("SELECT q.*, p.name as product_name 
                         FROM quote_requests q 
                         LEFT JOIN products p ON q.product_id = p.id 
                         ORDER BY q.created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Quote Requests</h1>
    <div>
        <a href="?export=csv" class="btn btn-secondary">📥 Export to CSV</a>
    </div>
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
                <th>Contact</th>
                <th>Product</th>
                <th>Area (sqm)</th>
                <th>Installation</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quotes)): ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem;">No quote requests yet.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($quotes as $quote): ?>
            <tr>
                <td><strong>#<?php echo $quote['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($quote['name']); ?></td>
                <td>
                    <small>
                        📧 <?php echo htmlspecialchars($quote['email']); ?><br>
                        📞 <?php echo htmlspecialchars($quote['phone']); ?>
                    </small>
                </td>
                <td><?php echo htmlspecialchars($quote['product_name'] ?? 'N/A'); ?></td>
                <td><?php echo number_format($quote['area'], 2); ?></td>
                <td><?php echo $quote['include_installation'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo date('M d, Y', strtotime($quote['created_at'])); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $quote['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="status-badge status-<?php echo $quote['status']; ?>" style="border: none; background: transparent; cursor: pointer; font-weight: 500;">
                            <option value="pending" <?php echo $quote['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="responded" <?php echo $quote['status'] == 'responded' ? 'selected' : ''; ?>>Responded</option>
                            <option value="completed" <?php echo $quote['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $quote['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </form>
                </td>
                <td>
                    <button class="btn-icon" onclick='viewQuote(<?php echo json_encode($quote); ?>)' title="View Details">👁️</button>
                    <button class="btn-icon" onclick="deleteQuote(<?php echo $quote['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- View Quote Modal -->
<div id="quoteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Quote Request Details</h2>
        <div id="quoteDetails"></div>
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
    display: inline-block;
    width: 150px;
}
</style>

<script>
function viewQuote(quote) {
    const installation = quote.include_installation ? 'Yes' : 'No';
    const html = `
        <div class="detail-row">
            <strong>Request ID:</strong> #${quote.id}
        </div>
        <div class="detail-row">
            <strong>Name:</strong> ${quote.name}
        </div>
        <div class="detail-row">
            <strong>Email:</strong> ${quote.email}
        </div>
        <div class="detail-row">
            <strong>Phone:</strong> ${quote.phone || 'N/A'}
        </div>
        <div class="detail-row">
            <strong>Product:</strong> ${quote.product_name || 'N/A'}
        </div>
        <div class="detail-row">
            <strong>Area:</strong> ${parseFloat(quote.area).toFixed(2)} sqm
        </div>
        <div class="detail-row">
            <strong>Installation:</strong> ${installation}
        </div>
        <div class="detail-row">
            <strong>Message:</strong><br>
            ${quote.message || 'No additional message'}
        </div>
        <div class="detail-row">
            <strong>Status:</strong> <span class="status-badge status-${quote.status}">${quote.status.charAt(0).toUpperCase() + quote.status.slice(1)}</span>
        </div>
        <div class="detail-row">
            <strong>Date:</strong> ${new Date(quote.created_at).toLocaleString()}
        </div>
        <div style="margin-top: 1.5rem;">
            <a href="mailto:${quote.email}" class="btn btn-primary">📧 Send Email</a>
            <a href="https://wa.me/${quote.phone}?text=Hello ${quote.name}, regarding your quote request..." class="btn btn-secondary" target="_blank">💬 WhatsApp</a>
        </div>
    `;
    
    document.getElementById('quoteDetails').innerHTML = html;
    document.getElementById('quoteModal').style.display = 'block';
}

function deleteQuote(id) {
    if (confirm('Are you sure you want to delete this quote request?')) {
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
    document.getElementById('quoteModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('quoteModal')) {
        closeModal();
    }
}
</script>

<?php
// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="quote_requests_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Product', 'Area', 'Installation', 'Status', 'Date']);
    
    foreach ($quotes as $quote) {
        fputcsv($output, [
            $quote['id'],
            $quote['name'],
            $quote['email'],
            $quote['phone'],
            $quote['product_name'] ?? 'N/A',
            $quote['area'],
            $quote['include_installation'] ? 'Yes' : 'No',
            $quote['status'],
            $quote['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

include 'includes/admin_footer.php';
?>