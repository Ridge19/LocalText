<?php
require_once 'bootstrap/app.php';

echo "=== DEVICE STATUS CHECK ===\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";

// Check recent SMS messages
echo "📱 RECENT SMS MESSAGES:\n";
echo "======================\n";
$recent_sms = DB::table('sms')->orderBy('id', 'desc')->limit(10)->get();

foreach($recent_sms as $sms) {
    $status_text = '';
    switch($sms->status) {
        case 0: $status_text = '⏳ INITIAL (not sent to device)'; break;
        case 1: $status_text = '✅ DELIVERED'; break;
        case 2: $status_text = '🔄 PENDING'; break;
        case 9: $status_text = '❌ FAILED'; break;
        default: $status_text = '❓ UNKNOWN (' . $sms->status . ')'; break;
    }
    
    echo "ID: {$sms->id} | Status: {$status_text} | Created: {$sms->created_at}\n";
}

echo "\n📲 CONNECTED DEVICES:\n";
echo "=====================\n";
$devices = DB::table('devices')->where('user_id', 2)->get();

foreach($devices as $device) {
    echo "Device ID: {$device->id} | Name: {$device->device_slot_name} | Last Login: {$device->updated_at}\n";
}

echo "\n🔍 CHECKING PUSHER EVENTS:\n";
echo "==========================\n";
echo "App ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
echo "Cluster: " . config('broadcasting.connections.pusher.options.cluster') . "\n";
echo "Use TLS: " . (config('broadcasting.connections.pusher.options.useTLS') ? 'YES' : 'NO') . "\n";
echo "Scheme: " . config('broadcasting.connections.pusher.options.scheme') . "\n";

echo "\n⚠️  CURRENT ISSUE:\n";
echo "==================\n";
echo "Status 0 messages = Your device hasn't received/processed the SMS requests\n";
echo "This means either:\n";
echo "1. 📱 Old app is still installed (with SSL enabled)\n";
echo "2. 🔌 Device is offline/disconnected\n";
echo "3. 📡 App isn't listening to Pusher events\n\n";

echo "🔧 SOLUTION:\n";
echo "=============\n";
echo "1. Download: http://localtext.businesslocal.com.au/assets/files/apk/app-ssl-fixed.apk\n";
echo "2. Uninstall old SMS gateway app\n";
echo "3. Install new app-ssl-fixed.apk\n";
echo "4. Login and pair device again\n";
echo "5. Test SMS sending\n\n";

echo "📊 SUMMARY:\n";
echo "============\n";
$status_0_count = DB::table('sms')->where('status', 0)->count();
$status_1_count = DB::table('sms')->where('status', 1)->count();
echo "Messages with Status 0 (INITIAL): {$status_0_count}\n";
echo "Messages with Status 1 (DELIVERED): {$status_1_count}\n";
?>
