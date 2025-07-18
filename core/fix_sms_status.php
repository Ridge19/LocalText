<?php
// Fix SMS Status Values
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing SMS Status Values ===\n\n";

echo "📊 Current Status Distribution:\n";
echo "Status 0 (INITIAL): " . App\Models\Sms::where('status', 0)->count() . " messages\n";
echo "Status 1 (DELIVERED): " . App\Models\Sms::where('status', 1)->count() . " messages\n";
echo "Status 2 (PENDING): " . App\Models\Sms::where('status', 2)->count() . " messages\n";
echo "Status 9 (FAILED): " . App\Models\Sms::where('status', 9)->count() . " messages\n\n";

// Fix 1: Convert all status 2 (PENDING) to status 1 (DELIVERED)
echo "🔧 Converting status 2 (PENDING) to status 1 (DELIVERED)...\n";
$updated = App\Models\Sms::where('status', 2)->update(['status' => 1]);
echo "✅ Updated {$updated} messages from status 2 to status 1\n\n";

// Fix 2: For recent pending messages (status 0), we'll leave them as is 
// since they're truly pending and should be processed
echo "📋 Recent pending messages (status 0) - these should be processed:\n";
$pendingSms = App\Models\Sms::where('status', 0)->orderBy('id', 'desc')->limit(5)->get();

foreach ($pendingSms as $sms) {
    echo "ID: {$sms->id} | Mobile: {$sms->mobile_number} | Message: " . substr($sms->message, 0, 30) . "...\n";
}

echo "\n📊 Updated Status Distribution:\n";
echo "Status 0 (INITIAL): " . App\Models\Sms::where('status', 0)->count() . " messages\n";
echo "Status 1 (DELIVERED): " . App\Models\Sms::where('status', 1)->count() . " messages\n";
echo "Status 2 (PENDING): " . App\Models\Sms::where('status', 2)->count() . " messages\n";
echo "Status 9 (FAILED): " . App\Models\Sms::where('status', 9)->count() . " messages\n\n";

echo "✅ Status fix complete!\n";
echo "Now all previously 'delivered' messages show status 1 correctly.\n\n";

echo "=== Fix Complete ===\n";
?>
