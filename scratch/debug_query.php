<?php

use App\Models\Person;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Debugging FamilyChildrenExport Query ---\n";

$childCount = DB::table('persons')->count();
echo "Total persons: $childCount\n";

$withRelative = DB::table('persons')->whereNotNull('relative_id')->where('relative_id', '!=', '')->count();
echo "Persons with relative_id: $withRelative\n";

$relationships = DB::table('persons')
    ->select('relationship', DB::raw('count(*) as count'))
    ->whereNotNull('relative_id')
    ->groupBy('relationship')
    ->get();

echo "Relationships for people with relative_id:\n";
foreach ($relationships as $r) {
    echo "  - " . ($r->relationship ?: 'NULL') . ": " . $r->count . "\n";
}

$testJoin = DB::table('persons as child')
    ->join('persons as parent', 'child.relative_id', '=', 'parent.id_num')
    ->count();
echo "Successful joins (child.relative_id = parent.id_num): $testJoin\n";

$testJoin2 = DB::table('persons as child')
    ->join('persons as parent', 'child.relative_id', '=', 'parent.id')
    ->count();
echo "Successful joins (child.relative_id = parent.id): $testJoin2\n";

$testJoin3 = DB::table('persons as child')
    ->join('persons as parent', function($join) {
        $join->on(DB::raw('LPAD(child.relative_id, 9, "0")'), '=', DB::raw('LPAD(parent.id_num, 9, "0")'));
    })
    ->count();
echo "Successful joins (LPAD match): $testJoin3\n";
