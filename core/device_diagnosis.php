<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEVICE STATUS CHECK ===\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Check recent SMS messages
    echo "📱 RECENT SMS MESSAGES:\n";
    echo "======================\n";
    $stmt = $pdo->query("SELECT id, status, message, created_at FROM sms ORDER BY id DESC LIMIT 10");
    $recent_sms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($recent_sms as $sms) {
        $status_text = '';
        switch($sms['status']) {
            case 0: $status_text = '⏳ INITIAL (not sent to device)'; break;
            case 1: $status_text = '✅ DELIVERED'; break;
            case 2: $status_text = '🔄 PENDING'; break;
            case 9: $status_text = '❌ FAILED'; break;
            default: $status_text = '❓ UNKNOWN (' . $sms['status'] . ')'; break;
        }
        
        $short_msg = substr($sms['message'], 0, 30) . '...';
        echo "ID: {$sms['id']} | Status: {$status_text} | Created: {$sms['created_at']}\n";
        echo "   Message: {$short_msg}\n\n";
    }
    
    echo "📲 CONNECTED DEVICES:\n";
    echo "=====================\n";
    $stmt = $pdo->query("SELECT id, name, updated_at FROM devices WHERE user_id = 2");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($devices as $device) {
        echo "Device ID: {$device['id']} | Name: {$device['name']} | Last Activity: {$device['updated_at']}\n";
    }
    
    echo "\n📊 SMS STATUS SUMMARY:\n";
    echo "======================\n";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM sms GROUP BY status ORDER BY status");
    $status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($status_counts as $status) {
        $status_text = '';
        switch($status['status']) {
            case 0: $status_text = 'INITIAL (waiting for device)'; break;
            case 1: $status_text = 'DELIVERED'; break;
            case 2: $status_text = 'PENDING'; break;
            case 9: $status_text = 'FAILED'; break;
            default: $status_text = 'UNKNOWN'; break;
        }
        echo "Status {$status['status']} ({$status_text}): {$status['count']} messages\n";
    }
    
    echo "\n⚠️  DIAGNOSIS:\n";
    echo "==============\n";
    
    $status_0_count = 0;
    foreach($status_counts as $status) {
        if($status['status'] == 0) {
            $status_0_count = $status['count'];
            break;
        }
    }
    
    if($status_0_count > 0) {
        echo "❌ PROBLEM: {$status_0_count} messages are stuck in Status 0 (INITIAL)\n";
        echo "This means your Android device is NOT processing SMS requests!\n\n";
        
        echo "🔧 REQUIRED ACTIONS:\n";
        echo "====================\n";
        echo "1. 📱 CHECK YOUR PHONE: Is the old SMS gateway app still installed?\n";
        echo "2. 🗑️  UNINSTALL: Remove the old app completely\n";
        echo "3. ⬇️  DOWNLOAD: Get the new APK from your dashboard\n";
        echo "   URL: http://localtext.businesslocal.com.au/assets/files/apk/app-ssl-fixed.apk\n";
        echo "4. 📲 INSTALL: Install the new app-ssl-fixed.apk\n";
        echo "5. 🔑 LOGIN: Use your credentials (riidgyy)\n";
        echo "6. 🔗 PAIR: Scan the QR code to pair your device\n";
        echo "7. 🧪 TEST: Send a test SMS\n\n";
        
        echo "💡 WHY THIS HAPPENS:\n";
        echo "====================\n";
        echo "- Old app: Uses HTTPS (SSL enabled)\n";
        echo "- Server: Sends HTTP events (SSL disabled)\n";
        echo "- Result: App can't receive Pusher events = No SMS processing\n";
        echo "- Solution: New app with SSL disabled = Perfect match!\n";
    } else {
        echo "✅ GOOD: All messages are being processed properly!\n";
    }
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
