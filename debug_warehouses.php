<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SubWarehouse;
foreach (SubWarehouse::all() as $sw) {
    echo "ID: {$sw->id}, Name: {$sw->name}\n";
}
