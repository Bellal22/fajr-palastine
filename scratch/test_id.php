<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Person;
use Illuminate\Support\Facades\DB;

$testId = '804693224';

echo "\n--- Testing with ID: $testId ---\n";

// 1. Search for this ID in persons
$person = Person::where('id_num', $testId)->first();

if (!$person) {
    echo "\n[!] ERROR: Person with id_num $testId was NOT found in the database.\n";
    
    // Check if it exists with padding
    $padded = str_pad($testId, 9, '0', STR_PAD_LEFT);
    $person = Person::where('id_num', $padded)->first();
    if ($person) {
        echo "[i] Found person with padded ID: $padded (ID: {$person->id})\n";
    } else {
        echo "[!] Even with padding ($padded), person was NOT found.\n";
        exit(1);
    }
} else {
    echo "\n[v] Found Person:\n";
    echo "  - ID: {$person->id}\n";
    echo "  - Name: {$person->first_name} {$person->family_name}\n";
    echo "  - Relative ID: " . ($person->relative_id ?: 'NULL') . "\n";
    echo "  - Relationship: " . ($person->relationship ?: 'NULL') . "\n";
}

// 2. Find dependents (those who have this person as relative_id)
echo "\n--- Searching for Dependents ---\n";
$dependents = Person::where('relative_id', $person->id_num)->get();

if ($dependents->isEmpty()) {
    // Try matching numeric or trimmed
    $dependents = Person::whereRaw('TRIM(relative_id) = ?', [$person->id_num])->get();
}

echo "Found count: " . $dependents->count() . "\n";

foreach ($dependents as $child) {
    echo "\n  Dependent Found:\n";
    echo "  - ID: {$child->id}\n";
    echo "  - Name: {$child->first_name} {$child->father_name} {$child->family_name}\n";
    echo "  - Relationship: " . ($child->relationship ?: 'NULL') . "\n";
    echo "  - ID Num: {$child->id_num}\n";
    
    // Check if my export filter would include this child
    $excluded = ['رب الأسرة', 'زوجة', 'زوج', 'wife', 'husband', 'Family Head', 'Head of Family'];
    $rel = trim($child->relationship);
    
    if (in_array($rel, $excluded)) {
        echo "  [!] WARNING: This record is EXCLUDED because relationship '$rel' is in exclusion list.\n";
    } else {
        echo "  [v] This record SHOULD appear in the export.\n";
    }
}

echo "\n--- Query Test Summary ---\n";
echo "If 'Found count' is 0, the export will be empty for this head.\n";
echo "If 'Dependent Found' shown above says [v], then my code IS working for this data.\n";
echo "--------------------------\n";
