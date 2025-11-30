<?php

namespace App\Http\Controllers;

use App\Enums\Person\PersonCity;
use App\Enums\Person\PersonCurrentCity;
use App\Enums\Person\PersonDamageHousingStatus;
use App\Enums\Person\PersonHousingType;
use App\Enums\Person\PersonNeighborhood;
use App\Enums\Person\PersonRelationship;
use App\Enums\Person\PersonSocialStatus;
use App\Http\Requests\StoreFamilyRequest;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdateFamilyRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\BanList;
use App\Models\Person;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PersonController extends Controller
{
    public function check(Request $request)
    {
        $request->session()->forget('id_num');
        $request->validate([
            'id_num'           => ['required', 'numeric', 'digits:9', 'unique:persons,id_num'],
        ], [
            'id_num.unique'    => 'هذا الشخص موجود بالفعل',
            'id_num.required'  => 'الرجاء ادخال رقم الهوية',
            'id_num.numeric'   => 'رقم الهوية يجب ان يكون ارقام فقط',
            'id_num.digits'    => 'رقم الهوية يجب ان يكون 9 ارقام',
        ]);

        // store the id_num in session
        $request->session()->put('id_num', $request->id_num);
        return redirect()->route('persons.create');
    }

    public function create(Request $request)
    {
        if (!$request->session()->has('id_num')) {
            return redirect()->route('persons.intro');
        }

        $collections = [
            'social_statuses'         => PersonSocialStatus::toValues(),
            'cities'                  => PersonCity::toValues(),
            'current_cities'          => PersonCurrentCity::toValues(),
            'neighborhoods'           => PersonNeighborhood::toValues(),
            'housing_types'           => PersonHousingType::toValues(),
            'housing_damage_statuses' => PersonDamageHousingStatus::toValues(),
        ];

        // تحويل جميع القيم إلى مصفوفات قابلة للعرض
        $data = collect($collections)
            ->map(fn($values) => collect($values)->mapWithKeys(fn($value) => [$value => __($value)]))
            ->all();

        return view('person', array_merge(['id_num' => $request->session()->get('id_num')], $data));
    }

    public function store(StorePersonRequest $request)
    {
        try {
            $id_num = $request->session()->get('id_num');
            if (!$id_num) {
                return response()->json([
                    'success' => false,
                    'error'   => 'رقم الهوية غير موجود في الجلسة.'
                ], 400);
            }

            // فحص قائمة المحظورين قبل أي شيء
            $banned = BanList::where('id_num', $id_num)->first();
            if ($banned) {
                return back()->withErrors([
                    'id_num' => 'لا يمكن التسجيل بهذا الرقم لأنه مرفوض من النظام. سبب الرفض: ' . ($banned->reason ?? 'غير محدد')
                ]);
            }

            $data = $request->validated();
            $data['id_num']   = $id_num;
            $data['phone']    = Str::of($data['phone'])->replace('-', '')->toInteger();
            $data['relationship'] = 'رب الأسرة نفسه';

            $request->session()->put('person', array_merge(
                $request->all(),
                [
                    'id_num'  => $id_num,
                    'passkey' => '123456789'
                ]
            ));

            $firstPersonData = [
                'id_num'                => $data['id_num'],
                'first_name'            => $data['first_name'],
                'father_name'           => $data['father_name'],
                'grandfather_name'      => $data['grandfather_name'],
                'family_name'           => $data['family_name'],
                'dob'                   => $data['dob'],
                'relationship'          => $data['relationship'],
                'has_condition'         => $data['has_condition'],
                'condition_description' => $data['condition_description'],
            ];

            $request->session()->put('first_person_data', $firstPersonData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ');
        }

        return redirect()->route('persons.createFamily');
    }

    public function createFamily(Request $request)
    {
        $session = $request->session();

        if (!$session->has('id_num')) {
            return redirect()->route('persons.intro');
        }

        // استخراج القيم المطلوبة من الجلسة
        $keys = ['id_num', 'first_name', 'father_name', 'grandfather_name', 'family_name'];
        $personData = array_intersect_key($session->all(), array_flip($keys));

        $peopleList = $session->get('peopleList', []);

        $relationships = collect(PersonRelationship::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $person = $request->session()->get('person');

        return view('family', array_merge($personData, compact('relationships', 'peopleList','person')));
    }

    public function storeFamily(StoreFamilyRequest $request)
    {
        $session = $request->session();
        $id_num  = $session->get('id_num');

        // في حال عدم وجود رقم الهوية في الجلسة
        if (!$id_num) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهوية غير موجود في الجلسة، يرجى البدء من جديد.'
            ]);
        }

        // رب الأسرة مسجل مسبقًا في persons
        if (Person::where('id_num', $id_num)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'رب الأسرة مسجل مسبقًا، لا يمكن تكرار التسجيل.'
            ]);
        }

        // جلب بيانات أفراد الأسرة من الجلسة
        $peopleList = $session->get('peopleList', []);
        $person     = $session->get('person'); // بيانات رب الأسرة من الخطوة السابقة

        // لرئيس الأسرة المحظور
        if ($banned = BanList::where('id_num', $id_num)->first()) {
            return response()->json([
                'success' => false,
                'message' => "رقم الهوية {$banned->id_num} مرفوض: " . ($banned->reason ?? 'غير محدد'),
                'rejected_id' => $banned->id_num,
                'reason' => $banned->reason ?? 'غير محدد'
            ]);
        }

        // لأحد أفراد الأسرة المحظورين
        foreach ($peopleList as $p) {
            if (!empty($p['id_num'])) {
                if ($banned = BanList::where('id_num', $p['id_num'])->first()) {
                    return response()->json([
                        'success' => false,
                        'message' => "رقم الهوية {$banned->id_num} (من أفراد الأسرة) مرفوض: " . ($banned->reason ?? 'غير محدد'),
                        'rejected_id' => $banned->id_num,
                        'reason' => $banned->reason ?? 'غير محدد'
                    ]);
                }
            }
        }

        // عدد الأقارب (أفراد الأسرة) + رب الأسرة
        $person['relatives_count'] = count($peopleList) + 1;

        // فحص كل فرد في peopleList لو رقمه محظور
        foreach ($peopleList as $p) {
            if (!empty($p['id_num'])) {
                if ($banned = BanList::where('id_num', $p['id_num'])->first()) {
                    return response()->json([
                        'success'     => false,
                        'rejected_id' => $banned->id_num,
                        'reason'      => $banned->reason ?? 'غير محدد',
                        'message'     => 'لا يمكن إضافة بعض أفراد الأسرة لأن أحد أرقام الهويات موجود في قائمة المحظورين.'
                    ]);
                }
            }
        }

        // تجهيز مصفوفة الأشخاص للإدخال في persons
        $persons = collect($peopleList)
            ->map(function ($p) use ($id_num) {
                if (array_key_exists('area_responsible_id', $p) && $p['area_responsible_id'] === '') {
                    $p['area_responsible_id'] = null;
                }

                if (isset($p['phone'])) {
                    $cleanPhone = str_replace('-', '', $p['phone']);
                    $cleanPhone = ltrim($cleanPhone, '0');
                    $p['phone'] = $cleanPhone;
                }

                return array_merge($p, [
                    'relative_id'   => $id_num,
                    'id_num'        => $p['id_num'] ?? $id_num,
                    'has_condition' => array_key_exists('has_condition', $p) && $p['has_condition'] === '' ? 0 : ($p['has_condition'] ?? 0),
                ]);
            })
            ->push(array_merge($person, [
                'area_responsible_id' => (array_key_exists('area_responsible_id', $person) && $person['area_responsible_id'] === '') ? null : ($person['area_responsible_id'] ?? null),
                'id_num'        => $person['id_num'] ?? $id_num,
                'has_condition' => array_key_exists('has_condition', $person) && $person['has_condition'] === '' ? 0 : ($person['has_condition'] ?? 0),
                'phone'         => isset($person['phone']) ? ltrim(str_replace('-', '', $person['phone']), '0') : null,
            ]))
            ->toArray();

        try {
            DB::beginTransaction();

            foreach ($persons as $row) {
                Person::create($row);
            }

            DB::commit();

            // تنظيف بيانات الجلسة
            $session->forget('peopleList');
            // ممكن برضه تنظف person و id_num لو حابب
            // $session->forget(['person', 'id_num']);

            return response()->json([
                'success'  => true,
                'redirect' => route('persons.success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل البيانات: ' . $e->getMessage()
            ]);
        }
    }

    public function addFamily(Request $request)
    {
        Log::info('Received Data:', $request->all());

        // التحقق من صحة البيانات
        try {
            $validatedData = $request->validate([
                'id_num'                => 'required|numeric|digits:9',
                'first_name'            => 'required|string|regex:/^[\p{Arabic} ]+$/u',
                'father_name'           => 'required|string|regex:/^[\p{Arabic} ]+$/u',
                'grandfather_name'      => 'required|string|regex:/^[\p{Arabic} ]+$/u',
                'family_name'           => 'required|string|regex:/^[\p{Arabic} ]+$/u',
                'dob'                   => 'required|date',
                'relationship'          => 'required|string',
                'has_condition'         => 'required|in:0,1',
                'condition_description' => 'nullable|string|max:500'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        }

        // ✅ التحقق من قائمة الحظر أولاً
        $banned = BanList::where('id_num', $validatedData['id_num'])->first();
        if ($banned) {
            return response()->json([
                'success' => false,
                'rejected_id' => $banned->id_num,
                'reason' => $banned->reason ?? 'غير محدد',
                'message' => "لا يمكن إضافة الفرد برقم الهوية {$banned->id_num} لأنه مرفوض من النظام. سبب الرفض: " . ($banned->reason ?? 'غير محدد')
            ], 422);
        }

        // ✅ التحقق من عدم تكرار رقم الهوية
        $existingPerson = Person::where('id_num', $validatedData['id_num'])->first();
        if ($existingPerson) {
            return response()->json([
                'success' => false,
                'message' => "رقم الهوية {$validatedData['id_num']} مسجل مسبقاً في النظام."
            ], 422);
        }

        // الحصول على رقم هوية المستخدم الحالي
        $relative_id = session('person')['id_num'] ?? null;

        if (!$relative_id) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: لم يتم العثور على معلومات المستخدم في الجلسة.'
            ], 401);
        }

        $validatedData['relative_id'] = $relative_id;

        try {
            // إنشاء السجل الجديد
            $person = Person::create($validatedData);

            // تحديث عدد الأقارب للمستخدم الأساسي
            $parentPerson = Person::where('id_num', $relative_id)->first();
            if ($parentPerson) {
                $parentPerson->increment('relatives_count');
                Log::info("✅ Updated relatives_count for parent person with id_num: $relative_id");
            } else {
                Log::warning("⚠️ Parent person with id_num: $relative_id not found.");
            }

            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة الفرد بنجاح',
                'person'  => $person
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error creating person: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الفرد. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    public function update(UpdatePersonRequest $request)
    {
        if (!session('person')) {
            return redirect()->route('loginView')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        $data = $request->validated();

        $data['phone']    =  Str::of($data['phone'])
            ->replace('-', '')
            ->toInteger();


        // $person = Person::create($data);
        $request->session()->put('person', $data);

        return redirect()->route('profile');
    }

    public function updateFamily(UpdateFamilyRequest $request)
    {
        $id_num = $request->session()->get('id_num');
        if (!$id_num) {
            return redirect()
                ->route('loginView')
                ->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        $data = $request->validated();
        $person = $request->session()->get('person');
        $person['relatives_count'] = count($data['persons']);

        // شرط تعدد الزوجات
        if ($person['social_status'] === 'polygamous' && $person['relatives_count'] < 2) {
            return back()->withErrors(['persons' => 'عدد الاقارب يجب ان يكون اكثر من 1']);
        }

        // فحص رب الأسرة المحظور مع إرجاع معرف وسبب مرفقين بالخطأ
        if ($banned = BanList::where('id_num', $id_num)->first()) {
            $reason = $banned->reason ?? 'غير محدد';

            return back()->withErrors([
                'id_num' => "لا يمكن تحديث بيانات الأسرة لأن رب الأسرة برقم الهوية {$banned->id_num} مرفوض من النظام. سبب الرفض: {$reason}"
            ]);
        }

        // فحص أفراد الأسرة ومع توضيح كل رفض برقم وهوية وسبب
        foreach ($data['persons'] as $member) {
            if (!empty($member['id_num'])) {
                $banned = BanList::where('id_num', $member['id_num'])->first();
                if ($banned) {
                    $reason = $banned->reason ?? 'غير محدد';
                    return back()->withErrors([
                        'persons' => "لا يمكن إضافة/تحديث الفرد برقم الهوية {$banned->id_num} لأنه مرفوض من النظام. سبب الرفض: {$reason}"
                    ]);
                }
            }
        }

        $persons = collect($data['persons'])
            ->map(fn($p) => array_merge($p, ['relative_id' => $id_num]))
            ->push($person);

        $persons->each(function ($p) {
            Person::create($p);
        });

        return redirect()->route('profile');
    }

    /*
     * api
     */
    public function checkId(Request $request)
    {
        if (!session()->has('id_num')) {
            return response()->json(['error' => 'رقم الهوية غير موجود في الجلسة'], 400);
        }

        $id_num = session('id_num');

        $existsInPersons = Person::where('id_num', $id_num)->exists();

        $existsInBanned = false;
        $bannedReason   = null;

        if (! $existsInPersons) {
            $banned = BanList::where('id_num', $id_num)->first();
            if ($banned) {
                $existsInBanned = true;
                $bannedReason   = $banned->reason; // عمود السبب في جدول المحظورين
            }
        }

        return response()->json([
            'exists_in_persons' => $existsInPersons,
            'exists_in_banned'  => $existsInBanned,
            'banned_reason'     => $bannedReason,
        ]);
    }

    public function success(Request $request)
    {
        $person = $request->session()->get('person');
        if (!$person) {
            return redirect()->route('persons.intro');
        }

        return view('successregister', ['passkey' => $person['passkey']]);
    }

    public function updateProfileParent(Request $request)
    {
        // جلب المستخدم الحالي حسب رقم هويته القديمة (لو متوفرة) أو الجديدة
        $user = Person::where('id_num', $request->old_id_num ?? $request->id_num)->first();

        if (!$user) {
            $banned = BanList::where('id_num', $request->id_num)->first();

            if ($banned) {
                return response()->json([
                    'success' => false,
                    'rejected_id' => $banned->id_num,
                    'reason' => $banned->reason ?? 'غير محدد',
                    'message' => "لا يمكن تحديث الملف الشخصي برقم الهوية {$banned->id_num} لأنه مرفوض من النظام. سبب الرفض: " . ($banned->reason ?? 'غير محدد')
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => "رقم الهوية {$request->id_num} غير مسجل مسبقاً وبالتالي لا يمكن تحديثه."
            ], 404);
        }

        // التحقق من تكرار رقم الهوية الجديد
        if ($request->id_num !== $request->old_id_num) {
            $exists = Person::where('id_num', $request->id_num)
                ->where('id', '<>', $user->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => "رقم الهوية {$request->id_num} مسجل مسبقاً لشخص آخر."
                ], 422);
            }
        }

        $banned = BanList::where('id_num', $request->id_num)->first();
        if ($banned) {
            return response()->json([
                'success' => false,
                'rejected_id' => $banned->id_num,
                'reason' => $banned->reason ?? 'غير محدد',
                'message' => "لا يمكن تحديث الملف الشخصي للمستخدم برقم الهوية {$banned->id_num} لأنه مرفوض من النظام. سبب الرفض: " . ($banned->reason ?? 'غير محدد')
            ], 422);
        }

        // استبعاد old_id_num من بيانات التحديث لأنه ليس عمود
        $data = $request->except('old_id_num');

        if (isset($data['area_responsible_id']) && ($data['area_responsible_id'] === 'null' || $data['area_responsible_id'] === '')) {
            $data['area_responsible_id'] = null;
        }

        if (isset($data['phone'])) {
            $cleanPhone = str_replace('-', '', $data['phone']);
            $cleanPhone = ltrim($cleanPhone, '0');
            $data['phone'] = $cleanPhone;
        }

        $updated = $user->update($data);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'تم تحديث الملف الشخصي بنجاح']);
        } else {
            return response()->json(['success' => false, 'message' => 'فشل تحديث البيانات. يرجى المحاولة مرة أخرى.'], 500);
        }
    }

    public function updateProfileChild(Request $request)
    {
        // ✅ استخدام id بدلاً من id_num للبحث عن المستخدم
        $user = Person::find($request->id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "المستخدم غير موجود."
            ], 404);
        }

        $oldId = $user->id_num; // ✅ الرقم القديم من قاعدة البيانات
        $newId = $request->id_num;

        // ✅ التحقق من رقم الهوية الجديد فقط إذا تغير
        if ($oldId !== $newId) {
            // التحقق من وجود رقم الهوية الجديد لشخص آخر
            $exists = Person::where('id_num', $newId)
                ->where('id', '<>', $user->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => "رقم الهوية {$newId} مسجل مسبقاً لشخص آخر."
                ], 422);
            }

            // التحقق من قائمة الحظر
            $banned = BanList::where('id_num', $newId)->first();
            if ($banned) {
                return response()->json([
                    'success' => false,
                    'rejected_id' => $banned->id_num,
                    'reason' => $banned->reason ?? 'غير محدد',
                    'message' => "لا يمكن تحديث رقم الهوية إلى {$banned->id_num} لأنه مرفوض من النظام. سبب الرفض: " . ($banned->reason ?? 'غير محدد')
                ], 422);
            }
        }

        // التحقق من قائمة الحظر للرقم القديم (اختياري)
        $bannedOld = BanList::where('id_num', $oldId)->first();
        if ($bannedOld) {
            return response()->json([
                'success' => false,
                'rejected_id' => $bannedOld->id_num,
                'reason' => $bannedOld->reason ?? 'غير محدد',
                'message' => "لا يمكن تحديث الملف الشخصي لأن رقم الهوية {$bannedOld->id_num} مرفوض من النظام. سبب الرفض: " . ($bannedOld->reason ?? 'غير محدد')
            ], 422);
        }

        // تحديث البيانات
        $data = $request->except(['old_id_num', 'id']);

        $updated = $user->update($data);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'تم تحديث فرد الأسرة بنجاح']);
        } else {
            return response()->json(['success' => false, 'message' => 'فشل تحديث البيانات. يرجى المحاولة مرة أخرى.'], 500);
        }
    }

    public function updatePasskey(Request $request)
    {
        // Retrieve the authenticated user
        $user = Person::where('id_num', $request->id_num)->first();

        // Update new passkey
        $user->update(['passkey' => $request->new_passkey]);

        return response()->json(['success' => true, 'message' => 'تم تحديث كلمة المرور بنجاح.']);
    }

    public function deletePerson($id)
    {
        // تحقق إذا كان الشخص موجودًا في قاعدة البيانات
        $person = Person::find($id);

        if ($person) {
            // تحقق من وجود الشخص الذي يطابق الـ relative_id
            $relative = Person::where('id_num', $person->relative_id)->first();

            // تحقق إذا كان الشخص متزوجًا (social_status = married) والعلاقة هي زوجة أو حالة polygamous
            if ($relative && $relative->social_status == 'married' && ($person->relationship == 'wife' || $relative->social_status == 'polygamous')) {
                // منع الحذف وإرجاع رسالة تنبيهية عبر SweetAlert
                return response()->json([
                    'error' => 'لا يمكن حذف الشخص في هذه الحالة. يرجى تعديل الحالة الاجتماعية أولاً.'
                ], 400);
            }

            // حذف الشخص من قاعدة البيانات
            $person->delete();

            $relative_id = session('person')['id_num'];  // أخذ id_num من الشخص المخزن في الجلسة
            $validatedData['relative_id'] = $relative_id;
            $parentPerson = Person::where('id_num', $relative_id)->first(); // جلب الشخص الأساسي باستخدام id_num

            if ($parentPerson) {
                // إذا كان الشخص الأساسي موجودًا، نقوم بتحديث relatives_count
                $parentPerson->decrement('relatives_count');  // زيادة relatives_count بمقدار 1
                Log::info("Updated relatives_count for parent person with id_num: $relative_id");
            } else {
                // في حالة عدم وجود الشخص الأساسي، يمكن إضافة رسالة تحذير في الـ log
                Log::warning("Parent person with id_num: $relative_id not found.");
            }

            // رد عند النجاح
            return response()->json(['success' => 'تم الحذف بنجاح.']);
        }

        // إذا لم يتم العثور على الشخص
        return response()->json(['error' => 'الشخص غير موجود.'], 404);
    }
}
