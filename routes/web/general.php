<?php

Route::middleware('dashboard.locales')->group(function () {
    Auth::routes();
});

Route::redirect('/home', '/dashboard');

Route::impersonate();

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Models\Block;
use App\Models\Location;
use App\Models\Person;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'intro')->name('persons.intro')->middleware('check.referer');

Route::post('/set-session', function (Request $request) {
    // التحقق من الرقم المدخل
    $request->validate([
        'id_num' => ['required', 'numeric', 'digits:9'],
    ], [
        'id_num.required' => 'رقم الهوية مطلوب',
        'id_num.numeric' => 'رقم الهوية يجب أن يكون أرقام فقط',
        'id_num.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام',
    ]);

    // تخزين الرقم في الجلسة بشكل صحيح
    $request->session()->put('id_num', $request->id_num);

    // حفظ الجلسة فوراً
    $request->session()->save();

    // إعادة التوجيه إلى صفحة choose
    return view('choose');
});

Route::get('/complaint', function () {
    return view('complaints.choice');
})->name('complaint');

Route::get('/complaint/relief', function () {
    return view('complaint');
})->name('complaint.relief');

Route::get('/complaint/kitchen', function () {
    return view('complaints.kitchen');
})->name('complaint.kitchen');

Route::post('/complaint/kitchen/verify', function (Request $request) {
    $card = \App\Models\KitchenCard::where('barcode', $request->barcode)->first();
    if ($card) {
        return response()->json([
            'success' => true,
            'camp_name' => $card->camp_name,
            'card_number' => $card->card_number,
        ]);
    }
    return response()->json([
        'success' => false,
        'message' => 'عذراً، لم يتم العثور على بطاقة مطابقة للرمز الممسوح.',
    ]);
});

Route::post('/complaint/kitchen/store', function (Request $request) {
    $request->validate([
        'camp_name' => 'required|string',
        'card_number' => 'required|string',
        'complainant_name' => 'required|string|max:255',
        'mobile_number' => 'required|string|max:20',
        'complaint_title' => 'required|string|max:255',
        'complaint_text' => 'required|string|max:5000',
    ]);

    $complaint = \App\Models\KitchenComplaint::create([
        'camp_name' => $request->camp_name,
        'card_number' => $request->card_number,
        'complainant_name' => $request->complainant_name,
        'mobile_number' => $request->mobile_number,
        'complaint_title' => $request->complaint_title,
        'complaint_text' => $request->complaint_text,
        'status' => 'pending',
    ]);

    // Send notification to all Kitchen Monitors
    $monitors = \App\Models\User::where('type', \App\Models\User::KITCHEN_MONITOR_TYPE)->get();
    foreach ($monitors as $monitor) {
        $monitor->notify(new \App\Notifications\NewKitchenComplaintNotification($complaint));
    }

    return response()->json([
        'success' => true,
        'message' => 'تم تسجيل شكواك بنجاح في نظام المطبخ.',
    ]);
});



Route::get('/create', function (Request $request) {
    $id_num = session('id_num', 'Session not found'); // إذا كانت الجلسة غير موجودة سترجع "Session not found"

    // التحقق من صحة رقم الهوية باستخدام معادلة Luhn
    $validator = Validator::make(['id_num' => $id_num], [
        'id_num' => [
            'required',
            'numeric',
            'digits:9',
            function ($attribute, $value, $fail) {
                if (!isValidLuhn($value)) {
                    $fail('رقم الهوية غير صالح .');
                }
            },
        ],
    ], [
        'id_num.required' => 'رقم الهوية مطلوب',
        'id_num.numeric' => 'رقم الهوية يجب أن يحتوي على أرقام فقط',
        'id_num.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    return view('person', compact('id_num'));
});

// دالة للتحقق باستخدام معادلة Luhn
if (!function_exists('isValidLuhn')) {
    function isValidLuhn($number)
    {
        $sum = 0;
        $shouldDouble = false;

        // بدء التحقق من آخر رقم إلى أول رقم
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = $number[$i];

            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }

        return ($sum % 10) === 0; // رقم الهوية صالح إذا كان المجموع يقبل القسمة على 10
    }
}

Route::get('/check-id', [PersonController::class, 'checkId'])->name('check-id');

// Route::get('/check-id/{id_num}', function ($id_num) {
//     $exists = Person::where('id_num', $id_num)->first();
//     return response()->json([
//         'exists' => (bool)$exists,
//         'id_num' => $exists ? $exists->id_num : null
//     ]);
// });

Route::post('/store-people-session', function (Request $request) {
    $peopleList = $request->input('peopleList', []);

    // تنظيف واستخراج جميع أرقام الهوية من القائمة
    foreach ($peopleList as &$member) {
        if (isset($member['id_num'])) {
            $member['id_num'] = preg_replace('/\D/', '', $member['id_num']);
        }
    }
    unset($member);

    $idNumbers = array_values(array_filter(array_column($peopleList, 'id_num')));
    $headIdNum = session('id_num');

    if ($headIdNum && in_array($headIdNum, $idNumbers)) {
        $msg = "رقم الهوية ({$headIdNum}) هو رقم هوية رب الأسرة نفسه ولا يمكن إضافته كفرد تابع في الأسرة.";
        return response()->json([
            'success' => false,
            'message' => $msg,
            'error' => $msg,
            'duplicateIds' => [$headIdNum],
            'peopleList' => $peopleList,
            'redirect' => route('persons.createFamily')
        ], 422);
    }

    // البحث عن أرقام الهوية الموجودة مسبقًا في قاعدة البيانات
    $existingPersons = !empty($idNumbers) ? Person::whereIn('id_num', $idNumbers)->get() : collect();

    if ($existingPersons->isNotEmpty()) {
        $messages = [];
        foreach ($existingPersons as $existing) {
            $fullName = trim("{$existing->first_name} {$existing->family_name}");
            $namePart = $fullName ? " ({$fullName})" : "";
            $regStatus = empty($existing->relative_id) ? 'مسجل لحاله كرب أسرة' : 'مسجل ضمن عائلة';
            $messages[] = "الفرد صاحب رقم الهوية ({$existing->id_num}){$namePart} مسجل مسبقاً لحاله أو ضمن عائلة ({$regStatus}).";
        }
        $errorMessage = implode('<br>', $messages);

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'error' => $errorMessage,
            'duplicateIds' => $existingPersons->pluck('id_num')->toArray(),
            'peopleList' => $peopleList,
            'redirect' => route('persons.createFamily')
        ], 422);
    }

    session()->put('peopleList', $peopleList);

    return response()->json([
        'success' => 'تم تخزين البيانات بنجاح.',
        'postRedirect' => route('persons.storeFamily') // تأكد من استخدام اسم المسار الصحيح
    ]);
});

