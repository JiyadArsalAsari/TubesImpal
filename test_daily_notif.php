<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Deadline;
use App\Models\Mahasiswa;
use Carbon\Carbon;

$user = User::where('role', 'mahasiswa')->first();
if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "Testing with User: " . $user->username . "\n";
$mahasiswa = $user->mahasiswa;

if (!$mahasiswa) {
    // Create dummy mahasiswa if missing
    $mahasiswa = Mahasiswa::create([
        'user_id' => $user->id,
        'mahasiswaID' => '12345',
        'nama' => $user->username
    ]);
}

// Clear existing test data to avoid confusion
Schedule::where('subject_name', 'Test Schedule Today')->delete();
Deadline::where('subject_name', 'Test Deadline Today')->delete();

// Create Schedule for Today
$todayDay = Carbon::now('Asia/Jakarta')->format('l');
echo "Creating Schedule for: $todayDay\n";

Schedule::create([
    'mahasiswa_id' => $mahasiswa->id,
    'subject_name' => 'Test Schedule Today',
    'day' => $todayDay,
    'time' => '10:00',
    'room' => 'Room A'
]);

// Create Deadline for Today
$todayDate = Carbon::now('Asia/Jakarta')->toDateString();
echo "Creating Deadline for: $todayDate\n";

Deadline::create([
    'mahasiswa_id' => $mahasiswa->id,
    'subject_name' => 'Test Deadline Today',
    'date' => $todayDate,
    'day' => $todayDay,
    'time' => '23:59'
]);

echo "Data created. Running notification check...\n";
Artisan::call('notifications:check');
echo Artisan::output();

// Check if notifications exist
$notifs = $user->notifications()
    ->where('created_at', '>=', Carbon::now('Asia/Jakarta')->subMinutes(1))
    ->get();

echo "Notifications found: " . $notifs->count() . "\n";
foreach ($notifs as $n) {
    echo "- " . ($n->data['subject'] ?? 'No Subject') . " (" . ($n->data['type'] ?? 'No Type') . ")\n";
}
