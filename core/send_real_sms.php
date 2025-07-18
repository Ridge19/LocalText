<?php
// Send Real SMS to Device
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Sending Real SMS to Your Device ===\n\n";

// Get the first connected device
$device = App\Models\Device::where('status', 1)->with('user')->first();

if (!$device) {
    echo "❌ No connected devices found\n";
    exit;
}

echo "Using device: {$device->name} ({$device->device_id})\n";
echo "Device user: {$device->user->username}\n\n";

// Create SMS record
$sms = new App\Models\Sms();
$sms->device_id = $device->id;
$sms->user_id = $device->user_id;
$sms->device_slot_number = 0;
$sms->device_slot_name = "SIM 1";
$sms->mobile_number = "61480597773"; // Your number
$sms->message = "🚀 Test SMS from LocalText at " . date('H:i:s') . " - This should appear on your phone!";
$sms->schedule = now()->format("Y-m-d H:i");
$sms->status = 0; // initial
$sms->et = 1; // event trigger enabled
$sms->save();

echo "✅ SMS created with ID: {$sms->id}\n";
echo "To: {$sms->mobile_number}\n";
echo "Message: {$sms->message}\n\n";

// Send via Pusher to device
echo "📡 Sending to device via Pusher...\n";

$messages[] = $sms;

event(new App\Events\MessageSend([
    'success' => true,
    'device_id' => $device->device_id,
    'original_data' => [
        'message' => $messages,
    ]
]));

echo "✅ Event sent to device!\n\n";

echo "🔥 CHECK YOUR DEVICE NOW! 🔥\n";
echo "- Look for a notification from your SMS gateway app\n";
echo "- The app should process and send this SMS\n";
echo "- You should receive the SMS on {$sms->mobile_number}\n\n";

echo "If you receive the SMS, the system is working perfectly!\n";
echo "If not, check:\n";
echo "1. Device app is running and connected\n";
echo "2. App has SMS sending permissions\n";
echo "3. SIM card is active\n";
echo "4. Device has cellular/WiFi connection\n";

echo "\n=== Message Sent ===\n";
?>
