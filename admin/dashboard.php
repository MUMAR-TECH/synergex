<?php
// ============================================================================
// FILE: admin/dashboard.php - Admin Dashboard
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();

// Get statistics
$totalProducts = $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'];
$totalQuotes = $db->fetchOne("SELECT COUNT(*) as count FROM quote_requests")['count'];
$pendingQuotes = $db->fetchOne("SELECT COUNT(*) as count FROM quote_requests WHERE status = 'pending'")['count'];
$totalMessages = $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'];
$unreadMessages = $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")['count'];
$totalSubscribers = $db->fetchOne("SELECT COUNT(*) as count FROM subscribers WHERE status = 'active'")['count'];
$totalGallery = $db->fetchOne("SELECT COUNT(*) as count FROM gallery")['count'];
$totalAchievements = $db->fetchOne("SELECT COUNT(*) as count FROM achievements")['count'];

// Recent quotes
$recentQuotes = $db->fetchAll("SELECT q.*, p.name as product_name FROM quote_requests q 
                               LEFT JOIN products p ON q.product_id = p.id 
                               ORDER BY q.created_at DESC LIMIT 5");

// Recent messages
$recentMessages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

include 'includes/admin_header.php';
?>

<div class="dashboard">
    <h1>Dashboard</h1>
    <p class="subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-number"><?php echo $totalProducts; ?></div>
            <div class="stat-label">Products</div>
            <a href="products.php" class="stat-link">Manage →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-number"><?php echo $totalQuotes; ?></div>
            <div class="stat-label">Quote Requests</div>
            <?php if ($pendingQuotes > 0): ?>
            <span class="badge"><?php echo $pendingQuotes; ?> Pending</span>
            <?php endif; ?>
            <a href="quotes.php" class="stat-link">View →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            <div class="stat-number"><?php echo $totalMessages; ?></div>
            <div class="stat-label">Messages</div>
            <?php if ($unreadMessages > 0): ?>
            <span class="badge"><?php echo $unreadMessages; ?> Unread</span>
            <?php endif; ?>
            <a href="messages.php" class="stat-link">View →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number"><?php echo $totalSubscribers; ?></div>
            <div class="stat-label">Subscribers</div>
            <a href="subscribers.php" class="stat-link">View →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-images"></i></div>
            <div class="stat-number"><?php echo $totalGallery; ?></div>
            <div class="stat-label">Gallery Images</div>
            <a href="gallery.php" class="stat-link">Manage →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            <div class="stat-number"><?php echo $totalAchievements; ?></div>
            <div class="stat-label">Achievements</div>
            <a href="achievements.php" class="stat-link">Manage →</a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="recent-activity">
        <div class="activity-section">
            <h2>Recent Quote Requests</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Area</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentQuotes as $quote): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($quote['name']); ?></td>
                            <td><?php echo htmlspecialchars($quote['product_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $quote['area']; ?> sqm</td>
                            <td><?php echo date('M d, Y', strtotime($quote['created_at'])); ?></td>
                            <td><span class="status-badge status-<?php echo $quote['status']; ?>"><?php echo ucfirst($quote['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="quotes.php" class="btn btn-secondary">View All Quotes</a>
        </div>
        
        <div class="activity-section">
            <h2>Recent Messages</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject'] ?: 'No Subject'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                            <td><span class="status-badge status-<?php echo $message['status']; ?>"><?php echo ucfirst($message['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="messages.php" class="btn btn-secondary">View All Messages</a>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>