<?php
// SMS Diagnostics Script
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS System Diagnostics ===\n\n";

// Check recent SMS
echo "1. Recent SMS Messages:\n";
echo "------------------------\n";
try {
    $recentSms = App\Models\Sms::orderBy('id', 'desc')->limit(5)->get();
    if ($recentSms->count() > 0) {
        foreach ($recentSms as $sms) {
            echo "ID: {$sms->id} | Mobile: {$sms->mobile_number} | Status: {$sms->status} | ET: {$sms->et} | Created: {$sms->created_at}\n";
        }
    } else {
        echo "No SMS messages found.\n";
    }
} catch (Exception $e) {
    echo "Error checking SMS: " . $e->getMessage() . "\n";
}

echo "\n2. Device Status:\n";
echo "------------------\n";
try {
    $devices = App\Models\Device::all();
    if ($devices->count() > 0) {
        foreach ($devices as $device) {
            $status = $device->status == 1 ? 'Connected' : 'Disconnected';
            echo "Device: {$device->device_name} ({$device->device_model}) | Status: {$status} | User: {$device->user_id}\n";
        }
    } else {
        echo "No devices found.\n";
    }
} catch (Exception $e) {
    echo "Error checking devices: " . $e->getMessage() . "\n";
}

echo "\n3. Broadcasting Configuration:\n";
echo "------------------------------\n";
echo "Broadcast Driver: " . config('broadcasting.default') . "\n";
echo "Pusher App ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
echo "Pusher Key: " . substr(config('broadcasting.connections.pusher.key'), 0, 10) . "...\n";
echo "Pusher Cluster: " . config('broadcasting.connections.pusher.options.cluster') . "\n";

echo "\n4. User Limits:\n";
echo "----------------\n";
try {
    $user = App\Models\User::find(1); // Assuming first user
    if ($user) {
        echo "Available SMS: " . ($user->available_sms == -1 ? 'Unlimited' : $user->available_sms) . "\n";
        echo "Daily SMS Limit: " . ($user->daily_sms_limit == -1 ? 'Unlimited' : $user->daily_sms_limit) . "\n";
        echo "Available Devices: " . ($user->available_device_limit == -1 ? 'Unlimited' : $user->available_device_limit) . "\n";
        
        $todaysSms = App\Models\Sms::where('user_id', $user->id)->whereDate('created_at', now())->count();
        echo "SMS sent today: {$todaysSms}\n";
    }
} catch (Exception $e) {
    echo "Error checking user limits: " . $e->getMessage() . "\n";
}

echo "\n=== End Diagnostics ===\n";
?>
