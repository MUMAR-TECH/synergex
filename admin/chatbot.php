<?php
/**
 * ============================================================================
 * ADMIN CHATBOT MANAGEMENT
 * ============================================================================
 * Manage chatbot conversations and knowledge base
 */

require_once '../includes/db.php';
require_once 'includes/admin_header.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Get statistics
$stmt = $conn->query("SELECT COUNT(*) as total FROM chatbot_conversations");
$totalConversations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM chatbot_conversations WHERE status = 'active'");
$activeConversations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM chatbot_messages");
$totalMessages = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM chatbot_knowledge WHERE is_active = 1");
$activeKnowledge = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent conversations
$stmt = $conn->query("
    SELECT c.*, 
           COUNT(m.id) as message_count,
           MAX(m.created_at) as last_message_time
    FROM chatbot_conversations c
    LEFT JOIN chatbot_messages m ON c.id = m.conversation_id
    GROUP BY c.id
    ORDER BY c.last_activity DESC
    LIMIT 20
");
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all knowledge base items
$stmt = $conn->query("
    SELECT * FROM chatbot_knowledge 
    ORDER BY priority DESC, category ASC, created_at DESC
");
$knowledgeItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get chatbot settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM chatbot_settings");
$chatbotSettings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $chatbotSettings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="admin-content">
    <div class="admin-header">
        <h1><i class="fas fa-robot"></i> AI Chatbot Management</h1>
        <p>Manage chatbot conversations and knowledge base</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-comments"></i></div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalConversations; ?></div>
                <div class="stat-label">Total Conversations</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $activeConversations; ?></div>
                <div class="stat-label">Active Conversations</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalMessages; ?></div>
                <div class="stat-label">Total Messages</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $activeKnowledge; ?></div>
                <div class="stat-label">Knowledge Items</div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('conversations')">Conversations</button>
        <button class="tab-btn" onclick="showTab('knowledge')">Knowledge Base</button>
        <button class="tab-btn" onclick="showTab('settings')">Settings</button>
    </div>
    
    <!-- Conversations Tab -->
    <div id="conversations-tab" class="tab-content active">
        <div class="card">
            <div class="card-header">
                <h2>Recent Conversations</h2>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Visitor</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($conversations)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No conversations yet</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                        <tr>
                            <td><?php echo substr($conv['session_id'], 0, 15) . '...'; ?></td>
                            <td>
                                <?php 
                                if ($conv['visitor_name']) {
                                    echo htmlspecialchars($conv['visitor_name']);
                                    if ($conv['visitor_email']) {
                                        echo '<br><small>' . htmlspecialchars($conv['visitor_email']) . '</small>';
                                    }
                                } else {
                                    echo 'Anonymous';
                                }
                                ?>
                            </td>
                            <td><?php echo $conv['message_count']; ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $conv['status'] === 'active' ? 'success' : 
                                         ($conv['status'] === 'resolved' ? 'info' : 'secondary'); 
                                ?>">
                                    <?php echo ucfirst($conv['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, g:i A', strtotime($conv['started_at'])); ?></td>
                            <td><?php echo date('M j, g:i A', strtotime($conv['last_activity'])); ?></td>
                            <td>
                                <button onclick="viewConversation(<?php echo $conv['id']; ?>)" 
                                        class="btn btn-sm btn-primary">
                                    View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Knowledge Base Tab -->
    <div id="knowledge-tab" class="tab-content">
        <div class="card">
            <div class="card-header">
                <h2>Knowledge Base (<?php echo count($knowledgeItems); ?> items)</h2>
                <button onclick="showAddKnowledgeModal()" class="btn btn-primary">
                    + Add New Knowledge
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">Question</th>
                            <th width="35%">Answer</th>
                            <th width="10%">Category</th>
                            <th width="8%">Priority</th>
                            <th width="7%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($knowledgeItems)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                No knowledge items found. Click "Add New Knowledge" to create one.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($knowledgeItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(substr($item['question'], 0, 100)); ?></strong>
                                <?php if (strlen($item['question']) > 100): ?>...<?php endif; ?>
                                <?php if ($item['keywords']): ?>
                                <br><small style="color: #666;">
                                    Keywords: <?php echo htmlspecialchars(substr($item['keywords'], 0, 50)); ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars(substr($item['answer'], 0, 150)); ?>
                                    <?php if (strlen($item['answer']) > 150): ?>...<?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info"><?php echo ucfirst($item['category']); ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: <?php 
                                    echo $item['priority'] >= 80 ? '#e74c3c' : 
                                         ($item['priority'] >= 50 ? '#f39c12' : '#95a5a6'); 
                                ?>">
                                    <?php echo $item['priority']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $item['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $item['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="viewKnowledge(<?php echo $item['id']; ?>)" 
                                        class="btn btn-sm btn-info" 
                                        title="View Details"
                                        style="margin-right: 4px;">
                                    👁️
                                </button>
                                <button onclick="editKnowledge(<?php echo $item['id']; ?>)" 
                                        class="btn btn-sm btn-primary"
                                        title="Edit">
                                    ✏️
                                </button>
                                <button onclick="deleteKnowledge(<?php echo $item['id']; ?>)" 
                                        class="btn btn-sm btn-danger"
                                        title="Delete">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Settings Tab -->
    <div id="settings-tab" class="tab-content">
        <div class="card">
            <div class="card-header">
                <h2>Chatbot Settings</h2>
            </div>
            
            <form id="chatbotSettingsForm" class="admin-form">
                <div class="form-group">
                    <label>Enable Chatbot</label>
                    <select name="chatbot_enabled" class="form-control">
                        <option value="1" <?php echo ($chatbotSettings['chatbot_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo ($chatbotSettings['chatbot_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Chatbot Name</label>
                    <input type="text" name="chatbot_name" class="form-control" 
                           value="<?php echo htmlspecialchars($chatbotSettings['chatbot_name'] ?? 'Synergex Assistant'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Greeting Message</label>
                    <textarea name="chatbot_greeting" class="form-control" rows="3" required><?php echo htmlspecialchars($chatbotSettings['chatbot_greeting'] ?? 'Hello! 👋 I\'m here to help you learn about Synergex Solutions and our eco-friendly products. How can I assist you today?'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Chatbot Color</label>
                    <input type="color" name="chatbot_color" class="form-control" 
                           value="<?php echo htmlspecialchars($chatbotSettings['chatbot_color'] ?? '#27ae60'); ?>">
                </div>
                
                <div class="form-group">
                    <label>Position</label>
                    <select name="chatbot_position" class="form-control">
                        <option value="bottom-right" <?php echo ($chatbotSettings['chatbot_position'] ?? 'bottom-right') == 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                        <option value="bottom-left" <?php echo ($chatbotSettings['chatbot_position'] ?? 'bottom-right') == 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Offline Message</label>
                    <textarea name="offline_message" class="form-control" rows="2"><?php echo htmlspecialchars($chatbotSettings['offline_message'] ?? 'Thanks for your message! Our team will get back to you soon.'); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<!-- View Conversation Modal -->
<div id="conversationModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2>Conversation Details</h2>
            <button class="modal-close" onclick="closeConversationModal()">&times;</button>
        </div>
        <div class="modal-body" id="conversationDetails">
            <!-- Will be loaded via AJAX -->
        </div>
    </div>
</div>

<!-- View Knowledge Details Modal -->
<div id="viewKnowledgeModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Knowledge Details</h2>
            <button class="modal-close" onclick="closeViewKnowledgeModal()">&times;</button>
        </div>
        <div class="modal-body" id="viewKnowledgeContent">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Add/Edit Knowledge Modal -->
<div id="knowledgeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="knowledgeModalTitle">Add Knowledge</h2>
            <button class="modal-close" onclick="closeKnowledgeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="knowledgeForm" class="admin-form">
                <input type="hidden" name="id" id="knowledge_id">
                
                <div class="form-group">
                    <label>Question *</label>
                    <textarea name="question" id="knowledge_question" class="form-control" 
                              rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Answer *</label>
                    <textarea name="answer" id="knowledge_answer" class="form-control" 
                              rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="knowledge_category" class="form-control">
                        <option value="general">General</option>
                        <option value="company">Company</option>
                        <option value="products">Products</option>
                        <option value="sales">Sales</option>
                        <option value="contact">Contact</option>
                        <option value="process">Process</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Keywords (comma-separated)</label>
                    <input type="text" name="keywords" id="knowledge_keywords" class="form-control"
                           placeholder="keyword1,keyword2,keyword3">
                </div>
                
                <div class="form-group">
                    <label>Priority (0-100)</label>
                    <input type="number" name="priority" id="knowledge_priority" class="form-control"
                           value="50" min="0" max="100">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" id="knowledge_active" checked>
                        Active
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Knowledge</button>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>/assets/js/chatbot-admin.js"></script>

<?php require_once 'includes/admin_footer.php'; ?>
