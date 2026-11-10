<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = '918997016';
$head = DB::table('persons')->where('id_num', $id)->first();

if (!$head) {
    die("Error: Head of family with ID $id not found.\n");
}

$members = DB::table('persons')->where('relative_id', $id)->get();

echo "--- FAMILY HEAD ---\n";
echo "Name: " . $head->first_name . " " . $head->family_name . "\n";
echo "ID: " . $head->id_num . "\n";
echo "DOB: " . $head->dob . "\n";
echo "Condition: " . $head->condition_description . "\n";
echo "Housing: " . $head->housing_type . " / " . $head->housing_damage_status . "\n\n";

echo "--- MEMBERS ---\n";
foreach ($members as $m) {
    $rel = $m->relationship;
    $dob = $m->dob;
    $age = "Unknown";
    try {
        if (!empty($dob)) {
            $age = \Carbon\Carbon::parse($dob)->age;
            if ($age == 0 && strlen($dob) >= 4) { $age = date('Y') - substr($dob, 0, 4); }
        }
    } catch(\Exception $e) {}
    
    echo "Name: " . $m->first_name . " | Rel: $rel | Age: $age | Condition: " . $m->condition_description . "\n";
}

echo "\n--- SYSTEM ANALYSIS ---\n";
// Simulation of calculateScores logic
$primaryHeads = [$head];
$spouse = null;
foreach($members as $m) {
    $rel = mb_strtolower(trim($m->relationship));
    if (in_array($rel, ['زوج', 'زوجة', 'husband', 'wife', 'Wife', 'Husband'])) {
        $spouse = $m;
        $primaryHeads[] = $m;
        break;
    }
}

$hasHealthyParent = false;
foreach ($primaryHeads as $p) {
    $pHealth = \App\Audit\MedicalLibrary::calculateHealthScore($p->condition_description);
    $isElderly = false;
    try {
        if (!empty($p->dob)) {
            $dob_p = \Carbon\Carbon::parse($p->dob);
            $age_p = $dob_p->age;
            if ($age_p == 0 && strlen($p->dob) >= 4) { $age_p = date('Y') - substr($p->dob, 0, 4); }
            if ($age_p >= 60) $isElderly = true;
        }
    } catch(\Exception $e) {}

    if ($pHealth > 0 || $isElderly) {
        echo "Parent (" . ($p->relationship ?: 'Head') . ") is INCACAPACITATED (Health: $pHealth, Elderly: " . ($isElderly?'Yes':'No') . ")\n";
    } else {
        echo "Parent (" . ($p->relationship ?: 'Head') . ") is HEALTHY - This blocks the 'Adult Son' logic.\n";
        $hasHealthyParent = true;
    }
}

if (!$hasHealthyParent) {
    echo "Searching for Backup Provider (Adult Son)...\n";
    $son = null;
    foreach($members as $m) {
        $rel = mb_strtolower(trim($m->relationship));
        $isSon = (mb_strpos($rel, 'ابن') !== false || mb_strpos($rel, 'ولد') !== false || mb_strpos($rel, 'son') !== false);
        
        $age = 0;
        try {
            if (!empty($m->dob)) {
                $age = \Carbon\Carbon::parse($m->dob)->age;
                if ($age == 0 && strlen($m->dob) >= 4) { $age = date('Y') - substr($m->dob, 0, 4); }
            }
        } catch(\Exception $e) {}
        
        if ($isSon && $age >= 18) {
            $son = $m;
            echo "Found Adult Son: " . $m->first_name . " (Age: $age)\n";
            $sonHealth = \App\Audit\MedicalLibrary::calculateHealthScore($m->condition_description);
            if ($sonHealth == 0) {
                echo "SUCCESS: Son is Healthy. Priority will be capped at Category B.\n";
            } else {
                echo "FAILED: Son is sick (Score: $sonHealth). No priority cap applied.\n";
            }
            break;
        }
    }
    if (!$son) echo "FAILED: No Adult Son (>= 18) found in members.\n";
}
