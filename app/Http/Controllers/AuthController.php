<?php

namespace App\Http\Controllers;

use App\Enums\Person\PersonCity;
use App\Enums\Person\PersonCurrentCity;
use App\Enums\Person\PersonDamageHousingStatus;
use App\Enums\Person\PersonHousingType;
use App\Enums\Person\PersonNeighborhood;
use App\Enums\Person\PersonRelationship;
use App\Enums\Person\PersonSocialStatus;
use App\Models\AreaResponsible;
use App\Models\Complaint;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_num' => 'required|integer',
            'passkey' => 'required|string',
        ], [
            'id_num.required' => 'رقم الهوية مطلوب.',
            'id_num.integer' => 'رقم الهوية يجب أن يكون أرقاماً فقط.',
            'passkey.required' => 'كلمة المرور مطلوبة.',
        ]);

        $person = Person::where('id_num', $request->id_num)->first();

        if (!$person) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم الهوية غير موجود. الرجاء التحقق والمحاولة مجدداً.'
                ], 404);
            }
            return back()->with('error', 'رقم الهوية غير موجود. الرجاء التحقق والمحاولة مجدداً.');
        }

        // التحقق من أن الشخص ليس فرداً في عائلة (يجب أن يكون relative_id فارغ)
        if (!is_null($person->relative_id)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تسجيل الدخول. رقم الهوية المدخل مُدرج ضمن عائلة. الرجاء استخدام رقم هوية رب الأسرة.'
                ], 403);
            }
            return back()->with('error', 'لا يمكن تسجيل الدخول. رقم الهوية المدخل مُدرج ضمن عائلة. الرجاء استخدام رقم هوية رب الأسرة.');
        }

        if ($person->passkey !== $request->passkey) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة المرور غير صحيحة. الرجاء المحاولة مرة أخرى.'
                ], 401);
            }
            return back()->with('error', 'كلمة المرور غير صحيحة. الرجاء المحاولة مرة أخرى.');
        }

        session(['person' => $person]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('profile')
            ]);
        }

        return redirect()->route('profile');
    }

    public function profile()
    {
        // التحقق من وجود الشخص في الجلسة
        if (!session('person')) {
            return redirect()->route('loginView')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        // استرجاع رقم الهوية من السيشن
        $id_num = session('person')['id_num'];

        // جلب بيانات الشخص مع أفراد الأسرة باستخدام رقم الهوية المخزن في السيشن
        $person = Person::with(['familyMembers', 'areaResponsible'])->where('id_num', $id_num)->first();

        // التأكد من وجود الشخص في قاعدة البيانات
        if (!$person) {
            return redirect()->route('profile')->with('error', 'الشخص غير موجود في قاعدة البيانات.');
        }

        // جلب الشكاوى المرتبطة بهذا الشخص
        $complaints = Complaint::where('id_num', $id_num)->get();

        // جلب القيم المطلوبة
        $social_statuses = collect(PersonSocialStatus::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $relationships = collect(PersonRelationship::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $cities = collect(PersonCity::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $current_cities = collect(PersonCurrentCity::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $neighborhoods = collect(PersonNeighborhood::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $areaResponsibles = AreaResponsible::all();

        $housing_types = collect(PersonHousingType::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        $housing_damage_statuses = collect(PersonDamageHousingStatus::toValues())
            ->mapWithKeys(fn($value) => [$value => __($value)]);

        // عرض الصفحة
        return view('profile', compact(
            'person',
            'complaints',
            'social_statuses',
            'relationships',
            'cities',
            'current_cities',
            'neighborhoods',
            'areaResponsibles',
            'housing_types',
            'housing_damage_statuses'
        ));
    }

    public function showChangePasswordForm()
    {
        // استرجاع البيانات من الـ session
        $passkey = session('passkey');

        return view('change-password-form', compact('passkey'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // استرجاع المستخدم الحالي
        $user = auth()->user(); // هنا نفترض أنك تستخدم الـ session (بدلاً من الجارد)

        // التحقق من أن كلمة المرور القديمة صحيحة
        if ($request->old_password !== $user->passkey) {
            return redirect()->back()->with('error', 'كلمة المرور القديمة غير صحيحة');
        }

        // تحديث كلمة المرور في قاعدة البيانات
        $user->passkey = $request->new_password;
        $user->save();

        // إرجاع النتيجة مع رسالة نجاح
        return redirect()->back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    public function resetPassword(Request $request)
    {
        $idNum = $request->input('id_num');
        $phone = $request->input('phone');

        // البحث عن المستخدم برقم الهوية
        $user = DB::table('persons')->where('id_num', $idNum)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'رقم الهوية غير صحيح.']);
        }

        // إزالة الصفر من بداية رقم الجوال المخزن في قاعدة البيانات (إذا كان موجودًا)
        $storedPhone = ltrim($user->phone, '0');

        // إزالة الصفر من بداية رقم الجوال المدخل
        $phone = ltrim($phone, '0');

        // التحقق من أن رقم الجوال يطابق المخزن
        if ($storedPhone !== $phone) {
            return response()->json(['success' => false, 'message' => 'رقم الجوال غير صحيح.']);
        }

        // إنشاء كلمة مرور جديدة
        $newPassword = '123456789';

        // تحديث كلمة المرور
        DB::table('persons')->where('id_num', $idNum)->update([
            'passkey' => $newPassword
        ]);

        return response()->json(['success' => true, 'new_password' => $newPassword]);
    }

    public function logout()
    {
        session()->forget('person');
        return redirect()->route('loginView');
    }
}