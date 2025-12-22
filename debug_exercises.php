<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all exercises
$exercises = App\Models\Exercise::all();

echo "Total exercises: " . $exercises->count() . "\n";

foreach($exercises as $exercise) {
    echo "ID: " . $exercise->id . ", Type: " . $exercise->type . ", Title: " . $exercise->title . "\n";
}

// Count by type
$quizCount = App\Models\Exercise::where('type', 'quiz')->count();
$assignmentCount = App\Models\Exercise::where('type', 'assignment')->count();

echo "Total quizzes: " . $quizCount . "\n";
echo "Total assignments: " . $assignmentCount . "\n";