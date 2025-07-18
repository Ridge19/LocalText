<?php
// Test Pusher Connection
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Pusher Connection ===\n\n";

try {
    // Test basic pusher connection
    $pusher = new Pusher\Pusher(
        config('broadcasting.connections.pusher.key'),
        config('broadcasting.connections.pusher.secret'),
        config('broadcasting.connections.pusher.app_id'),
        config('broadcasting.connections.pusher.options')
    );
    
    echo "Pusher instance created successfully!\n";
    
    // Test sending a message
    $data = [
        'success' => true,
        'device_id' => 'test-device',
        'original_data' => [
            'message' => 'test message'
        ]
    ];
    
    $result = $pusher->trigger('private-message-send-test-device', 'message-send', $data);
    
    if ($result) {
        echo "✅ Test message sent to Pusher successfully!\n";
        echo "Response: " . json_encode($result) . "\n";
    } else {
        echo "❌ Failed to send test message to Pusher\n";
    }
    
} catch (Exception $e) {
    echo "❌ Pusher Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Recent SMS Event Trigger ===\n";

try {
    // Get the most recent SMS
    $recentSms = App\Models\Sms::orderBy('id', 'desc')->first();
    
    if ($recentSms && $recentSms->status == 0) {
        echo "Found recent SMS (ID: {$recentSms->id}) with status 0\n";
        echo "Attempting to trigger event manually...\n";
        
        // Manually trigger the event
        $messages[] = $recentSms;
        
        event(new App\Events\MessageSend([
            'success' => true,
            'device_id' => $recentSms->device->device_id,
            "original_data" => [
                'message' => $messages,
            ]
        ]));
        
        echo "✅ Event triggered manually!\n";
        echo "Check if SMS status changes...\n";
        
        // Check status after 2 seconds
        sleep(2);
        $recentSms->refresh();
        echo "Updated status: {$recentSms->status}\n";
        
    } else {
        echo "No recent SMS with status 0 found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Event Error: " . $e->getMessage() . "\n";
}

echo "\n=== End Test ===\n";
?>
