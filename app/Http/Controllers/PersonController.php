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
use App\Models\Person;
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
            // جلب رقم الهوية من الجلسة
            $id_num = $request->session()->get('id_num');
            if (!$id_num) {
                return response()->json([
                    'success' => false,
                    'error' => 'رقم الهوية غير موجود في الجلسة.'
                ], 400);
            }

            // جلب البيانات والتحقق منها
            $data = $request->validated();
            $data['id_num']   = $id_num;
            $data['phone']    = Str::of($data['phone'])->replace('-', '')->toInteger();
            $data['relationship'] = 'رب الأسرة نفسه'; // تعيين صلة القرابة

            // حفظ البيانات في الجلسة
            $request->session()->put('person', array_merge($request->all(), ['id_num' => $id_num,'passkey' => Str::random(8)]));

            // حفظ البيانات في الجلسة (طريقة واحدة وموحدة)
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
            // dd(session('first_person_data'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error','حدث خطأ');
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
        $id_num = $session->get('id_num');

        if (!$id_num) {
            return redirect()->route('persons.intro');
        }

        $peopleList = session('peopleList', []);
        $gender = session('person')['gender'];
        $socialStatus = session('person')['social_status'];

        $person = $session->get('person');
        $person['relatives_count'] = count($peopleList) + 1;

        // التحقق من شرط التعدد
        if ($person['social_status'] === 'polygamous' && collect($peopleList)->where('relationship', 'wife')->count() < 2) {
            return back()->withErrors(['persons' => 'يجب أن يكون لديك زوجتان على الأقل في حالة التعدد.']);
        }

        // تجهيز البيانات للإدخال في قاعدة البيانات مع الحفاظ على gender
        $persons = collect($peopleList)
            ->map(function ($p) use ($id_num) {
                // تصحيح قيمة area_responsible_id لتكون null إذا كانت فارغة
                if (array_key_exists('area_responsible_id', $p) && $p['area_responsible_id'] === '') {
                    $p['area_responsible_id'] = null;
                }
                return array_merge($p, [
                    'relative_id' => $id_num,
                    'id_num' => $p['id_num'] ?? Session::get('id_num'),
                    'has_condition' => array_key_exists('has_condition', $p) && $p['has_condition'] === '' ? 0 : ($p['has_condition'] ?? 0)
                ]);
            })
            ->push(array_merge($person, [
                // نفس التصحيح على الشخص الأساسي
                'area_responsible_id' => (array_key_exists('area_responsible_id', $person) && $person['area_responsible_id'] === '') ? null : ($person['area_responsible_id'] ?? null),
                'id_num' => $person['id_num'] ?? Session::get('id_num'),
                'has_condition' => array_key_exists('has_condition', $person) && $person['has_condition'] === '' ? 0 : ($person['has_condition'] ?? 0)
            ]))
            ->toArray();

        // إدخال جميع البيانات دفعة واحدة لتقليل عدد الاستعلامات مع تسجيل الأخطاء
        foreach ($persons as $person) {
            try {
                Log::info('Creating person:', $person);
                Person::create($person);
            } catch (\Exception $e) {
                Log::error('Error creating person: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في إدخال بيانات الشخص: ' . $e->getMessage()
                ]);
            }
        }

        // تفريغ الجلسة
        $session->forget('peopleList');

        // إعادة التوجيه إلى صفحة النجاح
        return response()->json([
            'success' => true,
            'redirect' => route('persons.success')
        ]);
    }

    public function addFamily(Request $request)
    {
        Log::info('Received Data:', $request->all()); // تحقق من البيانات المستلمة

        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'id_num'                  => 'required|numeric|digits:9',
            'first_name'              => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'father_name'             => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'grandfather_name'        => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'family_name'             => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'dob'                     => 'required|date',
            'relationship'            => 'required|string',
            'has_condition'           => 'required|in:0,1',
            'condition_description'   => 'nullable|string|max:500'
        ]);

        // إضافة relative_id باستخدام رقم الهوية المخزن في الجلسة
        $relative_id = session('person')['id_num'];  // أخذ id_num من الشخص المخزن في الجلسة
        $validatedData['relative_id'] = $relative_id;

        // إدخال البيانات إلى قاعدة البيانات
        $person = Person::create($validatedData);

        // التأكد من أن الشخص الأساسي (صاحب الجلسة) موجود
        $parentPerson = Person::where('id_num', $relative_id)->first(); // جلب الشخص الأساسي باستخدام id_num

        if ($parentPerson) {
            // إذا كان الشخص الأساسي موجودًا، نقوم بتحديث relatives_count
            $parentPerson->increment('relatives_count');  // زيادة relatives_count بمقدار 1
            Log::info("Updated relatives_count for parent person with id_num: $relative_id");
        } else {
            // في حالة عدم وجود الشخص الأساسي، يمكن إضافة رسالة تحذير في الـ log
            Log::warning("Parent person with id_num: $relative_id not found.");
        }

        return response()->json(['success' => 'تمت إضافة الفرد بنجاح', 'person' => $person]);
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
            return redirect()->route(
                'loginView'
            )->with('error', 'يجب تسجيل الدخول أولاً.');
            return redirect()->route('profile');
        }

        $data    = $request->validated();
        $person  = $request->session()->get('person');
        $person['relatives_count'] = count($data['persons']);
        // if person PersonSocialStatus = polygamous then relatives_count must be greater than 1

        if ($person['social_status'] === 'polygamous' && $person['relatives_count'] < 2) {
            return back()->withErrors(['persons' => 'عدد الاقارب يجب ان يكون اكثر من 1']);
        }

        $persons = collect($data['persons'])
            ->map(fn($person) => array_merge($person, ['relative_id' => $id_num]))
            ->push($person);


        $persons->each(function ($person) {
            Person::create($person);
        });

        return redirect()->route('profile');
    }
    /*
     * api
     */
    public function checkId(Request $request)
    {
        // التأكد من أن الجلسة تحتوي على رقم الهوية
        if (!session()->has('id_num')) {
            return response()->json(['error' => 'رقم الهوية غير موجود في الجلسة'], 400);
        }

        // جلب رقم الهوية من الجلسة
        $id_num = session('id_num');

        // التحقق من وجود الرقم في قاعدة البيانات
        $exists = Person::where('id_num', $id_num)->exists();

        // إرجاع النتيجة بصيغة JSON
        return response()->json(['exists' => $exists]);
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
        $user = Person::where('id_num', $request->id_num)->first();

        $data = $request->all();

        // تحويل النص "null" أو السلسلة الفارغة إلى قيمة null الحقيقية
        if (isset($data['area_responsible_id']) && ($data['area_responsible_id'] === 'null' || $data['area_responsible_id'] === '')) {
            $data['area_responsible_id'] = null;
        }

        $allowedNeighborhoods = [
            'westernLine',
            'alMahatta',
            'alKatiba',
            'alBatanAlSameen',
            'alMaskar',
            'alMashroo',
            'hamidCity',
            'downtown'
        ];

        // حذف إعادة تعيين area_responsible_id إلى null عند حي غير مسموح به
        // if (!in_array($request->neighborhood, $allowedNeighborhoods)) {
        //     $data['area_responsible_id'] = null;
        // }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }


    public function updateProfileChild(Request $request)
    {
        // Retrieve the authenticated user
        $user = Person::where('id_num', $request->id_num)->first();
        // Update user profile
        $user->update($request->all());

        return response()->json(['success' => true, 'message' => 'Family member updated successfully']);
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