Route::get('/loginView', function () {
    return view('login');
})->name('loginView');

Route::post('/user/login', [AuthController::class, 'login'])->name('user.login');

Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/', [PersonController::class, 'check'])->name('persons.check');

Route::get('/create', [PersonController::class, 'create'])->name('persons.create');

Route::post('/store', [PersonController::class, 'store'])->name('persons.store');

Route::get('/createFamily', [PersonController::class, 'createFamily'])->name('persons.createFamily');

Route::post('/storeFamily', [PersonController::class, 'storeFamily'])->name('persons.storeFamily');

Route::post('/addFamily', [PersonController::class, 'addFamily'])->name('persons.addFamily');

Route::post('/checkId', [PersonController::class, 'checkId'])->name('persons.checkId');

Route::get('/success',  [PersonController::class, 'success'])->name('persons.success');

Route::post('/complaints/verify-id', [ComplaintController::class, 'verifyId'])->name('complaints.verifyId');
Route::post('/complaints/check-type', [ComplaintController::class, 'checkTypeEligibility'])->name('complaints.checkType');
Route::resource('complaints', ComplaintController::class);

// Route::post('/complaints/submit', [ComplaintController::class, 'submit'])->name('complaints.submit');

Route::get('successcomplaint', [ComplaintController::class, 'successcomplaint'])->name('complaints.successcomplaint');

Route::get('/get-family-member-data/{id}', [ProfileController::class, 'getFamilyMemberData']);

Route::post('/update-profile', [PersonController::class, 'updateProfileParent']);

Route::post('/update-family-member', [PersonController::class, 'updateProfileChild']);

Route::post('/add-family-member', [PersonController::class, 'addChild']);

Route::post('/update-passkey', [PersonController::class, 'updatePasskey']);

Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::delete('/person/{id}', [PersonController::class, 'deletePerson']);

Route::post('complaints/{complaint}/respond', [ComplaintController::class, 'respond'])
    ->name('dashboard.complaints.respond');


Route::get('/map', function () {
    // المناطق النشطة مع علاقتها بمسؤول المنطقة وإحصائياته
    $regions = Region::with(['areaResponsible' => function ($q) {
            $q->withCount(['blocks']);
        }])
        ->where('is_active', true)
        ->get(['id', 'name', 'color', 'boundaries', 'area_responsible_id']);

    // اللوكيشينات النشطة مع علاقتها بالمنطقة والمندوب
    $locations = Location::with(['region.areaResponsible', 'block.areaResponsible'])
        ->where('is_active', true)
        ->get(['id', 'name', 'latitude', 'longitude', 'icon_color', 'address', 'phone', 'region_id', 'block_id']);

    return view('map', compact('regions', 'locations'));
})->name('public.map');

Route::get('/ajax/responsibles-by-neighborhood', [PersonController::class, 'getResponsiblesByNeighborhood'])
    ->name('area-responsibles.by-neighborhood');

Route::get('/ajax/blocks-by-responsible', [PersonController::class, 'getBlocksByResponsible'])
    ->name('blocks.by-responsible');
