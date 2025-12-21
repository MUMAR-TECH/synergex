<?php
// ============================================================================
// FILE: admin/ajax-handler.php - AJAX Request Handler for Admin Panel
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$db = Database::getInstance();
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'update_status':
            $table = sanitizeInput($_POST['table'] ?? '');
            $id = intval($_POST['id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');
            
            if (empty($table) || $id <= 0 || empty($status)) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }
            
            // Whitelist allowed tables
            $allowedTables = ['quote_requests', 'contact_messages', 'subscribers', 'products'];
            if (!in_array($table, $allowedTables)) {
                echo json_encode(['success' => false, 'message' => 'Invalid table']);
                exit;
            }
            
            $result = $db->update($table, ['status' => $status], 'id = ?', [$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            break;
            
        case 'delete_item':
            $table = sanitizeInput($_POST['table'] ?? '');
            $id = intval($_POST['id'] ?? 0);
            
            if (empty($table) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }
            
            // Whitelist allowed tables
            $allowedTables = ['products', 'gallery', 'achievements', 'quote_requests', 'contact_messages', 'subscribers'];
            if (!in_array($table, $allowedTables)) {
                echo json_encode(['success' => false, 'message' => 'Invalid table']);
                exit;
            }
            
            // If deleting gallery or achievement, also delete associated image
            if (in_array($table, ['gallery', 'achievements'])) {
                $item = $db->fetchOne("SELECT image FROM $table WHERE id = ?", [$id]);
                if ($item && !empty($item['image']) && file_exists(UPLOAD_PATH . $item['image'])) {
                    unlink(UPLOAD_PATH . $item['image']);
                }
            }
            
            $result = $db->delete($table, 'id = ?', [$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
            }
            break;
            
        case 'toggle_active':
            $table = sanitizeInput($_POST['table'] ?? '');
            $id = intval($_POST['id'] ?? 0);
            
            if (empty($table) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }
            
            // Whitelist allowed tables
            $allowedTables = ['products', 'partners'];
            if (!in_array($table, $allowedTables)) {
                echo json_encode(['success' => false, 'message' => 'Invalid table']);
                exit;
            }
            
            $item = $db->fetchOne("SELECT is_active FROM $table WHERE id = ?", [$id]);
            if (!$item) {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                exit;
            }
            
            $newStatus = $item['is_active'] ? 0 : 1;
            $result = $db->update($table, ['is_active' => $newStatus], 'id = ?', [$id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Status updated successfully',
                    'is_active' => $newStatus
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            break;
            
        case 'get_stats':
            // Return dashboard statistics
            $stats = [
                'total_products' => $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'],
                'active_products' => $db->fetchOne("SELECT COUNT(*) as count FROM products WHERE is_active = 1")['count'],
                'total_quotes' => $db->fetchOne("SELECT COUNT(*) as count FROM quote_requests")['count'],
                'pending_quotes' => $db->fetchOne("SELECT COUNT(*) as count FROM quote_requests WHERE status = 'pending'")['count'],
                'total_messages' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'],
                'unread_messages' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")['count'],
                'total_subscribers' => $db->fetchOne("SELECT COUNT(*) as count FROM subscribers WHERE status = 'active'")['count'],
                'total_gallery' => $db->fetchOne("SELECT COUNT(*) as count FROM gallery")['count'],
                'total_achievements' => $db->fetchOne("SELECT COUNT(*) as count FROM achievements")['count']
            ];
            
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'search':
            $table = sanitizeInput($_POST['table'] ?? '');
            $query = sanitizeInput($_POST['query'] ?? '');
            
            if (empty($table) || empty($query)) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }
            
            // Whitelist allowed tables
            $allowedTables = ['products', 'quote_requests', 'contact_messages', 'subscribers'];
            if (!in_array($table, $allowedTables)) {
                echo json_encode(['success' => false, 'message' => 'Invalid table']);
                exit;
            }
            
            // Build search query based on table
            $searchFields = [
                'products' => 'name, description',
                'quote_requests' => 'name, email, phone',
                'contact_messages' => 'name, email, subject, message',
                'subscribers' => 'email, name'
            ];
            
            $fields = $searchFields[$table] ?? 'name';
            $fieldArray = explode(', ', $fields);
            $conditions = [];
            
            foreach ($fieldArray as $field) {
                $conditions[] = "$field LIKE ?";
            }
            
            $whereClause = implode(' OR ', $conditions);
            $searchTerm = '%' . $query . '%';
            $params = array_fill(0, count($fieldArray), $searchTerm);
            
            $results = $db->fetchAll("SELECT * FROM $table WHERE $whereClause LIMIT 20", $params);
            
            echo json_encode(['success' => true, 'data' => $results]);
            break;
            
        case 'update_display_order':
            $table = sanitizeInput($_POST['table'] ?? '');
            $items = $_POST['items'] ?? [];
            
            if (empty($table) || empty($items)) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }
            
            // Whitelist allowed tables
            $allowedTables = ['gallery', 'achievements', 'partners', 'products'];
            if (!in_array($table, $allowedTables)) {
                echo json_encode(['success' => false, 'message' => 'Invalid table']);
                exit;
            }
            
            $success = true;
            foreach ($items as $item) {
                $id = intval($item['id'] ?? 0);
                $order = intval($item['order'] ?? 0);
                
                if ($id > 0) {
                    $result = $db->update($table, ['display_order' => $order], 'id = ?', [$id]);
                    if (!$result) {
                        $success = false;
                    }
                }
            }
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Display order updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update display order']);
            }
            break;
            
        // ===== CHATBOT ACTIONS =====
        case 'get_conversation':
            $conversationId = intval($_POST['conversation_id'] ?? 0);
            
            $conv = $db->query("SELECT * FROM chatbot_conversations WHERE id = ?", [$conversationId]);
            $messages = $db->query("SELECT * FROM chatbot_messages WHERE conversation_id = ? ORDER BY created_at ASC", [$conversationId]);
            
            echo json_encode([
                'success' => true, 
                'conversation' => $conv[0] ?? null,
                'messages' => $messages
            ]);
            break;
            
        case 'get_knowledge':
            $knowledge = $db->query("SELECT * FROM chatbot_knowledge ORDER BY priority DESC, created_at DESC");
            echo json_encode(['success' => true, 'knowledge' => $knowledge]);
            break;
            
        case 'get_knowledge_item':
            $id = intval($_POST['id'] ?? 0);
            $item = $db->query("SELECT * FROM chatbot_knowledge WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'item' => $item[0] ?? null]);
            break;
            
        case 'save_knowledge':
            $id = intval($_POST['id'] ?? 0);
            $question = sanitizeInput($_POST['question'] ?? '');
            $answer = sanitizeInput($_POST['answer'] ?? '');
            $category = sanitizeInput($_POST['category'] ?? 'general');
            $keywords = sanitizeInput($_POST['keywords'] ?? '');
            $priority = intval($_POST['priority'] ?? 50);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($question) || empty($answer)) {
                echo json_encode(['success' => false, 'message' => 'Question and answer are required']);
                exit;
            }
            
            $data = [
                'question' => $question,
                'answer' => $answer,
                'category' => $category,
                'keywords' => $keywords,
                'priority' => $priority,
                'is_active' => $isActive
            ];
            
            if ($id > 0) {
                $result = $db->update('chatbot_knowledge', $data, 'id = ?', [$id]);
            } else {
                $result = $db->insert('chatbot_knowledge', $data);
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Knowledge saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save knowledge']);
            }
            break;
            
        case 'delete_knowledge':
            $id = intval($_POST['id'] ?? 0);
            $result = $db->delete('chatbot_knowledge', 'id = ?', [$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Knowledge deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete knowledge']);
            }
            break;
            
        case 'get_chatbot_settings':
            $settings = [];
            $results = $db->query("SELECT setting_key, setting_value FROM chatbot_settings");
            
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            echo json_encode(['success' => true, 'settings' => $settings]);
            break;
            
        case 'save_chatbot_settings':
            $settings = [
                'chatbot_enabled' => $_POST['chatbot_enabled'] ?? '1',
                'chatbot_name' => sanitizeInput($_POST['chatbot_name'] ?? 'Synergex Assistant'),
                'chatbot_greeting' => sanitizeInput($_POST['chatbot_greeting'] ?? ''),
                'chatbot_color' => sanitizeInput($_POST['chatbot_color'] ?? '#27ae60'),
                'chatbot_position' => sanitizeInput($_POST['chatbot_position'] ?? 'bottom-right'),
                'offline_message' => sanitizeInput($_POST['offline_message'] ?? '')
            ];
            
            $success = true;
            foreach ($settings as $key => $value) {
                $result = $db->query(
                    "INSERT INTO chatbot_settings (setting_key, setting_value) VALUES (?, ?) 
                     ON DUPLICATE KEY UPDATE setting_value = ?",
                    [$key, $value, $value]
                );
                if (!$result) {
                    $success = false;
                }
            }
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    error_log('AJAX Handler Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>

