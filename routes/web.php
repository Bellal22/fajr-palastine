<?php

Route::get('/debug-id/{id}', function($id) {
    if (!auth()->check()) return "Please login first as Admin";
    
    $person = \App\Models\Person::where('id_num', $id)->first();
    if (!$person) return "Person $id not found";
    
    $members = \App\Models\Person::where('relative_id', $person->id_num)->get();
    
    // محاكاة منطق التدقيق
    $primaryHeads = collect([$person]);
    $spouse = $members->first(fn($m) => in_array(trim($m->relationship), ['زوج', 'زوجة', 'husband', 'wife', 'Wife', 'Husband']));
    if ($spouse) $primaryHeads->push($spouse);

    $analysis = [];
    $hasHealthyParent = false;

    foreach ($primaryHeads as $p) {
        $pHealth = \App\Audit\MedicalLibrary::calculateHealthScore($p->condition_description);
        $age = 0;
        try { if($p->dob) { $age = \Carbon\Carbon::parse($p->dob)->age; if($age==0 && strlen($p->dob)>=4) $age = date('Y')-substr($p->dob,0,4); } } catch(\Exception $e){}
        
        $incapacitated = ($pHealth > 0 || $age >= 60);
        if (!$incapacitated) $hasHealthyParent = true;

        $analysis[] = [
            'role' => $p->relationship ?: 'Head',
            'name' => $p->first_name . ' ' . $p->family_name,
            'age' => $age,
            'health_score' => $pHealth,
            'incapacitated' => $incapacitated ? 'Yes' : 'No (Healthy)'
        ];
    }

    $sonAnalysis = "Searching...";
    $hasHealthySon = false;
    if (!$hasHealthyParent) {
        $son = $members->filter(function($m) {
            $rel = mb_strtolower(trim($m->relationship));
            $isSon = (mb_strpos($rel, 'ابن') !== false || mb_strpos($rel, 'ولد') !== false || mb_strpos($rel, 'son') !== false);
            try { 
                if (empty($m->dob)) return false;
                $age = \Carbon\Carbon::parse($m->dob)->age;
                if ($age == 0 && strlen($m->dob) >= 4) $age = date('Y') - substr($m->dob, 0, 4);
                return $isSon && $age >= 18;
            } catch(\Exception $e) { return false; }
        })->sortBy('dob')->first();

        if ($son) {
            $sHealth = \App\Audit\MedicalLibrary::calculateHealthScore($son->condition_description);
            if ($sHealth == 0) {
                $hasHealthySon = true;
                $sonAnalysis = "Found Healthy Adult Son: " . $son->first_name . " (Age: " . (\Carbon\Carbon::parse($son->dob)->age ?: '18+') . ")";
            } else {
                $sonAnalysis = "Found Son but he is SICK (Health Score: $sHealth)";
            }
        } else {
            $sonAnalysis = "No Adult Son (>=18) found";
        }
    } else {
        $sonAnalysis = "Skipped (Family has a healthy parent who can support)";
    }

    return response()->json([
        'family_head' => $person->first_name . ' ' . $person->family_name,
        'parents_analysis' => $analysis,
        'has_healthy_parent_provider' => $hasHealthyParent,
        'adult_son_analysis' => $sonAnalysis,
        'will_be_capped_at_B' => $hasHealthySon ? 'YES' : 'NO',
        'final_decision' => $hasHealthySon ? 'Category B (High Priority with Support)' : 'Calculated by Score'
    ]);
});

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

foreach (glob(__DIR__.'/web/*.php') as $routes) {
    include $routes;
}

