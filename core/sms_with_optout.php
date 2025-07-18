<?php
// SMS Templates with Opt-out Options
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS with Opt-out Template ===\n\n";

// Function to add opt-out to any message
function addOptOut($message) {
    // Check if message already has opt-out text
    if (stripos($message, 'stop') !== false || stripos($message, 'opt out') !== false) {
        return $message;
    }
    
    // Add opt-out based on message length
    $maxLength = 160; // Standard SMS length
    $optOutText = " Reply STOP to opt out.";
    
    if (strlen($message . $optOutText) <= $maxLength) {
        return $message . $optOutText;
    } else {
        // If too long, truncate message and add opt-out
        $availableLength = $maxLength - strlen($optOutText);
        return substr($message, 0, $availableLength) . $optOutText;
    }
}

// Test with different message types
$testMessages = [
    "Hey! Your Business Local listing is almost live 🚀 Check your email for next steps...",
    "Test SMS from LocalText",
    "Hi, promote your business locally with Business Local. Get started today!",
    "🚨 URGENT: Device connection test. If you receive this SMS, reply WORKING to confirm."
];

echo "📝 Original vs Opt-out Messages:\n";
echo "================================\n";

foreach ($testMessages as $message) {
    $withOptOut = addOptOut($message);
    echo "Original: {$message}\n";
    echo "With Opt-out: {$withOptOut}\n";
    echo "Length: " . strlen($withOptOut) . " characters\n";
    echo "---\n";
}

// Send a properly formatted SMS with opt-out
$device = App\Models\Device::where('status', 1)->first();

if ($device) {
    echo "\n📱 Sending SMS with Opt-out:\n";
    echo "============================\n";
    
    $baseMessage = "Business Local: Your listing is ready! Visit businesslocal.com.au to view it.";
    $finalMessage = addOptOut($baseMessage);
    
    // Create SMS record
    $sms = new App\Models\Sms();
    $sms->device_id = $device->id;
    $sms->user_id = $device->user_id;
    $sms->device_slot_number = 0;
    $sms->device_slot_name = "SIM 1";
    $sms->mobile_number = "61480597773";
    $sms->message = $finalMessage;
    $sms->schedule = now()->format("Y-m-d H:i");
    $sms->status = 0;
    $sms->et = 1;
    $sms->save();
    
    echo "✅ SMS created with ID: {$sms->id}\n";
    echo "Message: {$finalMessage}\n";
    echo "Length: " . strlen($finalMessage) . " characters\n\n";
    
    // Send via Pusher
    echo "📡 Sending to device...\n";
    
    $messages[] = $sms;
    
    event(new App\Events\MessageSend([
        'success' => true,
        'device_id' => $device->device_id,
        'original_data' => [
            'message' => $messages,
        ]
    ]));
    
    echo "✅ SMS with opt-out sent to device!\n";
    echo "📱 Check your phone: {$sms->mobile_number}\n";
}

echo "\n=== Template Complete ===\n";
echo "\n💡 SMS Best Practices:\n";
echo "- Always include opt-out option\n";
echo "- Keep messages under 160 characters\n";
echo "- Use clear, professional language\n";
echo "- Include business name/contact info\n";
echo "- Respect opt-out requests immediately\n";
?>
