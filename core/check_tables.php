<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TABLE STRUCTURE CHECK ===\n";
    
    // Check devices table structure
    echo "📲 DEVICES TABLE COLUMNS:\n";
    echo "========================\n";
    $stmt = $pdo->query("DESCRIBE devices");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n📱 SMS TABLE COLUMNS:\n";
    echo "====================\n";
    $stmt = $pdo->query("DESCRIBE sms");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n📊 DEVICE DATA:\n";
    echo "===============\n";
    $stmt = $pdo->query("SELECT * FROM devices WHERE user_id = 2");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($devices)) {
        echo "❌ NO DEVICES FOUND for user_id = 2\n";
        echo "This means your device is not paired/connected!\n";
    } else {
        foreach($devices as $device) {
            echo "Device found:\n";
            foreach($device as $key => $value) {
                echo "  {$key}: {$value}\n";
            }
            echo "\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
