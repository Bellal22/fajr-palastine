<?php
use App\Models\Person;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if there are any children with relative_id
$childrenCount = DB::table('persons')
    ->whereIn('relationship', ['ابن', 'ابنة', 'أخ', 'أخت', 'son', 'daughter', 'brother', 'sister'])
    ->count();
echo "Total children count: $childrenCount\n";

$childrenWithRelativeId = DB::table('persons')
    ->whereIn('relationship', ['ابن', 'ابنة', 'أخ', 'أخت', 'son', 'daughter', 'brother', 'sister'])
    ->whereNotNull('relative_id')
    ->where('relative_id', '!=', '')
    ->count();
echo "Children with relative_id: $childrenWithRelativeId\n";

// Sample a child with relative_id
$sampleChild = DB::table('persons')
    ->whereIn('relationship', ['ابن', 'ابنة', 'أخ', 'أخت', 'son', 'daughter', 'brother', 'sister'])
    ->whereNotNull('relative_id')
    ->where('relative_id', '!=', '')
    ->first();

if ($sampleChild) {
    echo "Sample child: " . $sampleChild->first_name . " " . $sampleChild->family_name . " (ID: " . $sampleChild->id_num . ")\n";
    echo "Child relative_id: " . $sampleChild->relative_id . "\n";
    
    // Check if parent exists by id_num
    $parentByIdNum = DB::table('persons')
        ->where('id_num', $sampleChild->relative_id)
        ->first();
    
    if ($parentByIdNum) {
        echo "Parent found by id_num: " . $parentByIdNum->first_name . " " . $parentByIdNum->family_name . "\n";
        echo "Parent relative_id: [" . $parentByIdNum->relative_id . "]\n";
        echo "Parent relationship: [" . $parentByIdNum->relationship . "]\n";
    } else {
        echo "Parent NOT found by id_num: " . $sampleChild->relative_id . "\n";
        
        // Maybe relative_id is the primary key id?
        $parentById = DB::table('persons')
            ->where('id', $sampleChild->relative_id)
            ->first();
        if ($parentById) {
            echo "Parent found by DB ID: " . $parentById->first_name . " " . $parentById->family_name . "\n";
        } else {
            echo "Parent NOT found by DB ID: " . $sampleChild->relative_id . "\n";
        }
    }
} else {
    echo "No children with relative_id found at all.\n";
}

// Check what relationships exist in DB
$relationships = DB::table('persons')->distinct()->pluck('relationship');
echo "All relationships in DB: " . implode(', ', $relationships->toArray()) . "\n";
