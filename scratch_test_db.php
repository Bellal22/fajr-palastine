<?php

use App\Models\IncompletePerson;
use App\Models\Person;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Database Connection...\n";

try {
    $count = IncompletePerson::count();
    echo "Total Incomplete People: $count\n";

    if ($count > 0) {
        $first = IncompletePerson::first();
        echo "Example ID: " . $first->id_num . " Name: " . $first->first_name . " " . $first->family_name . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
