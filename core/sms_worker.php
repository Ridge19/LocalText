<?php
// SMS Processing Worker for Development (bypasses Pusher SSL issues)
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS Processing Worker Started ===\n";
echo "This script will continuously process pending SMS messages.\n";
echo "Press Ctrl+C to stop.\n\n";

$processedCount = 0;

while (true) {
    // Get pending SMS messages (status = 0 and et = 1)
    $pendingSms = App\Models\Sms::where('status', 0)
                                ->where('et', 1) // event trigger enabled
                                ->with('device')
                                ->orderBy('id', 'asc')
                                ->limit(10)
                                ->get();
    
    if ($pendingSms->count() > 0) {
        echo "Found " . $pendingSms->count() . " pending SMS messages...\n";
        
        foreach ($pendingSms as $sms) {
            echo "Processing SMS ID: {$sms->id} to {$sms->mobile_number}...\n";
            
            // Check if device is connected
            if ($sms->device && $sms->device->status == 1) {
                // Update status to processing (1)
                $sms->status = 1;
                $sms->save();
                
                // Simulate processing time
                sleep(1);
                
                // Update status to delivered (2) - in real app this would be updated by device response
                $sms->status = 2;
                $sms->delivered_at = now();
                $sms->save();
                
                echo "✅ SMS ID {$sms->id} marked as delivered\n";
                $processedCount++;
            } else {
                echo "❌ Device not connected for SMS ID {$sms->id}\n";
                $sms->status = 3; // failed
                $sms->save();
            }
        }
        
        echo "Processed batch. Total processed: {$processedCount}\n\n";
    } else {
        // No pending messages, wait before checking again
        sleep(2);
        echo "No pending messages. Waiting...\n";
    }
    
    // Small delay to prevent high CPU usage
    usleep(500000); // 0.5 seconds
}
?>
