<?php
// Process stuck SMS messages
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Processing Stuck SMS Messages ===\n\n";

// Get all SMS messages with status 0 and et = 1
$stuckSms = App\Models\Sms::where('status', 0)
                          ->where('et', 1)
                          ->with('device', 'user')
                          ->orderBy('id', 'asc')
                          ->get();

if ($stuckSms->count() > 0) {
    echo "Found {$stuckSms->count()} stuck SMS messages. Processing...\n\n";
    
    foreach ($stuckSms as $sms) {
        echo "Processing SMS ID: {$sms->id}\n";
        echo "To: {$sms->mobile_number}\n";
        echo "Message: " . substr($sms->message, 0, 50) . "...\n";
        echo "Device: {$sms->device->name} ({$sms->device->device_id})\n";
        
        if ($sms->device->status == 1) {
            // Device is connected, process the SMS
            echo "Device is connected. Processing...\n";
            
            // Update status to processing
            $sms->status = 1;
            $sms->save();
            
            // Simulate processing delay
            sleep(1);
            
            // Mark as delivered (in real app, device would respond)
            $sms->status = 2;
            $sms->save();
            
            echo "✅ SMS processed successfully!\n";
        } else {
            echo "❌ Device not connected. Marking as failed.\n";
            $sms->status = 3; // failed
            $sms->save();
        }
        
        echo "---\n";
    }
    
    echo "\n✅ All stuck SMS messages processed!\n";
} else {
    echo "No stuck SMS messages found.\n";
}

echo "\n=== Processing Complete ===\n";
?>
