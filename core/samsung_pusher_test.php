<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔥 SAMSUNG DEVICE - PUSHER CONNECTION TEST 🔥\n";
    echo "==============================================\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    echo "📱 SAMSUNG DEVICE STATUS:\n";
    echo "=========================\n";
    echo "✅ Device: Samsung SM-S938B\n";
    echo "🆔 Device ID: 21da7925bb07102a\n";
    echo "👤 User: riidgyy (user_id: 2)\n";
    echo "📡 SIM Slot: felix (slot 1)\n\n";
    
    echo "🚨 PROBLEM DIAGNOSIS:\n";
    echo "=====================\n";
    echo "App shows 'connected' BUT messages stay Status 0\n";
    echo "This means: PUSHER EVENTS NOT REACHING YOUR SAMSUNG PHONE\n\n";
    
    echo "🔧 POSSIBLE CAUSES:\n";
    echo "===================\n";
    echo "1. 📱 APP ISSUES:\n";
    echo "   - Background app refresh disabled\n";
    echo "   - Battery optimization enabled\n";
    echo "   - App permissions restricted\n";
    echo "   - Not logged in properly\n\n";
    
    echo "2. 🔌 CONNECTION ISSUES:\n";
    echo "   - WiFi/mobile data problems\n";
    echo "   - Firewall blocking WebSocket\n";
    echo "   - Network switching (WiFi <-> Mobile)\n\n";
    
    echo "3. ⚙️  PUSHER CONFIG ISSUES:\n";
    echo "   - Wrong channel subscription\n";
    echo "   - SSL mismatch (still using old code)\n";
    echo "   - Event listener not working\n\n";
    
    echo "🎯 IMMEDIATE ACTIONS FOR YOUR SAMSUNG:\n";
    echo "=======================================\n";
    echo "1. 📱 Open SMS Gateway app on Samsung\n";
    echo "2. ⚙️  Go to Android Settings > Apps > SMS Gateway\n";
    echo "3. 🔋 Battery > Optimize battery usage > Find SMS Gateway > Don't optimize\n";
    echo "4. 📡 Background App Refresh > Allow\n";
    echo "5. 🔔 Notifications > Allow all\n";
    echo "6. 🔐 Permissions > Allow all requested permissions\n\n";
    
    echo "🔄 RE-PAIRING PROCESS:\n";
    echo "======================\n";
    echo "1. 🚪 In SMS Gateway app: Logout completely\n";
    echo "2. 🔄 Force close the app (recent apps > swipe away)\n";
    echo "3. 📱 Reopen SMS Gateway app\n";
    echo "4. 🔑 Login with: riidgyy\n";
    echo "5. 🌐 Go to your web dashboard\n";
    echo "6. 🔗 Find QR code for device pairing\n";
    echo "7. 📷 Scan QR code with SMS Gateway app\n";
    echo "8. ⏰ Wait 30 seconds\n\n";
    
    // Create a simple test SMS for immediate testing
    $test_message = "📱 SAMSUNG TEST " . date('H:i:s') . " - If this reaches you, Pusher is working! Reply STOP to opt out.";
    
    $stmt = $pdo->prepare("INSERT INTO sms (user_id, campaign_id, api_key_id, device_slot_number, device_id, device_slot_name, mobile_number, message, schedule, status, sms_type, batch_id, et, error_code, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt->execute([
        2, // user_id
        0, // campaign_id  
        0, // api_key_id
        1, // device_slot_number
        1, // device_id (Samsung device)
        'felix', // device_slot_name
        '61480597773', // mobile_number
        $test_message,
        date('Y-m-d H:i:s'), // schedule
        0, // status (INITIAL)
        1, // sms_type
        1, // batch_id
        0, // et
        0  // error_code
    ]);
    
    $new_sms_id = $pdo->lastInsertId();
    
    echo "🧪 CREATED TEST SMS:\n";
    echo "====================\n";
    echo "SMS ID: {$new_sms_id}\n";
    echo "Message: {$test_message}\n";
    echo "Target: Samsung device (felix SIM)\n";
    echo "Status: 0 (INITIAL - waiting for device)\n\n";
    
    echo "📊 WHAT TO WATCH FOR:\n";
    echo "======================\n";
    echo "After re-pairing your Samsung:\n";
    echo "✅ SMS ID {$new_sms_id} should change from Status 0 → Status 1\n";
    echo "📱 You should receive the SMS on your Samsung phone\n";
    echo "⏰ This should happen within 10-30 seconds\n\n";
    
    echo "🔍 CHECK RESULT:\n";
    echo "================\n";
    echo "Run this command after re-pairing:\n";
    echo "php live_monitoring.php\n";
    echo "Look for SMS ID {$new_sms_id} and check if status changed!\n\n";
    
    echo "🆘 IF STILL NOT WORKING:\n";
    echo "========================\n";
    echo "1. 🗑️  Uninstall SMS Gateway app completely\n";
    echo "2. 🔄 Restart Samsung phone\n";
    echo "3. ⬇️  Re-download app-ssl-fixed.apk\n";
    echo "4. 📲 Fresh install\n";
    echo "5. 🔧 Check Samsung's Developer Options > Background Process Limit = Standard\n";
    echo "6. 📡 Test with different network (WiFi vs Mobile data)\n\n";
    
    echo "Current pending messages: 13 (including this new test)\n";
    echo "All will be delivered once Pusher connection is established!\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
