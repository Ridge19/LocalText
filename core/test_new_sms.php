<?php
// Test new SMS sending with Pusher
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing New SMS with Pusher ===\n\n";

try {
    // Get a connected device
    $device = App\Models\Device::where('status', 1)->first();
    
    if (!$device) {
        echo "❌ No connected devices found\n";
        exit;
    }
    
    echo "Using device: {$device->name} ({$device->device_id})\n";
    
    // Create a test SMS
    $sms = new App\Models\Sms();
    $sms->device_id = $device->id;
    $sms->user_id = $device->user_id;
    $sms->device_slot_number = 0;
    $sms->device_slot_name = "SIM 1";
    $sms->mobile_number = "61480597773";
    $sms->message = "Test SMS " . date('H:i:s');
    $sms->schedule = now()->format("Y-m-d H:i");
    $sms->status = 0;
    $sms->et = 1;
    $sms->save();
    
    echo "✅ SMS created with ID: {$sms->id}\n";
    echo "Message: {$sms->message}\n";
    echo "Status: {$sms->status}\n\n";
    
    // Trigger the event manually
    echo "Triggering MessageSend event...\n";
    
    $messages[] = $sms;
    
    event(new App\Events\MessageSend([
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => $messages,
        ]
    ]));
    
    echo "✅ Event triggered!\n\n";
    
    // Wait a moment and check status
    echo "Waiting 3 seconds to check status...\n";
    sleep(3);
    
    $sms->refresh();
    echo "Updated SMS status: {$sms->status}\n";
    
    if ($sms->status == 0) {
        echo "⚠️ SMS still at status 0 - Pusher event may not have been delivered\n";
        echo "This could be due to SSL certificate issues in XAMPP environment\n";
    } else {
        echo "✅ SMS status changed - event was processed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
