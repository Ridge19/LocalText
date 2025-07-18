<?php
// Auto SMS Processor for Development
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Auto SMS Processor Started ===\n";
echo "This will automatically process new SMS messages.\n";
echo "Press Ctrl+C to stop.\n\n";

$processedCount = 0;
$lastCheck = time();

while (true) {
    // Check for new SMS every 2 seconds
    if (time() - $lastCheck >= 2) {
        $pendingSms = App\Models\Sms::where('status', 0)
                                    ->where('et', 1)
                                    ->with('device')
                                    ->orderBy('id', 'asc')
                                    ->limit(5)
                                    ->get();
        
        if ($pendingSms->count() > 0) {
            echo "[" . date('H:i:s') . "] Processing " . $pendingSms->count() . " SMS...\n";
            
            foreach ($pendingSms as $sms) {
                if ($sms->device && $sms->device->status == 1) {
                    $sms->status = 4; // SMS_PROCESSING
                    $sms->save();
                    
                    // Simulate processing time (0.5-2 seconds)
                    usleep(rand(500000, 2000000));
                    
                    $sms->status = 1; // SMS_DELIVERED
                    $sms->save();
                    
                    echo "  ✅ SMS {$sms->id} to {$sms->mobile_number} delivered\n";
                    $processedCount++;
                } else {
                    $sms->status = 9; // SMS_FAILED - device not connected
                    $sms->save();
                    echo "  ❌ SMS {$sms->id} failed - device not connected\n";
                }
            }
        }
        
        $lastCheck = time();
    }
    
    // Small delay to prevent high CPU usage
    usleep(100000); // 0.1 seconds
}
?>
