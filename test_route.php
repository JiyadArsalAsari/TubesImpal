<?php
// Simple test to check if the route exists
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

// Get the URL generator
$url = app('url');

try {
    // Try to generate the learning development route
    $route = $url->route('mahasiswa.learning.development');
    echo "Route URL: " . $route . "\n";
} catch (Exception $e) {
    echo "Error generating route: " . $e->getMessage() . "\n";
}