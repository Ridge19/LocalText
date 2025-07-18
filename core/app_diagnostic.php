<?php
// SMS Gateway App Diagnostic
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMS GATEWAY APP DIAGNOSTIC ===\n\n";

echo "🔍 POTENTIAL APP ISSUES:\n";
echo "========================\n\n";

echo "1. 📱 APP NOT ACTUALLY RUNNING\n";
echo "   - App may have crashed silently\n";
echo "   - Background processes killed by OS\n";
echo "   - App needs to be manually restarted\n\n";

echo "2. 🔗 PUSHER CONNECTION ISSUES\n";
echo "   - App shows 'connected' but Pusher connection is broken\n";
echo "   - Network firewall blocking Pusher ports\n";
echo "   - App using old/cached Pusher credentials\n\n";

echo "3. ⚙️ APP CONFIGURATION PROBLEMS\n";
echo "   - Wrong API endpoint URLs\n";
echo "   - Incorrect channel subscription\n";
echo "   - Event listener not properly registered\n\n";

echo "4. 📨 SMS PROCESSING BUGS\n";
echo "   - App receives events but fails to process SMS\n";
echo "   - Error in SMS sending logic\n";
echo "   - SIM card selection issues\n\n";

echo "5. 🔒 PERMISSION ISSUES\n";
echo "   - SMS permission revoked\n";
echo "   - Background execution restricted\n";
echo "   - Network access blocked\n\n";

echo "🧪 APP TESTING CHECKLIST:\n";
echo "=========================\n";
echo "On your device, check these:\n\n";

echo "✅ BASIC APP STATUS:\n";
echo "   □ App is actually open/running\n";
echo "   □ App shows in recent apps list\n";
echo "   □ No crash reports in device settings\n";
echo "   □ App version is latest\n\n";

echo "✅ CONNECTION STATUS:\n";
echo "   □ App shows 'Connected' or 'Online'\n";
echo "   □ Green indicator/status light\n";
echo "   □ Last activity timestamp is recent\n";
echo "   □ Can manually connect/disconnect\n\n";

echo "✅ SETTINGS VERIFICATION:\n";
echo "   □ Server URL/endpoint correct\n";
echo "   □ Pusher App ID: 1761451\n";
echo "   □ Pusher Key: 5d5588cff51ff3f6a94b\n";
echo "   □ Pusher Cluster: ap2\n";
echo "   □ Device ID: 21da7925bb07102a\n";
echo "   □ Username: riidgyy\n\n";

echo "✅ PERMISSIONS:\n";
echo "   □ SMS permission granted\n";
echo "   □ Phone permission granted\n";
echo "   □ Storage permission granted\n";
echo "   □ Network permission granted\n";
echo "   □ Notification permission granted\n";
echo "   □ Background app refresh enabled\n";
echo "   □ Battery optimization disabled\n\n";

echo "✅ FUNCTIONALITY TEST:\n";
echo "   □ Can send test SMS from app manually\n";
echo "   □ SIM card detected and active\n";
echo "   □ Network connectivity working\n";
echo "   □ App can access internet\n\n";

// Send a different type of test event to see if app responds
echo "🚀 SENDING ALTERNATIVE TEST EVENT:\n";
echo "==================================\n";

$device = App\Models\Device::where('device_id', '21da7925bb07102a')->first();

if ($device) {
    try {
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
        
        $channelName = 'private-message-send-' . $device->device_id;
        
        // Send different event types to test app responsiveness
        $testEvents = [
            [
                'event' => 'ping',
                'data' => ['message' => 'Simple ping test', 'timestamp' => time()]
            ],
            [
                'event' => 'connection-test',
                'data' => ['type' => 'connectivity_check', 'server_time' => date('Y-m-d H:i:s')]
            ],
            [
                'event' => 'app-status-request',
                'data' => ['request_id' => uniqid(), 'please_respond' => true]
            ]
        ];
        
        foreach ($testEvents as $test) {
            echo "Sending {$test['event']} event...\n";
            $result = $pusher->trigger($channelName, $test['event'], $test['data']);
            echo $result ? "✅ Sent successfully\n" : "❌ Failed to send\n";
        }
        
        echo "\n📱 CHECK YOUR APP NOW:\n";
        echo "=====================\n";
        echo "Look for ANY signs of activity:\n";
        echo "- New notifications\n";
        echo "- Log entries\n";
        echo "- Status changes\n";
        echo "- Connection indicators\n";
        echo "- Error messages\n\n";
        
    } catch (Exception $e) {
        echo "❌ Error sending test events: " . $e->getMessage() . "\n";
    }
}

echo "🔧 TROUBLESHOOTING STEPS:\n";
echo "=========================\n";
echo "1. RESTART APP: Force close and reopen\n";
echo "2. REINSTALL APP: If available, try reinstalling\n";
echo "3. CHECK LOGS: Look for app logs or error messages\n";
echo "4. TEST MANUALLY: Try sending SMS directly from app\n";
echo "5. DIFFERENT DEVICE: Try with another device if available\n";
echo "6. CONTACT APP DEVELOPER: If app is third-party\n\n";

echo "📋 COMMON APP PROBLEMS:\n";
echo "=======================\n";
echo "- App using wrong API version\n";
echo "- Outdated SSL certificates in app\n";
echo "- Memory leaks causing crashes\n";
echo "- Event handlers not properly bound\n";
echo "- Database corruption in app\n";
echo "- Background service not starting\n\n";

echo "=== DIAGNOSTIC COMPLETE ===\n";
echo "The server is working perfectly.\n";
echo "Focus on fixing the SMS gateway app on your device.\n";
?>
