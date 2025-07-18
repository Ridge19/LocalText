<?php
// Device Connection and Channel Test
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Device Connection & Channel Test ===\n\n";

// Get all devices
$devices = App\Models\Device::with('user')->orderBy('last_seen', 'desc')->get();

echo "📱 Device Status:\n";
echo "----------------\n";
foreach ($devices as $device) {
    echo "Device: {$device->name} ({$device->device_id})\n";
    echo "User: {$device->user->username}\n";
    echo "Status: " . ($device->status == 1 ? "✅ Connected" : "❌ Disconnected") . "\n";
    echo "Last Seen: {$device->last_seen}\n";
    echo "Created: {$device->created_at}\n";
    
    // Test the exact channel name
    $channelName = 'private-message-send-' . $device->device_id;
    echo "Channel: {$channelName}\n";
    echo "---\n";
}

echo "\n🔍 Testing Pusher Channels:\n";
echo "----------------------------\n";

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
    
    // Test each connected device
    foreach ($devices as $device) {
        if ($device->status == 1) {
            echo "Testing device: {$device->device_id}\n";
            
            $channelName = 'private-message-send-' . $device->device_id;
            
            // Send a simple ping message
            $testData = [
                'type' => 'ping',
                'timestamp' => time(),
                'message' => 'Connection test from server'
            ];
            
            $result = $pusher->trigger($channelName, 'connection-test', $testData);
            
            if ($result) {
                echo "✅ Ping sent to {$channelName}\n";
            } else {
                echo "❌ Failed to send ping to {$channelName}\n";
            }
            
            // Now send a real SMS test
            $smsData = [
                'success' => true,
                'device_id' => $device->device_id,
                'original_data' => [
                    'message' => [[
                        'id' => 999999,
                        'mobile_number' => '61480597773',
                        'message' => '🔥 URGENT TEST - Reply "OK" if you receive this via Pusher',
                        'device_slot_number' => 0,
                        'device_slot_name' => 'SIM 1'
                    ]]
                ]
            ];
            
            $result2 = $pusher->trigger($channelName, 'message-send', $smsData);
            
            if ($result2) {
                echo "✅ SMS event sent to {$channelName}\n";
                echo "📱 CHECK YOUR DEVICE NOW!\n";
            } else {
                echo "❌ Failed to send SMS event\n";
            }
            
            echo "---\n";
        }
    }
    
    // Get channel info
    echo "\n📊 Pusher Channel Info:\n";
    echo "-----------------------\n";
    
    foreach ($devices as $device) {
        if ($device->status == 1) {
            $channelName = 'private-message-send-' . $device->device_id;
            
            try {
                $channelInfo = $pusher->getChannelInfo($channelName, ['info' => 'subscription_count']);
                echo "Channel: {$channelName}\n";
                echo "Subscribers: " . ($channelInfo->subscription_count ?? 0) . "\n";
                
                if (($channelInfo->subscription_count ?? 0) == 0) {
                    echo "⚠️ No subscribers - device app may not be listening!\n";
                } else {
                    echo "✅ Device is subscribed to channel\n";
                }
                echo "---\n";
            } catch (Exception $e) {
                echo "❌ Could not get channel info: " . $e->getMessage() . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Pusher Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nDEBUG STEPS:\n";
echo "1. Check if your device app is actually running\n";
echo "2. Verify the app is logged in with correct credentials\n";
echo "3. Check if app has internet connection\n";
echo "4. Look for any error messages in the app\n";
echo "5. Try restarting the SMS gateway app\n";
echo "6. Check app permissions (SMS, notification, background)\n";
?>
