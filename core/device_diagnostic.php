<?php
// Device App Diagnostic
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Device App Diagnostic ===\n\n";

echo "🔧 Server Configuration:\n";
echo "------------------------\n";
echo "Pusher App ID: " . env('PUSHER_APP_ID') . "\n";
echo "Pusher Key: " . substr(env('PUSHER_APP_KEY'), 0, 10) . "...\n";
echo "Pusher Cluster: " . env('PUSHER_APP_CLUSTER') . "\n";
echo "Broadcast Driver: " . env('BROADCAST_DRIVER') . "\n\n";

$device = App\Models\Device::where('device_id', '21da7925bb07102a')->first();

if ($device) {
    echo "📱 Your Device Info:\n";
    echo "-------------------\n";
    echo "Device ID: {$device->device_id}\n";
    echo "Database ID: {$device->id}\n";
    echo "User: {$device->user->username}\n";
    echo "Status: " . ($device->status ? "Connected" : "Disconnected") . "\n";
    echo "Channel: private-message-send-{$device->device_id}\n\n";
    
    echo "📋 Recent SMS for this device:\n";
    echo "------------------------------\n";
    
    $recentSms = App\Models\Sms::where('device_id', $device->id)
                               ->orderBy('id', 'desc')
                               ->limit(5)
                               ->get();
    
    foreach ($recentSms as $sms) {
        $statusText = [
            0 => 'Pending',
            1 => 'Processing', 
            2 => 'Delivered',
            3 => 'Failed'
        ];
        
        echo "SMS {$sms->id}: {$statusText[$sms->status]} - {$sms->mobile_number} - " . substr($sms->message, 0, 30) . "...\n";
    }
}

echo "\n🚨 ACTION REQUIRED ON YOUR DEVICE:\n";
echo "==================================\n";
echo "1. Open your SMS gateway app\n";
echo "2. Check if it shows 'Connected' status\n";
echo "3. Look for any error messages or alerts\n";
echo "4. Verify the app settings match:\n";
echo "   - Pusher App ID: " . env('PUSHER_APP_ID') . "\n";
echo "   - Pusher Key: " . env('PUSHER_APP_KEY') . "\n";
echo "   - Pusher Cluster: " . env('PUSHER_APP_CLUSTER') . "\n";
echo "   - Device ID: 21da7925bb07102a\n";
echo "5. Check app permissions (SMS, Phone, Notifications)\n";
echo "6. Restart the app if needed\n";
echo "7. Test sending an SMS manually from the app\n\n";

echo "💡 Common Issues:\n";
echo "-----------------\n";
echo "- App killed by battery optimization\n";
echo "- Pusher credentials mismatch\n";
echo "- SMS permissions disabled\n";
echo "- Network connectivity issues\n";
echo "- SIM card problems\n";
echo "- Carrier SMS restrictions\n\n";

echo "=== Diagnostic Complete ===\n";
?>
