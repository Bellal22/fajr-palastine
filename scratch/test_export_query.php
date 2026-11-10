<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\FamilyChildrenExport;
use Illuminate\Http\Request;

$testId = '804693224';

echo "\n--- Testing FamilyChildrenExport Logic directly ---\n";
echo "Testing with ID: $testId\n";

// Simulate a request with the search ID
$request = new Request(['search' => $testId]);

// Create the export instance
$export = new FamilyChildrenExport($request);

// Get the query
$query = $export->query();

echo "\nGenerated SQL Trace (approx):\n";
// echo $query->toSql() . "\n";

// Execute the query
$results = $query->get();

echo "\nFound total records: " . $results->count() . "\n";

if ($results->isEmpty()) {
    echo "\n[!] FAILED: Query returned 0 results.\n";
} else {
    echo "\n[v] SUCCESS! Query returned " . $results->count() . " records.\n";
    
    // Test Mapping for the first record
    echo "\nTesting Mapping for the first record:\n";
    try {
        $firstRow = $results->first();
        $mapped = $export->map($firstRow);
        
        echo "Mapped columns count: " . count($mapped) . "\n";
        echo "Example data (Child Name): " . ($mapped[28] ?? 'MISSING') . "\n"; // Index 28 is child name in headings
        
        if (count($mapped) !== count($export->headings())) {
            echo "[!] WARNING: Mapped columns count (" . count($mapped) . ") does NOT match headings count (" . count($export->headings()) . ")\n";
        } else {
            echo "[v] Mapping seems correct and consistent with headings.\n";
        }
    } catch (\Exception $e) {
        echo "[!] MAP ERROR: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
    
    echo "\nDetails:\n";
    foreach ($results as $row) {
        echo "    - Child: {$row->child_full_name} ({$row->child_id_num})\n";
        echo "      DOB: " . ($row->child_dob ?? 'NULL') . "\n";
        echo "      Age (Years): " . ($row->child_age_years ?? 'N/A') . "\n";
        echo "      Age (Months): " . ($row->child_age_months ?? 'N/A') . "\n";
    }
}
