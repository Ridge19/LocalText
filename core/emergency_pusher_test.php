<?php
require_once 'bootstrap/app.php';
use App\Events\MessageSend;

echo "🔥 EMERGENCY PUSHER TEST 🔥\n";
echo "============================\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";

// Create a test SMS
$sms_data = [
    'user_id' => 2,
    'campaign_id' => 0,
    'api_key_id' => 0,
    'device_slot_number' => 1,
    'device_id' => 1,
    'device_slot_name' => 'felix',
    'mobile_number' => '61480597773',
    'message' => '🆘 EMERGENCY CONNECTION TEST ' . date('H:i:s') . ' - Testing if Pusher events reach your device!',
    'schedule' => now(),
    'status' => 0,
    'sms_type' => 1,
    'batch_id' => 1,
    'et' => 0,
    'error_code' => 0,
    'created_at' => now(),
    'updated_at' => now()
];

// Insert the SMS
$sms_id = DB::table('sms')->insertGetId($sms_data);
echo "✅ Created test SMS with ID: {$sms_id}\n";
echo "📝 Message: {$sms_data['message']}\n\n";

// Create SMS object for broadcasting
$sms = (object) [
    'id' => $sms_id,
    'device_id' => 1,
    'mobile_number' => '61480597773',
    'message' => $sms_data['message']
];

echo "📡 Broadcasting Pusher event...\n";

try {
    // Broadcast the event
    broadcast(new App\Events\MessageSend($sms));
    echo "✅ Pusher event sent successfully!\n\n";
    
    // Show connection details
    echo "🔧 PUSHER CONFIGURATION:\n";
    echo "========================\n";
    echo "App ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
    echo "Key: " . config('broadcasting.connections.pusher.key') . "\n";
    echo "Secret: " . substr(config('broadcasting.connections.pusher.secret'), 0, 10) . "...\n";
    echo "Cluster: " . config('broadcasting.connections.pusher.options.cluster') . "\n";
    echo "Use TLS: " . (config('broadcasting.connections.pusher.options.useTLS') ? 'YES' : 'NO') . "\n";
    echo "Scheme: " . config('broadcasting.connections.pusher.options.scheme') . "\n";
    echo "Host: " . config('broadcasting.connections.pusher.options.host') . "\n";
    echo "Port: " . config('broadcasting.connections.pusher.options.port') . "\n\n";
    
    echo "📱 WHAT TO CHECK ON YOUR PHONE:\n";
    echo "================================\n";
    echo "1. 🔍 Open SMS Gateway app\n";
    echo "2. 📊 Check if it shows any activity/logs\n";
    echo "3. 🆔 Verify the device ID matches: 21da7925bb07102a\n";
    echo "4. 🔑 Make sure you're logged in as: riidgyy\n";
    echo "5. 📡 Check if it's listening to the right channel\n\n";
    
    echo "⏱️  TESTING TIMELINE:\n";
    echo "======================\n";
    echo "- SMS ID {$sms_id} created at " . date('H:i:s') . "\n";
    echo "- Pusher event sent\n";
    echo "- Check in 30 seconds if status changed from 0 to 1\n";
    echo "- If no change, there's a connection issue\n\n";
    
    echo "🧪 RUN THIS TO CHECK RESULT:\n";
    echo "=============================\n";
    echo "Wait 30 seconds, then run: php emergency_debug.php\n";
    echo "Look for SMS ID {$sms_id} and see if status changed!\n";
    
} catch (Exception $e) {
    echo "❌ Error broadcasting: " . $e->getMessage() . "\n";
    echo "This might indicate a configuration problem.\n";
}
?>
