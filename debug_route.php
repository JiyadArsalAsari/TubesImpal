<?php
// Simple debug script to test the learning development route
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

// Test if the route exists
$route = Route::getRoutes()->getByName('mahasiswa.learning.development');

if ($route) {
    echo "Route found:\n";
    echo "URI: " . $route->uri() . "\n";
    echo "Methods: " . implode(', ', $route->methods()) . "\n";
    
    // Get the controller method
    $action = $route->getAction();
    if (isset($action['controller'])) {
        echo "Controller: " . $action['controller'] . "\n";
    }
} else {
    echo "Route not found!\n";
}