<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Person;

$ids = ['949823546'];
foreach ($ids as $id_num) {
    echo "Debugging Score for ID: $id_num\n";
    $p = Person::where('id_num', $id_num)->first();
    if ($p) {
        $parents = Person::where(function($q) use ($id_num) {
            $q->where('id_num', $id_num)->orWhere(function($sq) use ($id_num) {
                $sq->where('relative_id', $id_num)->whereIn('relationship', ['زوجة', 'زوج']);
            });
        })->get();
        
        $members = Person::where('relative_id', $id_num)
            ->whereNotIn('relationship', ['زوجة', 'زوج'])
            ->get();

        $score = 0;
        $memberCount = count($members) + count($parents);
        $sizeScore = min($memberCount * 3, 30);
        $score += $sizeScore;
        echo "Base Size Score ($memberCount members): $sizeScore\n";

        $criticalKeywords = 'سرطان|كانسر|كيماوي|فشل كلوي|غسيل|قلب مفتوح|شرايين|كبد|قلب|فشل|أورام|ورم';
        $isParentIncapacitated = false;
        $hasHealthyParent = false;

        foreach ($parents as $parent) {
            $hScore = \App\Audit\MedicalLibrary::calculateHealthScore($parent->condition_description);
            $isCritical = preg_match('/' . $criticalKeywords . '/u', $parent->condition_description);
            $age = 40; // Simulated age for now, or use real one
            // Real age:
            $dob = $parent->dob;
            $age = 0;
            if ($dob) {
                $year = (int)substr($dob, 0, 4);
                $age = date('Y') - $year;
            }
            
            echo "PARENT: " . $parent->first_name . " Age: $age, Cond: " . ($parent->condition_description ?: 'None') . ", H-Score: $hScore, Critical: " . ($isCritical ? 'YES' : 'NO') . "\n";
            
            if ($isCritical || $age >= 60) {
                $isParentIncapacitated = true;
            }
            if (!$isCritical && $age < 60 && empty($parent->condition_description)) {
                $hasHealthyParent = true;
            }
        }

        if ($isParentIncapacitated || !$hasHealthyParent) {
            $score = max($score, 85);
            echo "FORCE UPGRADE to 85 (Incapacitated/No Healthy Parent)\n";
        }

        foreach ($members as $m) {
            $hScore = \App\Audit\MedicalLibrary::calculateHealthScore($m->condition_description);
            if ($hScore > 0) {
                $added = $hScore * 0.3;
                $score += $added;
                echo "  MEM: " . $m->first_name . " Cond: " . $m->condition_description . " H-Score: $hScore (+ $added)\n";
            }
        }
        
        echo "FINAL SCORE: $score\n";
    }
}
