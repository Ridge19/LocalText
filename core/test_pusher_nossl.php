<?php
// Test Pusher with SSL disabled for development
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Pusher with SSL Disabled ===\n\n";

try {
    // Create Pusher instance with SSL disabled
    $options = [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => false, // Disable TLS for testing
        'host' => 'api-' . env('PUSHER_APP_CLUSTER') . '.pusher.com',
        'port' => 80,
        'scheme' => 'http'
    ];
    
    $pusher = new Pusher\Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'), 
        env('PUSHER_APP_ID'),
        $options
    );
    
    echo "Pusher instance created (SSL disabled)!\n";
    
    // Get a device to test with
    $device = App\Models\Device::where('status', 1)->first();
    if (!$device) {
        echo "❌ No connected devices found\n";
        exit;
    }
    
    echo "Testing with device: {$device->device_id}\n";
    
    // Test sending a message
    $data = [
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => [[
                'id' => 999,
                'mobile_number' => '61480597773',
                'message' => 'Test message from server',
                'device_slot_number' => 0
            ]]
        ]
    ];
    
    $result = $pusher->trigger('private-message-send-' . $device->device_id, 'message-send', $data);
    
    if ($result) {
        echo "✅ Test message sent to device successfully!\n";
        echo "Channel: private-message-send-{$device->device_id}\n";
        echo "Event: message-send\n";
        echo "Result: " . json_encode($result) . "\n";
        
        echo "\n🔥 SMS should now be sent to your device!\n";
        echo "Check your device for the SMS being processed.\n";
    } else {
        echo "❌ Failed to send test message\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
