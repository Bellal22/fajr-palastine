<?php

use App\Models\Person;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cities = Person::select('current_city')->distinct()->pluck('current_city');
echo "Distinct current_city in persons:\n";
print_r($cities->toArray());
