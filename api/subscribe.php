<?php
// ============================================================================
// FILE: api/subscribe.php - Handle Newsletter Subscription
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $email = sanitizeInput($_POST['email'] ?? '');
    $name = sanitizeInput($_POST['name'] ?? '');
    
    // Validation
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please provide an email address']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    $db = Database::getInstance();
    
    // Check if already subscribed
    $existing = $db->fetchOne("SELECT id FROM subscribers WHERE email = ?", [$email]);
    
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'This email is already subscribed']);
        exit;
    }
    
    $data = [
        'email' => $email,
        'name' => $name,
        'status' => 'active'
    ];
    
    $subscriberId = $db->insert('subscribers', $data);
    
    if ($subscriberId) {
        // TODO: Send welcome email
        echo json_encode([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter',
            'subscriber_id' => $subscriberId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to subscribe']);
    }
    
} catch (Exception $e) {
    error_log('Subscribe API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
