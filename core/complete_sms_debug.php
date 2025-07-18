<?php
// Complete SMS System Debug
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPLETE SMS SYSTEM DEBUG ===\n\n";

// Step 1: Check database status
echo "📊 STEP 1: Database Status Check\n";
echo "================================\n";
$pendingSms = App\Models\Sms::where('status', 0)->count();
$totalSms = App\Models\Sms::count();
echo "Total SMS in database: {$totalSms}\n";
echo "Pending SMS (status 0): {$pendingSms}\n";

$recentSms = App\Models\Sms::orderBy('id', 'desc')->limit(3)->get();
echo "\nLast 3 SMS records:\n";
foreach ($recentSms as $sms) {
    echo "ID: {$sms->id} | Status: {$sms->status} | Mobile: {$sms->mobile_number} | Created: {$sms->created_at}\n";
}

// Step 2: Check devices
echo "\n📱 STEP 2: Device Status Check\n";
echo "==============================\n";
$devices = App\Models\Device::with('user')->get();
foreach ($devices as $device) {
    echo "Device: {$device->device_id}\n";
    echo "Name: {$device->name}\n";
    echo "User: {$device->user->username}\n";
    echo "Status: " . ($device->status ? "Connected" : "Disconnected") . "\n";
    echo "Channel: private-message-send-{$device->device_id}\n";
    echo "---\n";
}

// Step 3: Test Pusher connection step by step
echo "\n🔗 STEP 3: Pusher Connection Test\n";
echo "=================================\n";
try {
    echo "Creating Pusher instance...\n";
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
    echo "✅ Pusher instance created successfully\n";
    
    $device = App\Models\Device::where('status', 1)->first();
    if ($device) {
        echo "Using device: {$device->device_id}\n";
        
        // Test 1: Simple ping
        echo "\nTest 1: Sending ping...\n";
        $pingResult = $pusher->trigger(
            'private-message-send-' . $device->device_id,
            'ping',
            ['message' => 'Hello from server', 'timestamp' => time()]
        );
        echo $pingResult ? "✅ Ping sent" : "❌ Ping failed";
        echo "\n";
        
        // Test 2: Channel info
        echo "\nTest 2: Checking channel subscribers...\n";
        try {
            $channelInfo = $pusher->getChannelInfo(
                'private-message-send-' . $device->device_id,
                ['info' => 'subscription_count']
            );
            $subscribers = $channelInfo->subscription_count ?? 0;
            echo "Channel subscribers: {$subscribers}\n";
            if ($subscribers == 0) {
                echo "⚠️ NO SUBSCRIBERS - Your device app is NOT listening to this channel!\n";
                echo "This is why you're not getting SMS messages.\n";
            } else {
                echo "✅ Device is subscribed to the channel\n";
            }
        } catch (Exception $e) {
            echo "❌ Cannot get channel info: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Pusher error: " . $e->getMessage() . "\n";
}

// Step 4: Environment check
echo "\n⚙️ STEP 4: Environment Configuration\n";
echo "====================================\n";
echo "Pusher App ID: " . env('PUSHER_APP_ID') . "\n";
echo "Pusher Key: " . substr(env('PUSHER_APP_KEY'), 0, 10) . "...\n";
echo "Pusher Secret: " . (env('PUSHER_APP_SECRET') ? "Set" : "Missing") . "\n";
echo "Pusher Cluster: " . env('PUSHER_APP_CLUSTER') . "\n";
echo "Broadcast Driver: " . env('BROADCAST_DRIVER') . "\n";

// Step 5: Create and send one final test SMS
echo "\n🚀 STEP 5: Final Test SMS\n";
echo "=========================\n";
if ($device) {
    echo "Creating test SMS...\n";
    
    $sms = new App\Models\Sms();
    $sms->device_id = $device->id;
    $sms->user_id = $device->user_id;
    $sms->device_slot_number = 0;
    $sms->device_slot_name = "SIM 1";
    $sms->mobile_number = "61480597773";
    $sms->message = "🔥 FINAL TEST at " . date('H:i:s') . " - If you get this, the system works! Reply STOP to opt out.";
    $sms->schedule = now()->format("Y-m-d H:i");
    $sms->status = 0;
    $sms->et = 1;
    $sms->save();
    
    echo "✅ SMS created with ID: {$sms->id}\n";
    echo "Message: {$sms->message}\n";
    
    echo "\nSending via Pusher event...\n";
    $messages[] = $sms;
    
    event(new App\Events\MessageSend([
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => $messages,
        ]
    ]));
    
    echo "✅ Event sent!\n";
}

echo "\n🎯 DIAGNOSIS SUMMARY\n";
echo "===================\n";
echo "1. If channel subscribers = 0: Your device app is not connected to Pusher\n";
echo "2. If channel subscribers > 0: App is connected but not processing SMS events\n";
echo "3. Check your device app for error messages or connection status\n";
echo "4. Restart the SMS gateway app on your device\n";
echo "5. Verify app permissions (SMS, Phone, Background)\n";
echo "6. Check if SIM card has credit and SMS capability\n";

echo "\n📱 IMMEDIATE ACTION REQUIRED:\n";
echo "============================\n";
echo "1. Open your SMS gateway app RIGHT NOW\n";
echo "2. Check connection status in the app\n";
echo "3. Look for any error messages\n";
echo "4. If disconnected, try to reconnect\n";
echo "5. Check app settings match server credentials above\n";

echo "\n=== DEBUG COMPLETE ===\n";
?>
