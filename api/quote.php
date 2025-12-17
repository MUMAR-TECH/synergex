<?php
// ============================================================================
// FILE: api/quote.php - Handle Quote Requests
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
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $productId = intval($_POST['product_id'] ?? 0);
    $area = floatval($_POST['area'] ?? 0);
    $installation = intval($_POST['installation'] ?? 0);
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($phone)) {
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
        'phone' => $phone,
        'product_id' => $productId,
        'area' => $area,
        'include_installation' => $installation,
        'message' => $message,
        'status' => 'pending'
    ];
    
    $quoteId = $db->insert('quote_requests', $data);
    
    if ($quoteId) {
        // TODO: Send email notification to admin
        echo json_encode([
            'success' => true,
            'message' => 'Quote request submitted successfully',
            'quote_id' => $quoteId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit quote request']);
    }
    
} catch (Exception $e) {
    error_log('Quote API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
