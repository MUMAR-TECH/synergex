<?php
/**
 * ============================================================================
 * CHATBOT API ENDPOINT
 * ============================================================================
 * Handles chatbot messages and responses
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Enable CORS for AJAX requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
    switch ($action) {
        case 'init':
            echo json_encode(initChatbot());
            break;
            
        case 'send_message':
            $message = $data['message'] ?? '';
            $sessionId = $data['session_id'] ?? '';
            $conversationId = $data['conversation_id'] ?? null;
            echo json_encode(sendMessage($message, $sessionId, $conversationId));
            break;
            
        case 'save_contact':
            $sessionId = $data['session_id'] ?? '';
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $phone = $data['phone'] ?? '';
            echo json_encode(saveContactInfo($sessionId, $name, $email, $phone));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

/**
 * Initialize chatbot session
 */
function initChatbot() {
    global $conn;
    
    // Get chatbot settings
    $settings = getChatbotSettings();
    
    // Generate session ID
    $sessionId = uniqid('chat_', true);
    
    // Create new conversation
    $stmt = $conn->prepare("INSERT INTO chatbot_conversations (session_id) VALUES (?)");
    $stmt->execute([$sessionId]);
    $conversationId = $conn->lastInsertId();
    
    // Add greeting message
    $greeting = $settings['chatbot_greeting'] ?? 'Hello! How can I help you today?';
    $stmt = $conn->prepare("INSERT INTO chatbot_messages (conversation_id, message, sender) VALUES (?, ?, 'bot')");
    $stmt->execute([$conversationId, $greeting]);
    
    return [
        'success' => true,
        'session_id' => $sessionId,
        'conversation_id' => $conversationId,
        'greeting' => $greeting,
        'settings' => $settings
    ];
}

/**
 * Process user message and get bot response
 */
function sendMessage($message, $sessionId, $conversationId = null) {
    global $conn;
    
    if (empty($message) || empty($sessionId)) {
        return ['success' => false, 'message' => 'Missing required data'];
    }
    
    // Sanitize message
    $message = trim($message);
    
    // Get or create conversation
    if (!$conversationId) {
        $stmt = $conn->prepare("SELECT id FROM chatbot_conversations WHERE session_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId]);
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$conv) {
            // Create new conversation
            $stmt = $conn->prepare("INSERT INTO chatbot_conversations (session_id) VALUES (?)");
            $stmt->execute([$sessionId]);
            $conversationId = $conn->lastInsertId();
        } else {
            $conversationId = $conv['id'];
        }
    }
    
    // Save user message
    $stmt = $conn->prepare("INSERT INTO chatbot_messages (conversation_id, message, sender) VALUES (?, ?, 'user')");
    $stmt->execute([$conversationId, $message]);
    
    // Get bot response
    $response = getBotResponse($message);
    
    // Save bot response
    $stmt = $conn->prepare("INSERT INTO chatbot_messages (conversation_id, message, sender, intent) VALUES (?, ?, 'bot', ?)");
    $stmt->execute([$conversationId, $response['answer'], $response['intent']]);
    
    return [
        'success' => true,
        'response' => $response['answer'],
        'intent' => $response['intent'],
        'suggestions' => $response['suggestions'] ?? []
    ];
}

/**
 * Get bot response based on user message
 */
function getBotResponse($message) {
    global $conn;
    
    $message = strtolower($message);
    
    // Check for greetings
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    foreach ($greetings as $greeting) {
        if (strpos($message, $greeting) !== false) {
            return [
                'answer' => "Hello! Welcome to Synergex Solutions. I'm here to help you learn about our eco-friendly products and services. What would you like to know?",
                'intent' => 'greeting',
                'suggestions' => [
                    'What products do you offer?',
                    'How do I get a quote?',
                    'Tell me about your company'
                ]
            ];
        }
    }
    
    // Check for goodbye
    $goodbyes = ['bye', 'goodbye', 'see you', 'thanks', 'thank you'];
    foreach ($goodbyes as $goodbye) {
        if (strpos($message, $goodbye) !== false && strlen($message) < 20) {
            return [
                'answer' => "Thank you for chatting with us! If you need anything else, feel free to ask. You can also contact us at +260 770 377471 or visit our contact page. Have a great day!",
                'intent' => 'goodbye'
            ];
        }
    }
    
    // Search knowledge base
    $stmt = $conn->prepare("
        SELECT question, answer, category 
        FROM chatbot_knowledge 
        WHERE is_active = 1 
        ORDER BY priority DESC
    ");
    $stmt->execute();
    $knowledge = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Score matching
    $bestMatch = null;
    $bestScore = 0;
    
    foreach ($knowledge as $item) {
        $score = 0;
        
        // Check keywords
        if (!empty($item['keywords'])) {
            $keywords = explode(',', strtolower($item['keywords']));
            foreach ($keywords as $keyword) {
                if (strpos($message, trim($keyword)) !== false) {
                    $score += 3;
                }
            }
        }
        
        // Check question similarity
        $questionWords = explode(' ', strtolower($item['question']));
        foreach ($questionWords as $word) {
            if (strlen($word) > 3 && strpos($message, $word) !== false) {
                $score += 2;
            }
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $item;
        }
    }
    
    // If good match found
    if ($bestScore >= 3 && $bestMatch) {
        $suggestions = getSuggestions($bestMatch['category']);
        return [
            'answer' => $bestMatch['answer'],
            'intent' => $bestMatch['category'],
            'suggestions' => $suggestions
        ];
    }
    
    // Default response with helpful suggestions
    return [
        'answer' => "I'm not quite sure about that, but I'd be happy to help you with information about:\n\n• Our eco-friendly products (pavers & tiles)\n• Getting a quote or placing an order\n• Our recycling process\n• Company information and mission\n\nYou can also contact us directly:\nWhatsApp: +260 770 377471\nUse our contact form\n\nWhat would you like to know?",
        'intent' => 'unknown',
        'suggestions' => [
            'What products do you offer?',
            'How do I get a quote?',
            'Tell me about recycling process',
            'Where are you located?'
        ]
    ];
}

/**
 * Get relevant suggestions based on category
 */
function getSuggestions($category) {
    $suggestions = [
        'company' => [
            'What products do you offer?',
            'How do I get a quote?',
            'What is your environmental impact?'
        ],
        'products' => [
            'How do I place an order?',
            'What are the benefits of eco-pavers?',
            'Do you offer delivery?'
        ],
        'sales' => [
            'What products do you offer?',
            'Do you offer delivery?',
            'Can I visit your facility?'
        ],
        'contact' => [
            'How do I get a quote?',
            'What are your working hours?',
            'Can I visit your facility?'
        ],
        'process' => [
            'What products do you offer?',
            'What is your environmental impact?',
            'What types of plastic do you recycle?'
        ]
    ];
    
    return $suggestions[$category] ?? [
        'What products do you offer?',
        'How do I get a quote?',
        'Tell me about your company'
    ];
}

/**
 * Save contact information
 */
function saveContactInfo($sessionId, $name, $email, $phone) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE chatbot_conversations 
        SET visitor_name = ?, visitor_email = ?, visitor_phone = ? 
        WHERE session_id = ?
    ");
    $stmt->execute([$name, $email, $phone, $sessionId]);
    
    return [
        'success' => true,
        'message' => 'Thank you! We have your contact information and will reach out to you soon.'
    ];
}

/**
 * Get chatbot settings
 */
function getChatbotSettings() {
    global $conn;
    
    $stmt = $conn->query("SELECT setting_key, setting_value FROM chatbot_settings");
    $settings = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}
