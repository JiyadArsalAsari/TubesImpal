<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Create a test user
$user = new User();
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->password = Hash::make('password');
$user->role = 'mahasiswa';
$user->username = 'testuser';
$user->save();

echo "Test user created successfully!\n";
echo "Email: test@example.com\n";
echo "Password: password\n";