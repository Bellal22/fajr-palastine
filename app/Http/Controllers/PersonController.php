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
use App\Models\Choose;
use App\Models\City;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\Neighborhood;
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
        $request->merge([
            'id_num' => preg_replace('/\D/', '', $request->id_num),
        ]);

        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
        ], [
            'id_num.required'  => 'الرجاء ادخال رقم الهوية',
            'id_num.numeric'   => 'رقم الهوية يجب ان يكون ارقام فقط',
            'id_num.digits'    => 'رقم الهوية يجب ان يكون 9 ارقام',
        ]);

        if (Person::where('id_num', $request->id_num)->exists()) {
             return redirect()->route('loginView')->with('error', 'هذا الشخص موجود بالفعل، يرجى تسجيل الدخول.');
        }

        // store the id_num in session
        $request->session()->put('id_num', $request->id_num);
        return redirect()->route('persons.create');
    }

    public function create(Request $request)
    {
        if (!$request->session()->has('id_num')) {
            return redirect()->route('persons.intro');
        }

        $id_num = $request->session()->get('id_num');

        // Fetch all cities
        $cities = City::pluck('name', 'id');

        // Fetch and group chooses by type
        $chooses = Choose::all()->groupBy('type');

        // Fetch neighborhoods grouped by city Name for JS compatibility
        $neighborhoodsGroupedByCity = City::with('neighborhoods')->get()->mapWithKeys(function ($city) {
            return [$city->name => $city->neighborhoods->pluck('name', 'id')];
        });

        // Fetch responsibles grouped by neighborhood Name
        $responsiblesGroupedByNeighborhood = Neighborhood::with('areaResponsibles')->get()->mapWithKeys(function ($neighborhood) {
            return [$neighborhood->name => $neighborhood->areaResponsibles->pluck('name', 'id')];
        });

        // Fetch blocks grouped by responsible ID
        $blocksGroupedByResponsible = AreaResponsible::with('blocks')->get()->mapWithKeys(function ($responsible) {
            return [$responsible->id => $responsible->blocks->pluck('name', 'id')];
        });

        return view('person', compact(
            'id_num',
            'cities',
            'chooses',
            'neighborhoodsGroupedByCity',
            'responsiblesGroupedByNeighborhood',
            'blocksGroupedByResponsible'
        ));
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

            // تنظيف رقم الجوال وإزالة الأحرف غير الرقمية
            if (isset($data['phone'])) {
                $data['phone'] = Str::of($data['phone'])->replace('-', '')->toInteger();
            }

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
                'phone'                 => $data['phone'] ?? null, // إضافة رقم الجوال
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

        // Fetch and group chooses by type
        $chooses = Choose::all()->groupBy('type');

        $person = $request->session()->get('person');

        return view('family', array_merge($personData, compact('chooses', 'peopleList', 'person')));
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

        // تحويل القيم الفارغة إلى null في بيانات رب الأسرة
        if (isset($person['area_responsible_id']) && $person['area_responsible_id'] === '') {
            $person['area_responsible_id'] = null;
        }
        if (isset($person['block_id']) && $person['block_id'] === '') {
            $person['block_id'] = null;
        }

        // استخلاص الـ passkey لربط الجلسة
        $passkey = $person['passkey'] ?? null;

        // عدد الأقارب لرب الأسرة
        $person['relatives_count'] = count($peopleList) + 1;
        $persons = collect($peopleList)
            ->map(function ($p) use ($id_num, $passkey) {
                // بيانات الفرد (بقائمة الحقول المطلوبة فقط، الباقي null)
                $mergedData = array_merge($p, [
                    'relative_id'           => $id_num,
                    'passkey'               => null, // أفراد العائلة لا يملكون passkey خاص بهم
                    'relatives_count'       => 1,
                    'person_status'         => 'غير فعال',
                    'has_condition'         => array_key_exists('has_condition', $p) && $p['has_condition'] === '' ? 0 : ($p['has_condition'] ?? 0),
                    // تصفير حقول الموقع لعدم الوراثة
                    'city'                  => null,
                    'current_city'          => null,
                    'neighborhood'          => null,
                    'area_responsible_id'   => null,
                    'block_id'              => null,
                    'landmark'              => null,
                    'housing_type'          => null,
                    'housing_damage_status' => null,
                    'employment_status'     => null,
                    'social_status'         => null,
                ]);

                // تنظيف رقم الجوال (للزوجة مثلاً) إن وجد
                if (isset($mergedData['phone'])) {
                    $mergedData['phone'] = ltrim(preg_replace('/[^0-9]/', '', $mergedData['phone']), '0');
                }

                return $this->mapSlugsToNames($mergedData);
            })
            ->push($this->mapSlugsToNames(array_merge($person, [
                'id_num' => $person['id_num'] ?? $id_num,
                'phone'  => isset($person['phone']) ? ltrim(preg_replace('/[^0-9]/', '', $person['phone']), '0') : null,
            ])))
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

        $request->merge([
            'id_num' => preg_replace('/\D/', '', $request->id_num),
        ]);

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
                'condition_description' => 'nullable|string|max:500',
                'phone'                 => ['nullable', 'string', 'regex:/^0?(59|56)[0-9]{7}$/'] // إضافة التحقق من رقم الجوال
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

        // تنظيف رقم الجوال إذا كان موجوداً
        if (isset($validatedData['phone'])) {
            $cleanPhone = str_replace('-', '', $validatedData['phone']);
            $cleanPhone = ltrim($cleanPhone, '0');
            $validatedData['phone'] = $cleanPhone;
        }

        try {
            // إنشاء السجل الجديد
            $person = Person::create($this->mapSlugsToNames($validatedData));

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
            Person::create($this->mapSlugsToNames($p));
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
        $request->merge([
            'id_num' => preg_replace('/\D/', '', $request->id_num),
            'old_id_num' => preg_replace('/\D/', '', $request->old_id_num),
        ]);

        // Clean phone before validation/processing
        if ($request->has('phone')) {
            $request->merge([
                'phone' => ltrim(str_replace('-', '', $request->phone), '0')
            ]);
        }

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

        if ($user->is_frozen && !auth()->user()?->isAdmin()) {
            $frozenFields = [
                'area_responsible_id', 'block_id', 'neighborhood', 'city'
            ];

            foreach ($frozenFields as $field) {
                if ($request->has($field) && $request->input($field) != $user->$field) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تم اعتماد بيانات السكن لهذه العائلة وتجميد التعديل عليها تجنباً لفقدان حقكم في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص'
                    ], 422);
                }
            }
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

        $request->validate([
            'phone' => ['nullable', 'string', 'regex:/^(59|56)[0-9]{7}$/'],
        ], [
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 59 أو 56 ويتكون من 9 أرقام (بدون صفر البداية).'
        ]);

        $data['phone'] = $request->phone;

        if (isset($data['area_responsible_id']) && ($data['area_responsible_id'] === 'null' || $data['area_responsible_id'] === '')) {
            $data['area_responsible_id'] = null;
        }

        if (isset($data['block_id']) && ($data['block_id'] === 'null' || $data['block_id'] === '')) {
            $data['block_id'] = null;
        }

        // Housing fields are already cleaned or unset if frozen
        if ($user->is_frozen && !auth()->user()?->isAdmin()) {
            unset($data['area_responsible_id'], $data['block_id'], $data['neighborhood'], $data['city']);
        }

        try {
            $updated = $user->update($this->mapSlugsToNames($data));

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'تم تحديث الملف الشخصي بنجاح']);
            } else {
                return response()->json(['success' => false, 'message' => 'فشل تحديث البيانات. يرجى المحاولة مرة أخرى.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('❌ Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfileChild(Request $request)
    {
        $request->merge([
            'id_num' => preg_replace('/\D/', '', $request->id_num),
        ]);

        // Clean phone before validation/processing
        if ($request->has('phone')) {
            $request->merge([
                'phone' => ltrim(str_replace('-', '', $request->phone), '0')
            ]);
        }

        // ✅ استخدام id بدلاً من id_num للبحث عن المستخدم
        $user = Person::find($request->id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "المستخدم غير موجود."
            ], 404);
        }

        if ($user->is_frozen && !auth()->user()?->isAdmin()) {
            // Check if restricted fields are being updated
             if ($request->has('area_responsible_id') && $request->input('area_responsible_id') != $user->area_responsible_id) {
                 return response()->json([
                    'success' => false,
                    'message' => 'تم اعتماد بيانات السكن لهذه العائلة وتجميد التعديل عليها تجنباً لفقدان حقكم في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص'
                ], 422);
             }
             if ($request->has('block_id') && $request->input('block_id') != $user->block_id) {
                 return response()->json([
                    'success' => false,
                    'message' => 'تم اعتماد بيانات السكن لهذه العائلة وتجميد التعديل عليها تجنباً لفقدان حقكم في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص'
                ], 422);
             }
             if ($request->has('neighborhood') && $request->input('neighborhood') != $user->neighborhood) {
                 return response()->json([
                    'success' => false,
                    'message' => 'تم اعتماد بيانات السكن لهذه العائلة وتجميد التعديل عليها تجنباً لفقدان حقكم في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص'
                ], 422);
             }
             if ($request->has('city') && $request->input('city') != $user->city) {
                 return response()->json([
                    'success' => false,
                    'message' => 'تم اعتماد بيانات السكن لهذه العائلة وتجميد التعديل عليها تجنباً لفقدان حقكم في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص'
                ], 422);
             }
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

        if (isset($data['phone'])) {
            $data['phone'] = ltrim(str_replace('-', '', $data['phone']), '0');
        }

        // التحقق من صحة البيانات
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone' => ['nullable', 'string', 'regex:/^0?(59|56)[0-9]{7}$/'],
        ], [
             'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 59 أو 56 ويتكون من 9 أرقام.'
        ]);

        if ($validator->fails()) {
             return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $data['phone'] = $request->phone;

        $updated = $user->update($this->mapSlugsToNames($data));

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

    private function mapSlugsToNames(array $data)
    {
        $chooses = Choose::all()->groupBy('type');
        
        $mapping = [
            'employment_status' => isset($chooses['employment_status']) ? $chooses['employment_status']->pluck('name', 'slug')->toArray() : [],
            'social_status' => isset($chooses['social_status']) ? $chooses['social_status']->pluck('name', 'slug')->toArray() : [],
            'housing_type' => isset($chooses['housing_type']) ? $chooses['housing_type']->pluck('name', 'slug')->toArray() : [],
            'housing_damage_status' => isset($chooses['housing_damage_status']) ? $chooses['housing_damage_status']->pluck('name', 'slug')->toArray() : [],
            'gender' => isset($chooses['gender']) ? $chooses['gender']->pluck('name', 'slug')->toArray() : [],
            'relationship' => isset($chooses['relationship']) ? $chooses['relationship']->pluck('name', 'slug')->toArray() : [],
        ];

        foreach ($mapping as $key => $map) {
            if (isset($data[$key])) {
                // Try slug first, then fallback to current value
                $data[$key] = $map[$data[$key]] ?? $data[$key];
            }
        }

        return $data;
    }

    /**
     * AJAX Method لجلب مسؤولي المنطقة حسب الحي السكني
     */
    public function getResponsiblesByNeighborhood(Request $request)
    {
        try {
            $neighborhoodName = trim($request->get('neighborhood_name'));
            Log::info('🔍 AJAX: getResponsiblesByNeighborhood called', ['neighborhood_name' => $neighborhoodName]);

            if (!$neighborhoodName) {
                return response()->json(['responsibles' => []]);
            }

            // البحث عن الحي
            $neighborhood = Neighborhood::where('name', $neighborhoodName)->first();

            if (!$neighborhood) {
                Log::warning('⚠️ AJAX: Neighborhood not found', ['name' => $neighborhoodName]);
                return response()->json([
                    'responsibles' => [],
                    'message' => 'الحي غير موجود'
                ], 404);
            }

            // تبسيط الاستعلام لتجنب أخطاء Ambiguous column
            $responsibles = $neighborhood->areaResponsibles()
                ->get(['area_responsibles.id', 'area_responsibles.name']);

            Log::info('✅ AJAX: Found responsibles', ['count' => $responsibles->count()]);

            return response()->json([
                'responsibles' => $responsibles,
                'message' => 'تم جلب المسؤولين بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ AJAX Error: getResponsiblesByNeighborhood', [
                'neighborhood_name' => $request->get('neighborhood_name'),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'responsibles' => [],
                'message' => 'حدث خطأ في جلب المسؤولين'
            ], 500);
        }
    }

    /**
     * AJAX Method لجلب المندوبين حسب مسؤول المنطقة
     */
    public function getBlocksByResponsible(Request $request)
    {
        try {
            $responsibleId = $request->get('responsible_id');

            if (!$responsibleId) {
                return response()->json(['blocks' => []]);
            }

            $areaResponsible = AreaResponsible::find($responsibleId);
            if (!$areaResponsible) {
                return response()->json([
                    'blocks' => [],
                    'message' => 'مسؤول المنطقة غير موجود'
                ], 404);
            }

            $blocks = Block::where('area_responsible_id', $responsibleId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'blocks' => $blocks,
                'message' => 'تم جلب المندوبين بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في جلب المندوبين', [
                'responsible_id' => $request->get('responsible_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'blocks' => [],
                'message' => 'حدث خطأ في جلب المندوبين'
            ], 500);
        }
    }
}
