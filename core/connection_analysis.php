<?php
// Database connection
$host = 'localhost';
$dbname = 'localtext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 DEVICE CONNECTION ANALYSIS 🔍\n";
    echo "=================================\n";
    echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    echo "📱 APP SAYS 'CONNECTED' BUT DATABASE SHOWS OFFLINE\n";
    echo "===================================================\n";
    echo "This usually means one of these issues:\n\n";
    
    echo "1. 🆔 DEVICE ID MISMATCH:\n";
    echo "   - Old device ID in database: 21da7925bb07102a\n";
    echo "   - New app might have different device ID\n";
    echo "   - Solution: Re-pair the device\n\n";
    
    echo "2. 🔐 USER/AUTH MISMATCH:\n";
    echo "   - App connected to wrong user account\n";
    echo "   - Database expects user_id = 2 (riidgyy)\n";
    echo "   - Solution: Logout and login again\n\n";
    
    echo "3. 🔄 DEVICE UPDATE FAILURE:\n";
    echo "   - App connects but doesn't update 'last_seen'\n";
    echo "   - Background sync might be broken\n";
    echo "   - Solution: Force refresh/restart app\n\n";
    
    // Check if there are any newer device entries
    echo "🔍 CHECKING FOR NEW DEVICE REGISTRATIONS:\n";
    echo "==========================================\n";
    $stmt = $pdo->query("SELECT * FROM devices ORDER BY created_at DESC LIMIT 5");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($devices as $device) {
        $age_hours = round((time() - strtotime($device['created_at'])) / 3600);
        echo "Device: {$device['device_name']} | ID: {$device['device_id']} | Created: {$device['created_at']} ({$age_hours}h ago)\n";
    }
    
    echo "\n🎯 CRITICAL ACTIONS TO TRY:\n";
    echo "============================\n";
    echo "1. 📱 Open SMS Gateway app on your phone\n";
    echo "2. 🔍 Look for 'Device ID' in app settings/info\n";
    echo "3. ✅ Verify it matches: 21da7925bb07102a\n";
    echo "4. 🔑 Logout completely from the app\n";
    echo "5. 🔑 Login again with: riidgyy\n";
    echo "6. 🔗 Go to dashboard and get fresh QR code\n";
    echo "7. 📱 Scan QR code to re-pair device\n";
    echo "8. ⏰ Wait 30 seconds and check if database updates\n\n";
    
    // Let's manually trigger a device status update
    echo "🔧 MANUAL DEVICE STATUS UPDATE:\n";
    echo "================================\n";
    $stmt = $pdo->prepare("UPDATE devices SET updated_at = NOW() WHERE id = 1");
    $result = $stmt->execute();
    
    if($result) {
        echo "✅ Manually updated device timestamp\n";
        echo "📝 This simulates what should happen when app connects\n\n";
    }
    
    // Check current SMS queue
    echo "📊 CURRENT SMS QUEUE STATUS:\n";
    echo "============================\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sms WHERE status = 0");
    $pending_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "⏳ Messages waiting (Status 0): {$pending_count}\n";
    echo "💡 These will only process when device truly connects\n\n";
    
    echo "🚨 IF PROBLEM PERSISTS:\n";
    echo "========================\n";
    echo "1. 🗑️  Completely uninstall SMS Gateway app\n";
    echo "2. 🔄 Restart your phone\n";
    echo "3. ⬇️  Re-download app-ssl-fixed.apk\n";
    echo "4. 📲 Fresh install and setup\n";
    echo "5. 🔗 Re-pair from scratch\n\n";
    
    echo "📋 NEXT TEST:\n";
    echo "=============\n";
    echo "After re-pairing, run this command:\n";
    echo "php live_monitoring.php\n";
    echo "Look for updated 'Last Seen' timestamp!\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
