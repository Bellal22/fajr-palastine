<?php
// استخدام MySQLi لضمان العمل على السيرفرات التي تفتقد لـ PDO
$env = file_get_contents('.env');
preg_match('/DB_DATABASE=(.*)/', $env, $m); $db = trim($m[1]);
preg_match('/DB_USERNAME=(.*)/', $env, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.*)/', $env, $m); $pass = trim($m[1]);

$conn = new mysqli('localhost', $user, $pass, $db);
$conn->set_charset("utf8mb4");

$id = '918997016';
$res = $conn->query("SELECT * FROM persons WHERE id_num = '$id'");
$head = $res->fetch_object();

if (!$head) die("ID not found in database");

$res = $conn->query("SELECT * FROM persons WHERE relative_id = '$id'");
$members = [];
while($row = $res->fetch_object()) $members[] = $row;

echo "Analysis for ID: $id\n";
echo "Head: " . $head->first_name . " " . $head->family_name . " (DOB: " . $head->dob . ")\n";
echo "Health: " . $head->condition_description . "\n";

$hasHealthyParent = false;
$primaryHeads = [$head];
foreach($members as $m) {
    if (in_array(trim($m->relationship), ['زوج', 'زوجة', 'wife', 'husband'])) $primaryHeads[] = $m;
}

foreach($primaryHeads as $p) {
    // محاكاة تقييم المكتبة الطبية بشكل مبسط هنا
    $isSick = (strpos($p->condition_description, 'سليم') === false && !empty($p->condition_description));
    $age = date('Y') - substr($p->dob, 0, 4);
    if (!$isSick && $age < 60) $hasHealthyParent = true;
    echo "- Parent (" . ($p->relationship ?: 'Head') . "): " . ($isSick ? 'Sick' : 'Healthy') . ", Age: $age\n";
}

$hasHealthySon = false;
foreach($members as $m) {
    $rel = $m->relationship;
    $isSon = (strpos($rel, 'ابن') !== false || strpos($rel, 'ولد') !== false);
    $age = date('Y') - substr($m->dob, 0, 4);
    $isHealthy = (strpos($m->condition_description, 'سليم') !== false || empty($m->condition_description));
    
    if ($isSon && $age >= 18 && $isHealthy) {
        $hasHealthySon = true;
        echo "- Found Supporter: Son " . $m->first_name . " (Age: $age, Status: Healthy)\n";
        break;
    }
}

echo "\n--- FINAL RESULT ---\n";
if ($hasHealthySon && !$hasHealthyParent) {
    echo "CATEGORY: B (Capped due to Adult Son supporter)\n";
} else {
    echo "CATEGORY: Calculated by score (Will be A if parents are critical and no son)\n";
}
