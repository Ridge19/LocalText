<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking user 'riidgyy'...\n";

$user = \App\Models\User::where('username', 'riidgyy')->first();
if ($user) {
    echo "User found:\n";
    echo "Username: " . $user->username . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Status: " . $user->status . "\n";
    echo "ID: " . $user->id . "\n";
} else {
    echo "User not found\n";
    echo "Let's check all users:\n";
    $users = \App\Models\User::all();
    foreach ($users as $user) {
        echo "- " . $user->username . " (" . $user->email . ")\n";
    }
}
?>
