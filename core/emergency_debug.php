<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🚨 EMERGENCY DEVICE TROUBLESHOOTING 🚨\n";
    echo "=======================================\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    echo "❓ ISSUE: App says 'connected' but database shows offline\n";
    echo "This suggests one of these problems:\n\n";
    
    // Check all devices for this user
    echo "📱 ALL REGISTERED DEVICES:\n";
    echo "==========================\n";
    $stmt = $pdo->query("SELECT * FROM devices WHERE user_id = 2 ORDER BY updated_at DESC");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $device_count = count($devices);
    echo "Total devices: {$device_count}\n\n";
    
    foreach($devices as $i => $device) {
        $device_num = $i + 1;
        echo "Device #{$device_num}:\n";
        echo "  ID: {$device['id']}\n";
        echo "  Device ID: {$device['device_id']}\n";
        echo "  Name: {$device['device_name']}\n";
        echo "  Model: {$device['device_model']}\n";
        echo "  Last Active: {$device['updated_at']}\n";
        echo "  Status: " . ($device['status'] ? 'Active' : 'Inactive') . "\n";
        echo "  App Version: {$device['app_version']}\n\n";
    }
    
    // Check if there are multiple devices (old vs new)
    if($device_count > 1) {
        echo "⚠️  MULTIPLE DEVICES DETECTED!\n";
        echo "This might be the problem - you may have registered the new app as a separate device.\n\n";
    }
    
    // Let's test sending a Pusher event manually
    echo "🧪 MANUAL PUSHER TEST:\n";
    echo "======================\n";
    echo "Testing if we can send a Pusher event...\n\n";
    
    // Send a test message
    $stmt = $pdo->prepare("INSERT INTO sms (user_id, campaign_id, api_key_id, device_slot_number, device_id, device_slot_name, mobile_number, message, schedule, status, sms_type, batch_id, et, error_code, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $test_message = "🔥 EMERGENCY TEST " . date('H:i:s') . " - If you get this, the connection works! Reply STOP to opt out.";
    
    $stmt->execute([
        2, // user_id
        0, // campaign_id  
        0, // api_key_id
        1, // device_slot_number
        1, // device_id
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
    echo "✅ Emergency test SMS created with ID: {$new_sms_id}\n";
    echo "📝 Message: {$test_message}\n\n";
    
    // Now try to broadcast the event
    echo "📡 SENDING PUSHER EVENT:\n";
    echo "========================\n";
    
    // Include Laravel's broadcasting system
    $broadcast_result = shell_exec('cd .. && php artisan tinker --execute="
        use App\\Events\\MessageSend;
        $sms = (object)[
            \'id\' => ' . $new_sms_id . ',
            \'device_id\' => 1,
            \'mobile_number\' => \'61480597773\',
            \'message\' => \'' . addslashes($test_message) . '\'
        ];
        broadcast(new MessageSend($sms));
        echo \'Event broadcasted successfully!\';
    "');
    
    if($broadcast_result) {
        echo "📤 Broadcast Result: {$broadcast_result}\n";
    } else {
        echo "❌ Broadcast failed or no output\n";
    }
    
    echo "\n🔍 DEBUGGING CHECKLIST:\n";
    echo "========================\n";
    echo "1. 📱 App Connection:\n";
    echo "   - App shows 'connected' ✅\n";
    echo "   - But database last_seen is old ❌\n";
    echo "   - This means: App connects but doesn't update database\n\n";
    
    echo "2. 🔧 Possible Causes:\n";
    echo "   a) 🆔 Device ID mismatch (new app, new device ID)\n";
    echo "   b) 🔐 Authentication issue (wrong user/credentials)\n";
    echo "   c) 📡 Pusher channel mismatch\n";
    echo "   d) 🔄 App not properly updating device status\n";
    echo "   e) 🚫 Background processing restricted\n\n";
    
    echo "3. 🎯 IMMEDIATE ACTIONS:\n";
    echo "   a) 📱 Open the SMS Gateway app on your phone\n";
    echo "   b) 🔍 Check what device ID it shows\n";
    echo "   c) 🔑 Verify you're logged in as 'riidgyy'\n";
    echo "   d) 🔗 Re-scan the QR code to re-pair\n";
    echo "   e) 🔄 Check if SMS ID {$new_sms_id} gets processed\n\n";
    
    echo "4. 📋 NEXT STEPS:\n";
    echo "   - Watch SMS ID {$new_sms_id} for 2-3 minutes\n";
    echo "   - If status changes from 0 to 1, connection works\n";
    echo "   - If it stays 0, there's still a connection issue\n";
    echo "   - Check app logs or re-pair the device\n\n";
    
    echo "Run this script again in 2 minutes to see if the test message was processed!\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
