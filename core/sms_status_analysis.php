<?php
// SMS Status Analysis and Fix
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS Status Analysis ===\n\n";

// Check current status distribution
echo "📊 Current SMS Status Distribution:\n";
echo "===================================\n";
$statusCounts = App\Models\Sms::selectRaw('status, COUNT(*) as count')
                              ->groupBy('status')
                              ->orderBy('status')
                              ->get();

foreach ($statusCounts as $status) {
    echo "Status {$status->status}: {$status->count} messages\n";
}

echo "\n📋 Recent SMS with Status Details:\n";
echo "==================================\n";
$recentSms = App\Models\Sms::orderBy('id', 'desc')->limit(10)->get();

foreach ($recentSms as $sms) {
    $statusText = [
        0 => 'Pending/Initial',
        1 => 'Processing/Sending', 
        2 => 'Delivered/Success',
        3 => 'Failed/Error'
    ];
    
    echo "ID: {$sms->id} | Status: {$sms->status} ({$statusText[$sms->status]}) | Mobile: {$sms->mobile_number}\n";
}

// Check status constants in the application
echo "\n🔍 Checking Status Constants:\n";
echo "=============================\n";
try {
    if (class_exists('App\Constants\Status')) {
        $reflection = new ReflectionClass('App\Constants\Status');
        $constants = $reflection->getConstants();
        
        echo "Status constants found:\n";
        foreach ($constants as $name => $value) {
            if (strpos($name, 'SMS') !== false) {
                echo "{$name} = {$value}\n";
            }
        }
    } else {
        echo "Status constants class not found\n";
    }
} catch (Exception $e) {
    echo "Error checking constants: " . $e->getMessage() . "\n";
}

echo "\n💡 PROPOSED STATUS MAPPING:\n";
echo "===========================\n";
echo "0 = Pending (SMS created, waiting to be sent)\n";
echo "1 = Delivered (SMS successfully sent and delivered)\n";
echo "2 = Failed (SMS failed to send or deliver)\n";
echo "3 = Scheduled (SMS scheduled for future sending)\n";

echo "\n🔧 Do you want me to:\n";
echo "=====================\n";
echo "1. Update all status 2 messages to status 1 (mark as delivered)\n";
echo "2. Update all status 0 messages to status 1 (mark as delivered)\n";
echo "3. Create a script to fix the status mapping\n";
echo "4. Check what the application code expects for delivered status\n";

echo "\n=== Analysis Complete ===\n";
?>
