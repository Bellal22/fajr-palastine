<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\IncompletePerson;
use App\Models\Person;
use App\Models\Employee;
use App\Models\BanList;
use App\Models\Block;
use App\Models\AreaResponsible;
use App\Exports\IncompleteSkippedExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class IncompletePersonController extends Controller
{
    public function index(Request $request)
    {
        $query = IncompletePerson::whereNull('relationship')->with('wife');

        // --- بحث بأرقام الهويات (متعددة) ---
        if ($request->filled('id_nums')) {
            $rawIds = preg_split('/[\s,\n\r]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
            $paddedIds = array_unique(array_map(fn($id) => str_pad(trim($id), 9, '0', STR_PAD_LEFT), $rawIds));
            $query->whereIn('id_num', $paddedIds);
        }

        // --- بحث بالاسم الأول ---
        if ($request->filled('first_name')) {
            $query->where('first_name', 'LIKE', '%' . $request->first_name . '%');
        }

        // --- بحث باسم الأب ---
        if ($request->filled('father_name')) {
            $query->where('father_name', 'LIKE', '%' . $request->father_name . '%');
        }

        // --- بحث باسم الجد ---
        if ($request->filled('grandfather_name')) {
            $query->where('grandfather_name', 'LIKE', '%' . $request->grandfather_name . '%');
        }

        // --- بحث باسم العائلة ---
        if ($request->filled('family_name')) {
            $query->where('family_name', 'LIKE', '%' . $request->family_name . '%');
        }

        // --- بحث برقم الجوال ---
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', '%' . $request->phone . '%');
        }

        // --- فلترة حسب صحة رقم الهوية (معادلة لونا) ---
        if ($request->filled('id_status')) {
            $luhnSql = "(
                (SUBSTRING(LPAD(id_num, 9, '0'), 1, 1) * 1) +
                (CASE WHEN SUBSTRING(LPAD(id_num, 9, '0'), 2, 1) * 2 > 9 THEN SUBSTRING(LPAD(id_num, 9, '0'), 2, 1) * 2 - 9 ELSE SUBSTRING(LPAD(id_num, 9, '0'), 2, 1) * 2 END) +
                (SUBSTRING(LPAD(id_num, 9, '0'), 3, 1) * 1) +
                (CASE WHEN SUBSTRING(LPAD(id_num, 9, '0'), 4, 1) * 2 > 9 THEN SUBSTRING(LPAD(id_num, 9, '0'), 4, 1) * 2 - 9 ELSE SUBSTRING(LPAD(id_num, 9, '0'), 4, 1) * 2 END) +
                (SUBSTRING(LPAD(id_num, 9, '0'), 5, 1) * 1) +
                (CASE WHEN SUBSTRING(LPAD(id_num, 9, '0'), 6, 1) * 2 > 9 THEN SUBSTRING(LPAD(id_num, 9, '0'), 6, 1) * 2 - 9 ELSE SUBSTRING(LPAD(id_num, 9, '0'), 6, 1) * 2 END) +
                (SUBSTRING(LPAD(id_num, 9, '0'), 7, 1) * 1) +
                (CASE WHEN SUBSTRING(LPAD(id_num, 9, '0'), 8, 1) * 2 > 9 THEN SUBSTRING(LPAD(id_num, 9, '0'), 8, 1) * 2 - 9 ELSE SUBSTRING(LPAD(id_num, 9, '0'), 8, 1) * 2 END) +
                (SUBSTRING(LPAD(id_num, 9, '0'), 9, 1) * 1)
            ) % 10";

            if ($request->id_status === 'valid') {
                $query->whereRaw("id_num REGEXP '^[0-9]{1,9}$' AND {$luhnSql} = 0");
            } elseif ($request->id_status === 'invalid') {
                $query->whereRaw("(id_num IS NULL OR id_num NOT REGEXP '^[0-9]{1,9}$' OR {$luhnSql} != 0)");
            }
        }

        // --- فلترة حسب صحة رقم الجوال (059 أو 056 + 7 أرقام) ---
        if ($request->filled('phone_status')) {
            if ($request->phone_status === 'valid') {
                $query->whereRaw("phone REGEXP '^0?(59|56)[0-9]{7}$'");
            } elseif ($request->phone_status === 'invalid') {
                $query->where(function ($q) {
                    $q->whereNull('phone')
                      ->orWhere('phone', '')
                      ->orWhereRaw("phone NOT REGEXP '^0?(59|56)[0-9]{7}$'");
                });
            }
        }

        $perPage = max(1, min(1000, (int) $request->input('perPage', 20)));
        $people = $query->latest()->paginate($perPage);
        return view('dashboard.incomplete_people.index', compact('people'));
    }

    public function show($id)
    {
        $person = IncompletePerson::with(['wife', 'children'])->findOrFail($id);
        
        // جلب سجل الكوبونات
        $coupons = DB::table('project_beneficiaries')
            ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
            ->leftJoin('sub_warehouses', 'sub_warehouses.id', '=', 'project_beneficiaries.sub_warehouse_id')
            ->select(
                'projects.name as project_name',
                'projects.description as project_description',
                'project_beneficiaries.status',
                'project_beneficiaries.quantity',
                'project_beneficiaries.delivery_date',
                'sub_warehouses.name as warehouse_name',
                'project_beneficiaries.notes',
                'project_beneficiaries.created_at as nomination_date'
            )
            ->where('project_beneficiaries.incomplete_person_id', $person->id)
            ->orderBy('project_beneficiaries.delivery_date', 'desc')
            ->get();

        return view('dashboard.incomplete_people.show', compact('person', 'coupons'));
    }



    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $data = Excel::toArray([], $request->file('excel_file'));
        $sheet = $data[0] ?? [];

        // تجميع كافة أرقام الهويات في الملف مسبقاً للاستعلام السريع
        $allIdsInFile = [];
        foreach ($sheet as $idx => $row) {
            if ($idx == 0) continue;
            $h = preg_replace('/\D/', '', trim((string)($row[0] ?? '')));
            if ($h) $allIdsInFile[] = str_pad($h, 9, '0', STR_PAD_LEFT);
            $w = preg_replace('/\D/', '', trim((string)($row[3] ?? '')));
            if ($w) $allIdsInFile[] = str_pad($w, 9, '0', STR_PAD_LEFT);
        }
        $uniqueIds = array_unique(array_filter($allIdsInFile));

        // جلب السجلات المحظورة والمستفيدين والموظفين والبيانات غير المكتملة مسبقاً بدفعة واحدة O(1)
        $bannedIds = !empty($uniqueIds) ? BanList::whereIn('id_num', $uniqueIds)->pluck('id_num')->flip()->toArray() : [];
        $existingPersonIds = !empty($uniqueIds) ? Person::whereIn('id_num', $uniqueIds)->pluck('id_num')->flip()->toArray() : [];
        $employeeIds = !empty($uniqueIds) ? Employee::whereIn('id_num', $uniqueIds)->pluck('id_num')->flip()->toArray() : [];
        $existingIncompleteIds = !empty($uniqueIds) ? IncompletePerson::whereIn('id_num', $uniqueIds)->pluck('id_num')->flip()->toArray() : [];

        $imported = 0;
        $skippedRows = [];
        $seenHusbandIds = [];
        $seenWifeIds = [];

        foreach ($sheet as $index => $row) {
            if ($index == 0) continue; // تخطي الترويسة

            $rawHusbandId = trim((string)($row[0] ?? ''));
            $husbandName = trim((string)($row[1] ?? ''));
            $rawPhone = trim((string)($row[2] ?? ''));
            $rawWifeId = trim((string)($row[3] ?? ''));
            $wifeName = trim((string)($row[4] ?? ''));

            // تخطي السطور الفارغة تماماً
            if (empty($rawHusbandId) && empty($husbandName) && empty($rawPhone) && empty($rawWifeId) && empty($wifeName)) {
                continue;
            }

            $rowErrors = [];
            $husbandId = null;

            // 1. تدقيق هوية رب الأسرة (الزوج)
            if (empty($rawHusbandId)) {
                $rowErrors[] = 'رقم هوية الزوج فارغ';
            } else {
                $digitsOnly = preg_replace('/\D/', '', $rawHusbandId);
                $husbandId = str_pad($digitsOnly, 9, '0', STR_PAD_LEFT);

                // أ. فحص معادلة لونا
                if (!IncompletePerson::isLuhnValid($husbandId)) {
                    $rowErrors[] = 'رقم هوية الزوج غير صحيح وفق معادلة لونا (Luhn Algorithm)';
                }

                // ب. فحص قائمة الحظر (البلوكات)
                if (isset($bannedIds[$husbandId])) {
                    $rowErrors[] = 'هوية الزوج مدرجة في قائمة الحظر (بلوك)';
                }

                // ج. فحص قائمة المستفيدين المعتمدين (Person)
                if (isset($existingPersonIds[$husbandId])) {
                    $rowErrors[] = 'هوية الزوج مسجلة مسبقاً كمستفيد معتمد في النظام';
                }

                // د. فحص قائمة الموظفين (Employee)
                if (isset($employeeIds[$husbandId])) {
                    $rowErrors[] = 'هوية الزوج مسجلة كـ موظف في الجمعية';
                }

                // هـ. فحص البيانات غير المكتملة مسبقاً
                if (isset($existingIncompleteIds[$husbandId])) {
                    $rowErrors[] = 'هوية الزوج مسجلة مسبقاً في البيانات غير المكتملة';
                }

                // و. فحص التكرار داخل نفس الملف المرفوع
                if (isset($seenHusbandIds[$husbandId])) {
                    $rowErrors[] = 'هوية الزوج مكررة أكثر من مرة في نفس الملف المرفوع';
                }
            }

            // 2. تدقيق رقم الجوال (يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام)
            $phone = null;
            if (!empty($rawPhone)) {
                $phone = IncompletePerson::normalizePhone($rawPhone);
                if (!IncompletePerson::isPhoneValid($phone)) {
                    $rowErrors[] = "رقم الجوال ({$rawPhone}) غير صالح (يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام)";
                }
            }

            // 3. تدقيق بيانات الزوجة إن وجدت
            $wifeId = null;
            if (!empty($rawWifeId)) {
                $digitsWifeOnly = preg_replace('/\D/', '', $rawWifeId);
                $wifeId = str_pad($digitsWifeOnly, 9, '0', STR_PAD_LEFT);

                // أ. فحص معادلة لونا للزوجة
                if (!IncompletePerson::isLuhnValid($wifeId)) {
                    $rowErrors[] = 'رقم هوية الزوجة غير صحيح وفق معادلة لونا (Luhn Algorithm)';
                }

                // ب. فحص قائمة الحظر للزوجة
                if (isset($bannedIds[$wifeId])) {
                    $rowErrors[] = 'هوية الزوجة مدرجة في قائمة الحظر (بلوك)';
                }

                // ج. فحص قائمة المستفيدين للزوجة
                if (isset($existingPersonIds[$wifeId])) {
                    $rowErrors[] = 'هوية الزوجة مسجلة مسبقاً كمستفيد معتمد في النظام';
                }

                // د. فحص قائمة الموظفين للزوجة
                if (isset($employeeIds[$wifeId])) {
                    $rowErrors[] = 'هوية الزوجة مسجلة كـ موظف في الجمعية';
                }

                // هـ. فحص البيانات غير المكتملة للزوجة
                if (isset($existingIncompleteIds[$wifeId])) {
                    $rowErrors[] = 'هوية الزوجة مسجلة مسبقاً في البيانات غير المكتملة';
                }

                // و. فحص تطابق هوية الزوجة مع هوية الزوج
                if (!empty($husbandId) && $wifeId === $husbandId) {
                    $rowErrors[] = 'هوية الزوجة مطابقة تماماً لهوية الزوج في نفس السطر';
                }

                // ز. فحص تكرار هوية الزوجة داخل الملف
                if (isset($seenWifeIds[$wifeId])) {
                    $rowErrors[] = 'هوية الزوجة مكررة أكثر من مرة في نفس الملف المرفوع';
                }
            }

            // إذا وُجد أي سبب للاستبعاد، يتم تخطي السجل وتوثيقه في كشف المستبعدين
            if (!empty($rowErrors)) {
                $skippedRows[] = [
                    'row' => $index + 1,
                    'husband_id' => $rawHusbandId ?: '-',
                    'husband_name' => $husbandName ?: '-',
                    'phone' => $rawPhone ?: '-',
                    'wife_id' => $rawWifeId ?: '-',
                    'wife_name' => $wifeName ?: '-',
                    'reasons' => $rowErrors,
                ];
                continue; // تخطي السجل والانتقال للسجل التالي
            }

            // تسجيل الهويات لتفادي التكرار اللاحق داخل الملف
            if (!empty($husbandId)) {
                $seenHusbandIds[$husbandId] = true;
                $existingIncompleteIds[$husbandId] = true;
            }
            if (!empty($wifeId)) {
                $seenWifeIds[$wifeId] = true;
                $existingIncompleteIds[$wifeId] = true;
            }

            // إدراج رب الأسرة
            $nameParts = $this->splitName($husbandName);
            IncompletePerson::create(array_merge($nameParts, [
                'id_num' => $husbandId,
                'phone' => $phone,
                'notes' => 'مستورد من إكسل - رب أسرة (بيانات ناقصة)',
            ]));
            $imported++;

            // إدراج الزوجة
            if (!empty($wifeId)) {
                $wifeNameParts = $this->splitName($wifeName);
                IncompletePerson::create(array_merge($wifeNameParts, [
                    'id_num' => $wifeId,
                    'relative_id' => $husbandId,
                    'relationship' => 'زوجة',
                    'notes' => 'مستورد من إكسل - زوجة (بيانات ناقصة)',
                ]));
                $imported++;
            }
        }

        // حفظ تقرير المستبعدين في الجلسة ليتمكن المستخدم من تنزيله ومراجعته
        session()->put('last_incomplete_skipped_report', $skippedRows);

        $skippedCount = count($skippedRows);
        if ($skippedCount > 0) {
            flash()->warning("اكتمل الاستيراد: تم بنجاح إضافة {$imported} فرد، وتم تخطي {$skippedCount} سجل لمخالفتها الشروط (أخطاء لونا، جوال غير صالح، أو مدرجة في الحظر/الموظفين/المستفيدين/المكرر). يمكنك تنزيل تقرير الإفادة بالمستبعدين لمراجعتها.");
        } else {
            flash()->success("اكتمل الاستيراد بنجاح تام! تم إدخال {$imported} فرد دون استبعاد أي سجل.");
        }

        return back();
    }

    public function exportSkipped()
    {
        $data = session('last_incomplete_skipped_report', []);

        if (empty($data)) {
            flash()->warning('لا يوجد تقرير استبعاد متاح حالياً للتنزيل.');
            return back();
        }

        return Excel::download(
            new IncompleteSkippedExport($data),
            'كشف_السجلات_المستبعدة_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;

        if (empty($ids)) {
            flash()->error('يرجى تحديد سجلات أولاً');
            return back();
        }

        if ($action == 'transfer') {
            return $this->processTransfer($ids);
        }

        if ($action == 'delete') {
            IncompletePerson::whereIn('id', $ids)->orWhereIn('relative_id', function($query) use ($ids) {
                $query->select('id_num')->from('incomplete_people')->whereIn('id', $ids);
            })->delete();
            flash()->success('تم حذف السجلات المحددة بنجاح');
            return back();
        }

        return back();
    }

    private function processTransfer($ids)
    {
        $transferred = 0;
        $skipped = 0;
        $errors = [];

        $heads = IncompletePerson::whereIn('id', $ids)->with('wife')->get();
        $personColumns = DB::getSchemaBuilder()->getColumnListing('persons');

        foreach ($heads as $head) {
            if (Person::where('id_num', $head->id_num)->exists()) {
                $skipped++;
                continue;
            }

            // التحقق من صحة رقم الهوية قبل النقل
            if (!IncompletePerson::isLuhnValid($head->id_num)) {
                $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن رقم الهوية غير مطابق لمعادلة لونا.";
                continue;
            }

            // التحقق من قائمة الحظر (البلوكات) لرب الأسرة
            if (BanList::where('id_num', $head->id_num)->exists()) {
                $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن الهوية مدرجة في قائمة الحظر (بلوك).";
                continue;
            }

            // التحقق من قائمة الموظفين لرب الأسرة
            if (Employee::where('id_num', $head->id_num)->exists()) {
                $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن الهوية مسجلة كـ موظف.";
                continue;
            }

            // التحقق من صحة رقم الجوال قبل النقل إذا كان مدخلاً
            if (!empty($head->phone) && !IncompletePerson::isPhoneValid($head->phone)) {
                $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن رقم الجوال ({$head->phone}) غير صالح (يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام).";
                continue;
            }

            // التحقق من صحة بيانات الزوجة
            if ($head->wife) {
                if (!IncompletePerson::isLuhnValid($head->wife->id_num)) {
                    $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن هوية الزوجة ({$head->wife->id_num}) غير مطابقة لمعادلة لونا.";
                    continue;
                }
                if (BanList::where('id_num', $head->wife->id_num)->exists()) {
                    $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن هوية الزوجة ({$head->wife->id_num}) مدرجة في قائمة الحظر (بلوك).";
                    continue;
                }
                if (Employee::where('id_num', $head->wife->id_num)->exists()) {
                    $errors[] = "السجل ({$head->getFullName()} - {$head->id_num}): لا يمكن النقل لأن هوية الزوجة ({$head->wife->id_num}) مسجلة كـ موظف.";
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                // 1. Prepare Person Data
                $data = $head->toArray();
                unset($data['id'], $data['created_at'], $data['updated_at'], $data['wife']);
                
                // تصفية البيانات لتشمل فقط الأعمدة الموجودة فعلياً في جدول الـ persons
                $data = array_intersect_key($data, array_flip($personColumns));

                // تعيين رمز المرور الافتراضي لرب الأسرة فقط
                $data['passkey'] = '123456789';

                $data['gender'] = $data['gender'] ?? 'ذكر';
                $data['person_status'] = $data['person_status'] ?? 'فعال';
                $data['relatives_count'] = $head->wife ? 2 : 1; // 1 لو ما فيه زوجة لو في زوجة حطو 2
                $data['has_condition'] = $data['has_condition'] ?? 0;
                $data['is_frozen'] = $data['is_frozen'] ?? 0;

                // 2. Direct Insert to Persons
                DB::table('persons')->insert(array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));

                // 3. Process Wife if exists
                if ($head->wife) {
                    $wife = $head->wife;
                    if (!Person::where('id_num', $wife->id_num)->exists()) {
                        $wifeData = $wife->toArray();
                        unset($wifeData['id'], $wifeData['created_at'], $wifeData['updated_at']);
                        
                        // تصفية بيانات الزوجة أيضاً
                        $wifeData = array_intersect_key($wifeData, array_flip($personColumns));

                        $wifeData['passkey'] = null; // الزوجة لا تحتاج رمز مرور افتراضي
                        
                        $wifeData['gender'] = $wifeData['gender'] ?? 'أنثى';
                        $wifeData['relationship'] = 'زوجة';
                        $wifeData['person_status'] = 'فعال';
                        $wifeData['relatives_count'] = 0; // الزوجة تتبع لرب الأسرة
                        $wifeData['has_condition'] = $wifeData['has_condition'] ?? 0;
                        $wifeData['is_frozen'] = $wifeData['is_frozen'] ?? 0;

                        DB::table('persons')->insert(array_merge($wifeData, [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]));
                    }
                    $wife->delete();
                }

                $head->delete();
                $transferred++;
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "الهوية {$head->id_num}: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            flash()->error("حدثت بعض الأخطاء: " . implode(' | ', $errors));
        } else {
            flash()->success("تم النقل بنجاح. العدد: {$transferred}، تم تخطي المكرر: {$skipped}");
        }
        
        return back();
    }

    private function splitName($fullName)
    {
        $fullName = trim($fullName);
        $parts = explode(' ', $fullName);
        return [
            'first_name' => $parts[0] ?? null,
            'father_name' => $parts[1] ?? null,
            'grandfather_name' => $parts[2] ?? null,
            'family_name' => count($parts) > 3 ? implode(' ', array_slice($parts, 3)) : null,
        ];
    }

    private function formatPhone($phone)
    {
        return IncompletePerson::normalizePhone($phone);
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\IncompletePeopleTemplateExport, 'incomplete_people_template.xlsx');
    }

    public function destroy($id)
    {
        IncompletePerson::findOrFail($id)->delete();
        flash()->success('تم الحذف بنجاح');
        return back();
    }

    public function moveFromPersons(Request $request)
    {
        $ids = $request->items ?? $request->ids;
        if (empty($ids)) {
            flash()->error('يرجى تحديد أشخاص أولاً');
            return back();
        }

        $people = Person::whereIn('id', $ids)->get();
        $scrapColumns = DB::getSchemaBuilder()->getColumnListing('incomplete_people');
        $moved = 0;

        foreach ($people as $person) {
            DB::beginTransaction();
            try {
                // 1. Prepare Scrap Data
                $data = $person->toArray();
                unset($data['id'], $data['created_at'], $data['updated_at']);
                
                // Filter to only existing scrap columns
                $data = array_intersect_key($data, array_flip($scrapColumns));

                // Add note
                $data['notes'] = ($data['notes'] ?? '') . " | تم إرجاعه من جدول الأفراد بتاريخ " . now()->format('Y-m-d');

                // 2. Insert to Incomplete
                IncompletePerson::create($data);

                // 3. Delete from Person
                $person->delete();

                // 4. Move Wife if exists (only if this person is a head)
                if (is_null($person->relative_id)) {
                    $wife = Person::where('relative_id', $person->id_num)
                        ->whereIn('relationship', ['زوجة', 'wife'])
                        ->first();
                    
                    if ($wife) {
                        $wifeData = $wife->toArray();
                        unset($wifeData['id'], $wifeData['created_at'], $wifeData['updated_at']);
                        $wifeData = array_intersect_key($wifeData, array_flip($scrapColumns));
                        $wifeData['notes'] = ($wifeData['notes'] ?? '') . " | تم إرجاعه آلياً مع رب الأسرة بتاريخ " . now()->format('Y-m-d');
                        IncompletePerson::create($wifeData);
                        $wife->delete();
                    }
                }

                DB::commit();
                $moved++;
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error("Error moving person back to scrap: " . $e->getMessage());
            }
        }

        flash()->success("تم نقل {$moved} سجل إلى البيانات غير المكتملة بنجاح");
        return back();
    }

    public function edit($id)
    {
        $person = IncompletePerson::with(['wife', 'children'])->findOrFail($id);
        $blocks = Block::orderBy('name')->pluck('name', 'id');
        $areaResponsibles = \App\Models\AreaResponsible::orderBy('name')->pluck('name', 'id');
        
        return view('dashboard.incomplete_people.edit', compact('person', 'blocks', 'areaResponsibles'));
    }

    public function update(Request $request, $id)
    {
        $person = IncompletePerson::findOrFail($id);
        
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'grandfather_name' => 'nullable|string|max:255',
            'family_name' => 'required|string|max:255',
            'id_num' => [
                'required',
                'string',
                'digits:9',
                'unique:incomplete_people,id_num,' . $id,
                'unique:persons,id_num',
                function ($attribute, $value, $fail) {
                    if (!IncompletePerson::isLuhnValid($value)) {
                        $fail('رقم الهوية غير صحيح بناءً على معادلة التدقيق (معادلة لونا).');
                    }
                    if (BanList::where('id_num', $value)->exists()) {
                        $fail('رقم الهوية مدرج في قائمة الحظر (البلوكات) وممنوع إضافته.');
                    }
                    if (Employee::where('id_num', $value)->exists()) {
                        $fail('رقم الهوية مسجل كـ موظف في الجمعية وممنوع إضافته.');
                    }
                }
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^0?(59|56)\d{7}$/',
            ],
            'area' => 'nullable|string|max:255',
            'social_status' => 'nullable|string|max:255',
            'block_id' => 'nullable|exists:blocks,id',
            'area_responsible_id' => 'nullable|exists:area_responsibles,id',
            'notes' => 'nullable|string',
        ], [
            'id_num.required' => 'رقم الهوية مطلوب.',
            'id_num.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام.',
            'id_num.unique' => 'رقم الهوية مسجل مسبقاً في النظام.',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام (10 أرقام إجمالاً).',
        ]);

        if (!empty($data['phone'])) {
            $data['phone'] = IncompletePerson::normalizePhone($data['phone']);
        }

        $person->update($data);

        flash()->success('تم تحديث البيانات بنجاح');
        return redirect()->route('dashboard.incomplete_people.show', $id);
    }
}
