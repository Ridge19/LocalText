<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 REAL-TIME DEVICE MONITORING 🔍\n";
    echo "==================================\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Check device status
    echo "📱 DEVICE STATUS CHECK:\n";
    echo "=======================\n";
    $stmt = $pdo->query("SELECT * FROM devices WHERE user_id = 2 ORDER BY updated_at DESC");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($devices)) {
        echo "❌ NO DEVICES FOUND!\n";
        echo "This means:\n";
        echo "1. 📱 App not installed on phone\n";
        echo "2. 🔑 Not logged in\n";
        echo "3. 🔗 Device not paired\n\n";
    } else {
        foreach($devices as $device) {
            $last_seen = strtotime($device['updated_at']);
            $minutes_ago = round((time() - $last_seen) / 60);
            $hours_ago = round($minutes_ago / 60);
            $days_ago = round($hours_ago / 24);
            
            if($minutes_ago < 5) {
                $status_emoji = "🟢";
                $status_text = "ONLINE (last seen {$minutes_ago} minutes ago)";
            } elseif($hours_ago < 24) {
                $status_emoji = "🟡";
                $status_text = "RECENT (last seen {$hours_ago} hours ago)";
            } else {
                $status_emoji = "🔴";
                $status_text = "OFFLINE (last seen {$days_ago} days ago)";
            }
            
            echo "{$status_emoji} Device: {$device['device_name']} ({$device['device_model']})\n";
            echo "   📱 Device ID: {$device['device_id']}\n";
            echo "   📅 Last Seen: {$device['updated_at']}\n";
            echo "   ⏰ Status: {$status_text}\n";
            echo "   📡 SIM: {$device['sim']}\n";
            echo "   🔢 App Version: {$device['app_version']}\n\n";
        }
    }
    
    // Check recent SMS status
    echo "📊 RECENT SMS STATUS (Last 5):\n";
    echo "===============================\n";
    $stmt = $pdo->query("SELECT id, status, message, created_at, updated_at FROM sms ORDER BY id DESC LIMIT 5");
    $recent_sms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($recent_sms as $sms) {
        $status_emoji = '';
        $status_text = '';
        switch($sms['status']) {
            case 0: $status_emoji = '⏳'; $status_text = 'INITIAL (waiting)'; break;
            case 1: $status_emoji = '✅'; $status_text = 'DELIVERED'; break;
            case 2: $status_emoji = '🔄'; $status_text = 'PENDING'; break;
            case 9: $status_emoji = '❌'; $status_text = 'FAILED'; break;
        }
        
        $short_msg = substr($sms['message'], 0, 40) . '...';
        echo "{$status_emoji} SMS {$sms['id']}: {$status_text}\n";
        echo "   📝 Message: {$short_msg}\n";
        echo "   📅 Created: {$sms['created_at']}\n";
        echo "   🔄 Updated: {$sms['updated_at']}\n\n";
    }
    
    // Count status 0 messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sms WHERE status = 0");
    $status_0_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "🎯 CURRENT ISSUES:\n";
    echo "==================\n";
    
    if($status_0_count > 0) {
        echo "❌ {$status_0_count} messages still stuck in Status 0\n";
        echo "This means your device is still not processing SMS requests!\n\n";
        
        echo "🔍 TROUBLESHOOTING STEPS:\n";
        echo "=========================\n";
        echo "1. 📱 CHECK YOUR PHONE:\n";
        echo "   - Is the new SMS Gateway app installed?\n";
        echo "   - Is it running/open?\n";
        echo "   - Are you logged in?\n\n";
        
        echo "2. 🔗 CHECK PAIRING:\n";
        echo "   - Go to your web dashboard\n";
        echo "   - Look for QR code\n";
        echo "   - Scan it with the app to pair\n\n";
        
        echo "3. 📡 CHECK CONNECTION:\n";
        echo "   - Is your phone connected to internet?\n";
        echo "   - Is the app allowed to run in background?\n";
        echo "   - Is battery optimization disabled?\n\n";
        
        echo "4. 🆕 VERIFY NEW APK:\n";
        echo "   - Did you download app-ssl-fixed.apk?\n";
        echo "   - Did you uninstall the old app first?\n";
        echo "   - Is the new app version installed?\n\n";
        
    } else {
        echo "✅ All messages are being processed correctly!\n";
    }
    
    echo "📋 WHAT TO DO RIGHT NOW:\n";
    echo "========================\n";
    echo "1. 📱 Look at your phone\n";
    echo "2. 🔍 Check if SMS Gateway app is installed and running\n";
    echo "3. 🔑 Make sure you're logged in (username: riidgyy)\n";
    echo "4. 🔗 Scan QR code from dashboard to pair\n";
    echo "5. 🧪 Send a test SMS to see if it works\n\n";
    
    echo "💡 REMEMBER:\n";
    echo "=============\n";
    echo "The new APK (app-ssl-fixed.apk) is ESSENTIAL!\n";
    echo "Without it, the SSL mismatch will prevent communication.\n";
    echo "Download from: http://localtext.businesslocal.com.au/assets/files/apk/app-ssl-fixed.apk\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
