<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$warehouses = \App\Models\SubWarehouse::all(['id', 'name'])->toArray();
file_put_contents('warehouses.json', json_encode($warehouses));
echo "DONE";
