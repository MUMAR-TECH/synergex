<?php
// ============================================================================
// FILE: api/contact.php - Handle Contact Form
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    $db = Database::getInstance();
    
    $data = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'status' => 'unread'
    ];
    
    $messageId = $db->insert('contact_messages', $data);
    
    if ($messageId) {
        // TODO: Send email notification to admin
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully',
            'message_id' => $messageId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    
} catch (Exception $e) {
    error_log('Contact API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>