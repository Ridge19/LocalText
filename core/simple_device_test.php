<?php
// Simple Device Test
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Simple Device Test ===\n\n";

// Get all devices
$devices = App\Models\Device::with('user')->get();

echo "📱 Available Devices:\n";
echo "-------------------\n";
foreach ($devices as $device) {
    echo "ID: {$device->id}\n";
    echo "Device ID: {$device->device_id}\n";
    echo "Name: {$device->name}\n";
    echo "User: {$device->user->username}\n";
    echo "Status: " . ($device->status == 1 ? "✅ Connected" : "❌ Disconnected") . "\n";
    echo "Channel: private-message-send-{$device->device_id}\n";
    echo "---\n";
}

// Get the first connected device
$device = App\Models\Device::where('status', 1)->first();

if (!$device) {
    echo "❌ No connected devices found!\n";
    exit;
}

echo "\n🎯 Using Device: {$device->device_id}\n";
echo "📡 Sending URGENT test message...\n\n";

try {
    // Create Pusher instance
    $pusher = new Pusher\Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => false,
            'host' => 'api-' . env('PUSHER_APP_CLUSTER') . '.pusher.com',
            'port' => 80,
            'scheme' => 'http'
        ]
    );
    
    $channelName = 'private-message-send-' . $device->device_id;
    
    // Send urgent test SMS
    $urgentData = [
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => [[
                'id' => 888888,
                'mobile_number' => '61480597773',
                'message' => '🚨 URGENT: Device connection test. If you receive this SMS, reply "WORKING" to confirm the system is operational.',
                'device_slot_number' => 0,
                'device_slot_name' => 'SIM 1'
            ]]
        ]
    ];
    
    echo "Sending to channel: {$channelName}\n";
    echo "Event: message-send\n";
    echo "Device ID: {$device->device_id}\n\n";
    
    $result = $pusher->trigger($channelName, 'message-send', $urgentData);
    
    if ($result) {
        echo "✅ URGENT MESSAGE SENT!\n\n";
        echo "🔥🔥🔥 CHECK YOUR PHONE NOW! 🔥🔥🔥\n";
        echo "Phone: 61480597773\n";
        echo "Expected SMS: '🚨 URGENT: Device connection test...'\n\n";
        echo "If you DON'T receive this SMS within 30 seconds:\n";
        echo "1. Your device app is not properly connected to Pusher\n";
        echo "2. App may not be running in background\n";
        echo "3. App may not have SMS sending permissions\n";
        echo "4. Wrong Pusher credentials in the app\n";
    } else {
        echo "❌ Failed to send message!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
