<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=localtext', 'root', '');
    echo "Connected successfully to localtext database\n";
    
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll();
    echo "Tables found: " . count($tables) . "\n";
    
    foreach($tables as $table) {
        echo "- " . $table[0] . "\n";
    }
    
    // Check specifically for general_settings table
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'localtext' AND table_name = 'general_settings'");
    $exists = $stmt->fetchColumn();
    echo "general_settings table exists: " . ($exists ? 'Yes' : 'No') . "\n";
    
} catch(Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
