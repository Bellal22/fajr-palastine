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
        $id_num = $request->session()->get('id_num');
        if (!$id_num) { // this code should be in middleware
            return redirect()->route('persons.intro');
        }

        $social_statuses = collect(PersonSocialStatus::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $cities = collect(PersonCity::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $current_cities = collect(PersonCurrentCity::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $neighborhoods = collect(PersonNeighborhood::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $housing_types = collect(PersonHousingType::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $housing_damage_statuses = collect(PersonDamageHousingStatus::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        return view(
            'person',
            compact(
                'id_num',
                'social_statuses',
                'cities',
                'current_cities',
                'neighborhoods',
                'housing_types',
                'housing_damage_statuses',
            )
        );
    }

    public function store(StorePersonRequest $request)
    {
        $id_num = $request->session()->get('id_num');
        if (!$id_num) {
            return redirect()->route('persons.intro');
        }

        $data = $request->validated();


        $data['passkey']  = Str::random(8);
        $data['id_num']   = $id_num;
        $data['phone']    =  Str::of($data['phone'])
            ->replace('-', '')
            ->toInteger();


        // $person = Person::create($data);
        $request->session()->put('person', $data);

        return redirect()->route('persons.createFamily');
    }

    public function createFamily(Request $request)
    {
        $id_num = $request->session()->get('id_num');
        if (!$id_num) {
            return redirect()->route('persons.intro');
        }

        $peopleList = session('peopleList', []); // جلب البيانات من الجلسة

        $relationships = collect(PersonRelationship::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        return view('family',
            compact(
                'id_num',
                'relationships',
                'peopleList'
            ));
    }

    public function storeFamily(StoreFamilyRequest $request)
    {
        $id_num = $request->session()->get('id_num');
        if (!$id_num) {
            return redirect()->route('persons.intro');
        }

        $peopleList = session('peopleList', []);
        if (empty($peopleList)) {
            return back()->withErrors(['persons' => 'لا توجد بيانات مخزنة لحفظها.']);
        }

        $data['persons'] = $peopleList;
        $person = $request->session()->get('person');
        $person['relatives_count'] = count($data['persons']) + 1;

        // التعامل مع حالة التعدد
        if ($person['social_status'] === 'polygamous') {
            $wives_count = collect($data['persons'])->where('relationship', 'wife')->count();
            if ($wives_count < 2) {
                return back()->withErrors(['persons' => 'يجب أن يكون لديك زوجتان على الأقل في حالة التعدد.']);
            }
        }

        // حفظ الأشخاص في قاعدة البيانات
        $persons = collect($data['persons'])
        ->map(fn($person) => array_merge($person, ['relative_id' => $id_num]))
            ->push($person);

        $persons->each(function ($person) {
            Person::create($person);
        });

        // تفريغ الجلسة بعد الحفظ
        session()->forget('peopleList');

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
            'id_num'                 => 'required|numeric|digits:9',
            'first_name'             => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'father_name'            => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'grandfather_name'       => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'family_name'            => 'required|string|regex:/^[\p{Arabic} ]+$/u',
            'dob'                     => 'required|date',
            'relationship'            => 'required|string',
            'has_condition'           => 'required|in:0,1',
            'condition_description'  => 'nullable|string|max:500'
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
        // Retrieve the authenticated user
        $user = Person::where('id_num', $request->id_num)->first();
        // Update user profile
        $user->update($request->all());

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