<?php
// سكربت خفيف لا يعتمد على تحميل Laravel بالكامل لتجنب أخطاء الإضافات المفقودة
$host = 'localhost';
$db   = 'fajr_palestine'; // اسم القاعدة بناءً على سياق المشروع
$user = 'root';
$pass = '';

// محاولة معرفة بيانات الاتصال من ملف .env
$env = file_get_contents('.env');
if (preg_match('/DB_DATABASE=(.*)/', $env, $m)) $db = trim($m[1]);
if (preg_match('/DB_USERNAME=(.*)/', $env, $m)) $user = trim($m[1]);
if (preg_match('/DB_PASSWORD=(.*)/', $env, $m)) $pass = trim($m[1]);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$id = '918997016';
$stmt = $pdo->prepare("SELECT * FROM persons WHERE id_num = ?");
$stmt->execute([$id]);
$head = $stmt->fetch(PDO::FETCH_OBJ);

if (!$head) die("Head $id not found\n");

$stmt = $pdo->prepare("SELECT * FROM persons WHERE relative_id = ?");
$stmt->execute([$id]);
$members = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "--- FAMILY HEAD ---\n";
echo "Name: " . $head->first_name . " " . $head->family_name . "\n";
echo "Condition: " . $head->condition_description . "\n";
echo "DOB: " . $head->dob . "\n\n";

echo "--- MEMBERS ---\n";
foreach ($members as $m) {
    echo "Name: " . $m->first_name . " | Rel: " . $m->relationship . " | DOB: " . $m->dob . " | Cond: " . $m->condition_description . "\n";
}
