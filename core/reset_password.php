<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking user password hash...\n";

$user = \App\Models\User::where('username', 'riidgyy')->first();
if ($user) {
    echo "User: " . $user->username . "\n";
    echo "Password hash: " . substr($user->password, 0, 20) . "...\n";
    echo "Hash length: " . strlen($user->password) . "\n";
    
    // Let's set a known password for testing
    $newPassword = '123456';
    $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
    $user->save();
    
    echo "✅ Password reset to: " . $newPassword . "\n";
} else {
    echo "User not found\n";
}
?>
