<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Force MySQL connection settings
config(['database.default' => 'mysql']);
config(['database.connections.mysql.driver' => 'mysql']);

// Show all database configurations
echo "Current database configuration:\n";
print_r(config('database'));

echo "\n\nTrying to connect...\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "Connected successfully!";
} catch (\Exception $e) {
    echo "Connection error: " . $e->getMessage();
}
