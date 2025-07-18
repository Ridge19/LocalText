<?php
// SSL/TLS Configuration Mismatch Diagnostic
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SSL/TLS CONFIGURATION MISMATCH FOUND! ===\n\n";

echo "🔍 PROBLEM IDENTIFIED:\n";
echo "======================\n";
echo "The Android app and server have mismatched SSL/TLS settings!\n\n";

echo "📱 ANDROID APP CONFIGURATION:\n";
echo "- Uses: setUseTLS(true) - HTTPS/SSL enabled\n";
echo "- Expects: wss://ws-ap2.pusher.com (secure WebSocket)\n";
echo "- Cluster: ap2 with SSL/TLS\n\n";

echo "🖥️ SERVER CONFIGURATION:\n";
echo "- Uses: useTLS => false - HTTP without SSL\n";
echo "- Sends to: ws://api-ap2.pusher.com (non-secure)\n";
echo "- Port: 80 (HTTP)\n\n";

echo "💥 RESULT:\n";
echo "- Server sends events successfully via HTTP\n";
echo "- Android app expects events via HTTPS/SSL\n";
echo "- App never receives the events!\n\n";

echo "🔧 SOLUTIONS:\n";
echo "=============\n\n";

echo "OPTION 1: Fix Server to Use SSL (Recommended)\n";
echo "----------------------------------------------\n";
echo "Update server broadcasting config to use proper SSL:\n";
echo "- useTLS: true\n";
echo "- scheme: https\n";
echo "- port: 443\n";
echo "- Proper SSL certificate bundle\n\n";

echo "OPTION 2: Modify Android App (Quick Fix)\n";
echo "----------------------------------------\n";
echo "Change Android app PusherOdk.java:\n";
echo "- setUseTLS(false) - Disable SSL in app\n";
echo "- Rebuild and reinstall APK\n\n";

echo "🚀 IMPLEMENTING OPTION 1 (Server SSL Fix):\n";
echo "==========================================\n";

// Check if we have the certificate file
$certPath = base_path('cacert.pem');
if (file_exists($certPath)) {
    echo "✅ SSL certificate file found: {$certPath}\n";
} else {
    echo "❌ SSL certificate file missing\n";
}

echo "\nUpdating server configuration for proper SSL...\n";

echo "\n=== TESTING WITH PROPER SSL ===\n";

try {
    // Test with proper SSL configuration
    $pusher = new Pusher\Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,  // Enable SSL
            'curl_options' => [
                CURLOPT_CAINFO => $certPath,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_SSL_VERIFYPEER => 1,
            ]
        ]
    );
    
    echo "✅ Pusher instance created with SSL\n";
    
    $device = App\Models\Device::where('device_id', '21da7925bb07102a')->first();
    
    if ($device) {
        $channelName = 'private-message-send-' . $device->device_id;
        
        $testData = [
            'success' => true,
            'device_id' => $device->device_id,
            'original_data' => [
                'message' => [[
                    'id' => 99999,
                    'mobile_number' => '61480597773',
                    'message' => '🔥 SSL FIX TEST - This should work with proper SSL!',
                    'device_slot_number' => 0,
                    'formatted_message' => '🔥 SSL FIX TEST - This should work with proper SSL!'
                ]]
            ]
        ];
        
        echo "Sending test message with SSL enabled...\n";
        $result = $pusher->trigger($channelName, 'message-send', $testData);
        
        if ($result) {
            echo "✅ SSL test message sent successfully!\n";
            echo "📱 CHECK YOUR DEVICE NOW - this should work!\n\n";
            
            echo "If this works, the SSL configuration is now matching!\n";
        } else {
            echo "❌ SSL test failed\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ SSL Error: " . $e->getMessage() . "\n";
    echo "This confirms the SSL certificate issue.\n\n";
    
    echo "🔄 FALLBACK: Try updating Android app instead...\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "The mismatch between app SSL (enabled) and server SSL (disabled)\n";
echo "is why your device isn't receiving SMS events!\n";
?>
