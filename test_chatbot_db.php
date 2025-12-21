<?php
/**
 * Test chatbot database connection
 */
require_once __DIR__ . '/includes/db.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<h2>Chatbot Database Test</h2>";
    
    // Check if tables exist
    $tables = ['chatbot_conversations', 'chatbot_messages', 'chatbot_knowledge', 'chatbot_settings'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ Table '$table' exists with {$result['count']} records<br>";
        } catch (PDOException $e) {
            echo "❌ Table '$table' does NOT exist - Error: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<br><h3>Database Connection: ✅ Working</h3>";
    echo "<p>Database: " . DB_NAME . "</p>";
    echo "<p>Host: " . DB_HOST . "</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
