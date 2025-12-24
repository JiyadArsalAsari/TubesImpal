<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the URL generator
$url = app('url');

try {
    // Try to generate the learning development route
    $route = $url->route('mahasiswa.learning.development');
    echo "Learning Development Route URL: " . $route . "\n";
    
    // Also test the dashboard route
    $dashboardRoute = $url->route('mahasiswa.dashboard');
    echo "Dashboard Route URL: " . $dashboardRoute . "\n";
} catch (Exception $e) {
    echo "Error generating route: " . $e->getMessage() . "\n";
}