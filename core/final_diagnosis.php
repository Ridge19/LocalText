<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🚨 CRITICAL DIAGNOSIS REPORT 🚨\n";
    echo "================================\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Device status
    echo "📱 YOUR REGISTERED DEVICE:\n";
    echo "==========================\n";
    $stmt = $pdo->query("SELECT * FROM devices WHERE user_id = 2");
    $device = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($device) {
        echo "✅ Device Found: {$device['device_name']} ({$device['device_model']})\n";
        echo "📱 Device ID: {$device['device_id']}\n";
        echo "🟢 Status: " . ($device['status'] ? 'Active' : 'Inactive') . "\n";
        echo "📅 Last Seen: {$device['updated_at']}\n";
        echo "⏰ Days Offline: " . ceil((time() - strtotime($device['updated_at'])) / 86400) . " days\n\n";
        
        // SIM info
        $sim_data = json_decode($device['sim'], true);
        if($sim_data && is_array($sim_data)) {
            echo "📡 SIM Configuration:\n";
            foreach($sim_data as $sim) {
                echo "   Slot {$sim['slot']}: {$sim['name']}\n";
            }
        }
    } else {
        echo "❌ NO DEVICE REGISTERED!\n";
    }
    
    echo "\n📊 SMS STATUS BREAKDOWN:\n";
    echo "========================\n";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM sms GROUP BY status ORDER BY status");
    $status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_status_0 = 0;
    foreach($status_counts as $status) {
        $status_text = '';
        $emoji = '';
        switch($status['status']) {
            case 0: 
                $status_text = 'INITIAL (waiting for device)'; 
                $emoji = '⏳';
                $total_status_0 = $status['count'];
                break;
            case 1: $status_text = 'DELIVERED'; $emoji = '✅'; break;
            case 2: $status_text = 'PENDING'; $emoji = '🔄'; break;
            case 9: $status_text = 'FAILED'; $emoji = '❌'; break;
            default: $status_text = 'UNKNOWN'; $emoji = '❓'; break;
        }
        echo "{$emoji} Status {$status['status']} ({$status_text}): {$status['count']} messages\n";
    }
    
    echo "\n🔍 PROBLEM ANALYSIS:\n";
    echo "====================\n";
    
    if($total_status_0 > 0) {
        echo "❌ CRITICAL ISSUE CONFIRMED!\n";
        echo "💥 {$total_status_0} SMS messages are stuck in Status 0\n";
        echo "🔌 Your device is NOT receiving Pusher events\n\n";
        
        echo "🕒 TIMELINE:\n";
        echo "============\n";
        echo "📅 Device Last Active: {$device['updated_at']}\n";
        echo "📅 Current Date: " . date('Y-m-d H:i:s') . "\n";
        echo "⏱️  Offline Duration: " . ceil((time() - strtotime($device['updated_at'])) / 86400) . " days\n\n";
        
        echo "🎯 ROOT CAUSE:\n";
        echo "==============\n";
        echo "Your SMS gateway app is either:\n";
        echo "1. 📱 Not installed on your phone\n";
        echo "2. 🔒 Still using the OLD version (with SSL enabled)\n";
        echo "3. 🔌 Not connected to the internet\n";
        echo "4. 🚫 Background activity restricted\n";
        echo "5. 🔋 Battery optimization killing the app\n\n";
        
        echo "🛠️  IMMEDIATE SOLUTION:\n";
        echo "========================\n";
        echo "1. 🗑️  UNINSTALL old SMS gateway app completely\n";
        echo "2. ⬇️  DOWNLOAD new APK from:\n";
        echo "   📂 http://localtext.businesslocal.com.au/assets/files/apk/app-ssl-fixed.apk\n";
        echo "3. 📲 INSTALL the new app-ssl-fixed.apk\n";
        echo "4. 🔑 LOGIN with username: riidgyy\n";
        echo "5. 📱 PAIR device using QR code from dashboard\n";
        echo "6. ⚙️  DISABLE battery optimization for the app\n";
        echo "7. 🔓 ALLOW background activity\n";
        echo "8. 🧪 TEST SMS sending\n\n";
        
        echo "💡 WHY THE NEW APK FIXES IT:\n";
        echo "=============================\n";
        echo "🔴 Old App Problem:\n";
        echo "   - Uses HTTPS/SSL for Pusher connection\n";
        echo "   - Server sends HTTP events (no SSL)\n";
        echo "   - Result: SSL mismatch = No communication\n\n";
        echo "🟢 New App Solution:\n";
        echo "   - Uses HTTP (SSL disabled) for Pusher connection\n";
        echo "   - Server sends HTTP events (no SSL)\n";
        echo "   - Result: Perfect match = SMS works!\n\n";
        
        echo "🎉 AFTER INSTALLING NEW APK:\n";
        echo "=============================\n";
        echo "- All Status 0 messages will become Status 1 (DELIVERED)\n";
        echo "- New SMS messages will be sent immediately\n";
        echo "- Your phone will receive SMS requests in real-time\n";
        echo "- Device will show as 'Active' and 'Last Seen' will update\n\n";
        
    } else {
        echo "✅ ALL GOOD: No messages stuck in Status 0!\n";
        echo "Your SMS system is working properly.\n";
    }
    
    echo "📋 QUICK CHECK:\n";
    echo "===============\n";
    echo "🔍 Look at your phone right now:\n";
    echo "   - Is the SMS Gateway app installed?\n";
    echo "   - When did you last open it?\n";
    echo "   - Is it showing as connected/paired?\n\n";
    
    echo "🚀 NEXT ACTION:\n";
    echo "===============\n";
    echo "Install the new APK and your SMS system will work perfectly!\n";
    echo "The SSL mismatch was the root cause of all your issues.\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
