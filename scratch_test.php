<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Person;
use Illuminate\Support\Facades\DB;

$areaIds = [18, 19, 20, 21];
echo "Analyzing Regions: " . implode(', ', $areaIds) . "\n";

// 1. Fetch all heads in these regions
$heads = Person::whereNull('relative_id')->where(function($query) use ($areaIds) {
    $query->whereIn('area_responsible_id', $areaIds)
          ->orWhereHas('block', function($q) use ($areaIds) {
              $q->whereIn('area_responsible_id', $areaIds);
          });
})->get();

$headIds = $heads->pluck('id_num')->toArray();
$allMembers = Person::whereIn('relative_id', $headIds)->get();

echo "Total families found in these regions: " . count($heads) . "\n";

$controller = new App\Http\Controllers\AuditController();
$reflector = new ReflectionClass($controller);
$method = $reflector->getMethod('calculateScores');
$method->setAccessible(true);

// Run Scenario 1: check_adult_son = true (Default)
$settingsEnabled = [
    'age_threshold' => 60,
    'infant_score' => 18,
    'child_score' => 10,
    'tent_score' => 15,
    'destroyed_score' => 20,
    'cat_a_min' => 85,
    'cat_b_min' => 60,
    'cat_c_min' => 35,
    'custom_keywords' => '',
    'force_incapacitated' => true,
    'check_adult_son' => true
];
$scoredEnabled = $method->invoke($controller, $heads, $allMembers, $settingsEnabled);

// Run Scenario 2: check_adult_son = false (Disabled)
$settingsDisabled = $settingsEnabled;
$settingsDisabled['check_adult_son'] = false;
$scoredDisabled = $method->invoke($controller, $heads, $allMembers, $settingsDisabled);

// Compare counts
echo "\n=== Scenario 1: check_adult_son = TRUE ===\n";
echo "A: " . $scoredEnabled->where('category', 'A')->count() . "\n";
echo "B: " . $scoredEnabled->where('category', 'B')->count() . "\n";
echo "C: " . $scoredEnabled->where('category', 'C')->count() . "\n";
echo "D: " . $scoredEnabled->where('category', 'D')->count() . "\n";

echo "\n=== Scenario 2: check_adult_son = FALSE ===\n";
echo "A: " . $scoredDisabled->where('category', 'A')->count() . "\n";
echo "B: " . $scoredDisabled->where('category', 'B')->count() . "\n";
echo "C: " . $scoredDisabled->where('category', 'C')->count() . "\n";
echo "D: " . $scoredDisabled->where('category', 'D')->count() . "\n";

// Let's find if there are any differences
$diffs = [];
foreach ($scoredEnabled as $index => $fEnabled) {
    $fDisabled = $scoredDisabled[$index];
    if ($fEnabled->category !== $fDisabled->category) {
        $diffs[] = [
            'name' => $fEnabled->full_name,
            'id' => $fEnabled->id_num,
            'cat_enabled' => $fEnabled->category,
            'cat_disabled' => $fDisabled->category,
            'score_enabled' => $fEnabled->score,
            'score_disabled' => $fDisabled->score
        ];
    }
}

echo "\nTotal families that changed category: " . count($diffs) . "\n";
foreach (array_slice($diffs, 0, 10) as $d) {
    echo "Family: {$d['name']} (ID: {$d['id']}) | Enabled: {$d['cat_enabled']} ({$d['score_enabled']}) | Disabled: {$d['cat_disabled']} ({$d['score_disabled']})\n";
}
