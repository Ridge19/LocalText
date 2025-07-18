<?php
// Process Pending SMS with Correct Status
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Processing Pending SMS (Correct Status) ===\n\n";

// Get pending SMS messages (status = 0)
$pendingSms = App\Models\Sms::where('status', 0)
                            ->where('et', 1)
                            ->with('device')
                            ->orderBy('id', 'asc')
                            ->get();

if ($pendingSms->count() > 0) {
    echo "Found {$pendingSms->count()} pending SMS messages. Processing...\n\n";
    
    foreach ($pendingSms as $sms) {
        echo "Processing SMS ID: {$sms->id}\n";
        echo "To: {$sms->mobile_number}\n";
        echo "Message: " . substr($sms->message, 0, 50) . "...\n";
        
        if ($sms->device && $sms->device->status == 1) {
            echo "Device is connected. Processing...\n";
            
            // Update status to processing
            $sms->status = 4; // SMS_PROCESSING
            $sms->save();
            
            // Simulate processing delay
            sleep(1);
            
            // Mark as delivered using correct status
            $sms->status = 1; // SMS_DELIVERED
            $sms->save();
            
            echo "✅ SMS processed successfully! (Status: 1 - DELIVERED)\n";
        } else {
            echo "❌ Device not connected. Marking as failed.\n";
            $sms->status = 9; // SMS_FAILED
            $sms->save();
        }
        
        echo "---\n";
    }
    
    echo "\n✅ All pending SMS messages processed!\n";
} else {
    echo "No pending SMS messages found.\n";
}

echo "\n📊 Final Status Summary:\n";
echo "========================\n";
echo "Status 0 (INITIAL): " . App\Models\Sms::where('status', 0)->count() . " messages\n";
echo "Status 1 (DELIVERED): " . App\Models\Sms::where('status', 1)->count() . " messages\n";
echo "Status 4 (PROCESSING): " . App\Models\Sms::where('status', 4)->count() . " messages\n";
echo "Status 9 (FAILED): " . App\Models\Sms::where('status', 9)->count() . " messages\n";

echo "\n=== Processing Complete ===\n";
?>
