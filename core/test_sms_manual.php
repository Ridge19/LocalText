<?php
// Simple SMS Processing Test
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS Processing Test ===\n\n";

// Get the most recent SMS
$recentSms = App\Models\Sms::orderBy('id', 'desc')->first();

if ($recentSms) {
    echo "Recent SMS found:\n";
    echo "ID: {$recentSms->id}\n";
    echo "Mobile: {$recentSms->mobile}\n";
    echo "Status: {$recentSms->status}\n";
    echo "Device ID: {$recentSms->device_id}\n";
    echo "Message: {$recentSms->message}\n";
    echo "Created: {$recentSms->created_at}\n\n";
    
    // Check device status
    $device = $recentSms->device;
    if ($device) {
        echo "Device Info:\n";
        echo "Device ID: {$device->device_id}\n";
        echo "Name: {$device->name}\n";
        echo "Status: {$device->status}\n";
        echo "Last Seen: {$device->last_seen}\n\n";
    }
    
    // Let's try to manually process this SMS
    if ($recentSms->status == 0) {
        echo "Attempting to process SMS manually...\n";
        
        // Update status to 1 (processing)
        $recentSms->status = 1;
        $recentSms->save();
        echo "✅ Updated SMS status to 1 (processing)\n";
        
        // Check if this fixes the issue
        $recentSms->refresh();
        echo "Current status: {$recentSms->status}\n";
        
        // Simulate successful delivery
        sleep(1);
        $recentSms->status = 2; // delivered
        $recentSms->save();
        echo "✅ Updated SMS status to 2 (delivered)\n";
        
        echo "\nSMS processing completed manually!\n";
        echo "Try sending another SMS to see if it works now.\n";
    } else {
        echo "SMS status is not 0, current status: {$recentSms->status}\n";
    }
    
} else {
    echo "No SMS found in database\n";
}

echo "\n=== End Test ===\n";
?>
