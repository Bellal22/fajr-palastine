<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\SubWarehouse;
$warehouses = SubWarehouse::all(['id', 'name'])->toArray();
file_put_contents('warehouses_dump.json', json_encode($warehouses, JSON_PRETTY_PRINT));
echo "Dumped " . count($warehouses) . " warehouses.\n";
