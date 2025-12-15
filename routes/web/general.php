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
use App\Models\Person;
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

Route::view('/', 'intro')->name('persons.intro');

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
    return view('complaint');  // هذا يشير إلى resources/views/complaint.blade.php
})->name('complaint');


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

    // استخراج جميع أرقام الهوية من القائمة
    $idNumbers = array_column($peopleList, 'id_num');

    // البحث عن أرقام الهوية الموجودة مسبقًا في قاعدة البيانات
    $existingIds = Person::whereIn('id_num', $idNumbers)->pluck('id_num')->toArray();

    if (!empty($existingIds)) {
        return response()->json([
            'error' => 'رقم الهوية التالي مسجل مسبقًا: ' . implode(', ', $existingIds),
            'duplicateIds' => $existingIds,
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

Route::resource('complaints', ComplaintController::class);
Route::post('/complaints/submit', [ComplaintController::class, 'submit'])->name('complaints.submit');

Route::get('/get-family-member-data/{id}', [ProfileController::class, 'getFamilyMemberData']);

Route::post('/update-profile', [PersonController::class, 'updateProfileParent']);

Route::post('/update-family-member', [PersonController::class, 'updateProfileChild']);

Route::post('/add-family-member', [PersonController::class, 'addChild']);

Route::post('/update-passkey', [PersonController::class, 'updatePasskey']);

Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::delete('/person/{id}', [PersonController::class, 'deletePerson']);

Route::post('complaints/{complaint}/respond', [ComplaintController::class, 'respond'])
    ->name('dashboard.complaints.respond');