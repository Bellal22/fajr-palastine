<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Simulate a request
$request = Request::create('/api/v2/set_aid_manage_receive', 'POST', [
    'captured_qty' => 123,
    'external_notes' => 'Test Notes',
    'data' => json_encode([['id' => 1, 'title' => 'Sample']])
]);

// Find a real ID to test if possible, or just look at the code logic
$capturedQty = (int) $request->input('captured_qty', 0);
$externalNotes = $request->input('external_notes');

echo "Captured Qty: " . $capturedQty . PHP_EOL;
echo "External Notes: " . $externalNotes . PHP_EOL;

// Check the table columns again
try {
    $columns = DB::select('SHOW COLUMNS FROM project_beneficiaries');
    foreach ($columns as $column) {
        if ($column->Field == 'captured_qty' || $column->Field == 'external_notes') {
            echo "Column: " . $column->Field . " | Type: " . $column->Type . PHP_EOL;
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
