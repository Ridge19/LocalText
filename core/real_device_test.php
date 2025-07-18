<?php
// Real Device Connection Test
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== REAL DEVICE CONNECTION TEST ===\n\n";

echo "🎯 The Problem: We're updating database status but SMS aren't reaching your phone.\n";
echo "This means your device app isn't actually processing the Pusher events.\n\n";

// Check if we can get any response from the device
$device = App\Models\Device::where('device_id', '21da7925bb07102a')->first();

if (!$device) {
    echo "❌ Device not found in database!\n";
    exit;
}

echo "📱 Testing Device: {$device->device_id}\n";
echo "User: {$device->user->username}\n";
echo "Status in DB: " . ($device->status ? "Connected" : "Disconnected") . "\n\n";

// Create a test SMS that will stay pending until device responds
echo "🧪 Creating REAL test SMS (will stay pending until device responds)...\n";

$testSms = new App\Models\Sms();
$testSms->device_id = $device->id;
$testSms->user_id = $device->user_id;
$testSms->device_slot_number = 0;
$testSms->device_slot_name = "SIM 1";
$testSms->mobile_number = "61480597773";
$testSms->message = "🚨 REAL TEST " . date('H:i:s') . " - If you get this SMS, your device is working! Reply OK to confirm.";
$testSms->schedule = now()->format("Y-m-d H:i");
$testSms->status = 0; // Keep as pending - only device should change this
$testSms->et = 1;
$testSms->save();

echo "✅ SMS created with ID: {$testSms->id}\n";
echo "Status: {$testSms->status} (will stay 0 until device processes it)\n\n";

// Send via Pusher
echo "📡 Sending Pusher event to device...\n";

try {
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
    $eventData = [
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => [$testSms->toArray()]
        ]
    ];
    
    echo "Channel: {$channelName}\n";
    echo "Event: message-send\n";
    echo "Data: " . json_encode($eventData, JSON_PRETTY_PRINT) . "\n\n";
    
    $result = $pusher->trigger($channelName, 'message-send', $eventData);
    
    if ($result) {
        echo "✅ Pusher event sent successfully!\n\n";
        
        echo "🔥 NOW CHECK YOUR DEVICE APP:\n";
        echo "=============================\n";
        echo "1. Open your SMS gateway app\n";
        echo "2. Look for any new notifications or messages\n";
        echo "3. Check if the app shows this SMS being processed\n";
        echo "4. Look for any error messages in the app\n\n";
        
        echo "⏰ Waiting 10 seconds to see if device responds...\n";
        
        for ($i = 10; $i >= 1; $i--) {
            echo "Checking in {$i} seconds...\r";
            sleep(1);
        }
        echo "\n\n";
        
        // Check if status changed
        $testSms->refresh();
        echo "📊 Status check after 10 seconds:\n";
        echo "SMS ID: {$testSms->id}\n";
        echo "Status: {$testSms->status}\n";
        
        if ($testSms->status == 0) {
            echo "❌ Status is still 0 - Device did NOT process the message\n\n";
            
            echo "🚨 DIAGNOSIS: Your device app is NOT working properly!\n";
            echo "===============================================\n";
            echo "Issues to check on your device:\n";
            echo "1. SMS gateway app is not running or crashed\n";
            echo "2. App is not connected to Pusher servers\n";
            echo "3. Wrong Pusher credentials in the app\n";
            echo "4. App doesn't have SMS permissions\n";
            echo "5. Network connectivity issues\n";
            echo "6. App is being killed by battery optimization\n\n";
            
            echo "🔧 IMMEDIATE ACTIONS:\n";
            echo "====================\n";
            echo "1. Open your SMS gateway app RIGHT NOW\n";
            echo "2. Check connection status in the app\n";
            echo "3. Verify these settings match:\n";
            echo "   - Pusher App ID: " . env('PUSHER_APP_ID') . "\n";
            echo "   - Pusher Key: " . env('PUSHER_APP_KEY') . "\n";
            echo "   - Pusher Cluster: " . env('PUSHER_APP_CLUSTER') . "\n";
            echo "   - Device ID: 21da7925bb07102a\n";
            echo "4. Restart the app completely\n";
            echo "5. Check app permissions (SMS, Phone, Background)\n";
            
        } else {
            echo "✅ Status changed to {$testSms->status} - Device is working!\n";
            echo "🎉 Your SMS system is operational!\n";
        }
        
    } else {
        echo "❌ Failed to send Pusher event\n";
    }
    
} catch (Exception $e) {
    echo "❌ Pusher Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
