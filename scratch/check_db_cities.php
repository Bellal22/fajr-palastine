<?php

use App\Models\City;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cities = City::all();
echo "All cities in database:\n";
print_r($cities->toArray());
