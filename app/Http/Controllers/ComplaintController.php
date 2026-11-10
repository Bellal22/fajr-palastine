<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Person;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!session('person')) {
            return redirect()->route('persons.intro')->with('error', 'يجب التحقق من رقم الهوية أولاً.');
        }

        return response()->view('complaint');
    }

    /**
     * التحقق من أهلية رقم الهوية لتقديم الشكوى (مسجل وفي خانيونس أو رفح)
     */
    private function checkPersonEligibility($idNum)
    {
        $idNum = trim((string)$idNum);
        if (strlen($idNum) !== 9 || !is_numeric($idNum)) {
            return [
                'eligible' => false,
                'message' => 'يجب إدخال رقم هوية صحيح مكون من 9 أرقام.'
            ];
        }

        // 1. فحص وجود الشخص في قاعدة البيانات (رب أسرة أو فرد أسرة)
        $person = Person::where('id_num', $idNum)->first();

        if (!$person) {
            return [
                'eligible' => false,
                'message' => 'عذراً، رقم الهوية غير مسجل في المنظومة. استقبال الشكاوى متاح فقط للأشخاص المسجلين عبر الرابط.'
            ];
        }

        // 2. استخراج المدينة الحالية (للشخص نفسه أو لرب الأسرة التابع له)
        $currentCity = trim((string)$person->current_city);
        if (empty($currentCity) && !empty($person->relative_id)) {
            $head = Person::where('id_num', $person->relative_id)->first();
            if ($head) {
                $currentCity = trim((string)$head->current_city);
            }
        }

        // 3. فحص هل المدينة ضمن نطاق خانيونس أو رفح
        $allowedCities = [
            'khanYounis', 'khan_younis', 'خان يونس', 'خانيونس', 'شرق خانيونس', 'eastKhanYounis',
            'rafah', 'رفح', 'مشروع رفح', 'rafahProject'
        ];

        $isValidCity = false;
        if (!empty($currentCity)) {
            foreach ($allowedCities as $allowed) {
                if (mb_strtolower($currentCity) === mb_strtolower($allowed) || mb_strpos($currentCity, $allowed) !== false || mb_strpos($allowed, $currentCity) !== false) {
                    $isValidCity = true;
                    break;
                }
            }
        }

        if (!$isValidCity) {
            $cityText = $currentCity ? " (المدينة الحالية المسجلة: {$currentCity})" : "";
            return [
                'eligible' => false,
                'message' => "عذراً، استقبال الشكاوى متاح فقط للأشخاص المسجلين والذين مدينتهم الحالية خانيونس أو رفح.{$cityText}"
            ];
        }

        $fullName = trim($person->first_name . ' ' . $person->father_name . ' ' . $person->family_name);

        return [
            'eligible' => true,
            'person' => $person,
            'name' => $fullName,
            'city' => $currentCity,
            'message' => 'رقم الهوية ينطبق عليه الشروط ومسجل بنجاح.'
        ];
    }

    /**
     * AJAX endpoint لفحص رقم الهوية مباشرة وإرجاع أنواع الشكاوى
     */
    public function verifyId(Request $request)
    {
        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
        ], [
            'id_num.required' => 'رقم الهوية مطلوب',
            'id_num.numeric' => 'رقم الهوية يجب أن يحتوي على أرقام فقط',
            'id_num.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام',
        ]);

        // 1. فحص الحد اليومي للنظام ككل (200 شكوى/يوم)
        $todayComplaintsCount = Complaint::whereDate('created_at', now()->today())->count();
        if ($todayComplaintsCount >= 200) {
            return response()->json([
                'eligible' => false,
                'daily_limit_reached' => true,
                'message' => 'نعتذر، تم الوصول للحد الأقصى المسموح به من الشكاوى اليومية (200 شكوى). يرجى المحاولة غداً.'
            ]);
        }

        $check = $this->checkPersonEligibility($request->id_num);

        if ($check['eligible']) {
            $types = ComplaintType::select('id', 'name', 'is_active', 'inactive_response', 'cooldown_days')->get();
            if ($types->isEmpty()) {
                ComplaintType::create([
                    'name' => 'شكوى عامة / استفسار',
                    'is_active' => true,
                    'cooldown_days' => 0,
                    'inactive_response' => 'نعتذر، استقبال الشكاوى متوقف حالياً.'
                ]);
                $types = ComplaintType::select('id', 'name', 'is_active', 'inactive_response', 'cooldown_days')->get();
            }
            $check['complaint_types'] = $types;
            $check['today_count'] = $todayComplaintsCount;
        }

        return response()->json($check);
    }

    /**
     * AJAX endpoint لفحص اختيار نوع الشكوى (الحالة والـ Cooldown)
     */
    public function checkTypeEligibility(Request $request)
    {
        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
            'complaint_type_id' => ['required', 'exists:complaint_types,id'],
        ]);

        $idNum = trim($request->id_num);
        $typeId = $request->complaint_type_id;
        $type = ComplaintType::find($typeId);

        if (!$type) {
            return response()->json([
                'eligible' => false,
                'message' => 'نوع الشكوى غير موجود.'
            ]);
        }

        // 1. فحص هل نوع الشكوى مطفي
        if (!$type->is_active) {
            return response()->json([
                'eligible' => false,
                'is_active' => false,
                'message' => $type->inactive_response ?: 'نعتذر، استقبال الشكاوى من هذا النوع غير متاح حالياً.'
            ]);
        }

        // 2. فحص فترة الانتظار الفاصلة للفرد (Cooldown)
        if ($type->cooldown_days > 0) {
            $lastComplaint = Complaint::where('id_num', $idNum)
                ->where('complaint_type_id', $typeId)
                ->latest()
                ->first();

            if ($lastComplaint && $lastComplaint->created_at) {
                $daysPassed = (int)$lastComplaint->created_at->diffInDays(now());
                if ($daysPassed < $type->cooldown_days) {
                    $remainingDays = $type->cooldown_days - $daysPassed;
                    $dateFormatted = $lastComplaint->created_at->format('Y-m-d');
                    return response()->json([
                        'eligible' => false,
                        'is_active' => true,
                        'cooldown_blocked' => true,
                        'message' => "لقد قمت بتقديم شكوى من نوع ({$type->name}) مسبقاً بتاريخ {$dateFormatted}. يجب الانتظار {$remainingDays} يوم/أيام إضافية لتتمكن من تقديم شكوى جديدة من هذا النوع."
                    ]);
                }
            }
        }

        return response()->json([
            'eligible' => true,
            'is_active' => true,
            'message' => 'يمكنك تقديم الشكوى بهذا النوع.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        // 1. فحص الحد اليومي العام (200 شكوى في اليوم)
        $todayComplaintsCount = Complaint::whereDate('created_at', now()->today())->count();
        if ($todayComplaintsCount >= 200) {
            $errorMessage = 'نعتذر، تم الوصول للحد الأقصى المسموح به من الشكاوى اليومية (200 شكوى). يرجى المحاولة غداً.';
            if ($isJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'errors' => ['id_num' => [$errorMessage]]
                ], 422);
            }
            return redirect()->back()->withErrors(['id_num' => $errorMessage])->withInput();
        }

        // إذا لم يتم إرسال نوع الشكوى (مثلاً من الفورم المباشر في بروفايل المواطن)، اختيار النوع الافتراضي الفعال
        if (!$request->filled('complaint_type_id')) {
            $defaultType = ComplaintType::where('is_active', true)->first() ?? ComplaintType::first();
            if (!$defaultType) {
                $defaultType = ComplaintType::create([
                    'name' => 'شكوى عامة / استفسار',
                    'is_active' => true,
                    'cooldown_days' => 0,
                    'inactive_response' => 'نعتذر، استقبال الشكاوى متوقف حالياً.'
                ]);
            }
            $request->merge(['complaint_type_id' => $defaultType->id]);
        }

        // 2. التحقق من البيانات الأولية
        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
            'complaint_type_id' => ['required', 'exists:complaint_types,id'],
            'complaint_title' => ['required', 'string', 'max:150'],
            'complaint_text' => ['required', 'string', 'max:5000'],
        ], [
            'id_num.required' => 'رقم الهوية مطلوب',
            'id_num.numeric' => 'رقم الهوية يجب أن يحتوي على أرقام فقط',
            'id_num.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام',
            'complaint_type_id.required' => 'اختر نوع الشكوى',
            'complaint_type_id.exists' => 'نوع الشكوى غير صحيح',
            'complaint_title.required' => 'عنوان الشكوى مطلوب',
            'complaint_text.required' => 'نص الشكوى مطلوب',
        ]);

        // 3. التحقق من أهلية رقم الهوية (مسجل وفي خانيونس أو رفح)
        $check = $this->checkPersonEligibility($request->id_num);

        if (!$check['eligible']) {
            if ($isJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => $check['message'],
                    'errors' => ['id_num' => [$check['message']]]
                ], 422);
            }
            return redirect()->back()->withErrors(['id_num' => $check['message']])->withInput();
        }

        // 4. التحقق من نوع الشكوى وحالته وفترة الانتظار (Cooldown)
        $type = ComplaintType::find($request->complaint_type_id);
        if (!$type) {
            $errorMessage = 'نوع الشكوى المختار غير صحيح.';
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], 422);
            }
            return redirect()->back()->withErrors(['complaint_type_id' => $errorMessage])->withInput();
        }

        if (!$type->is_active) {
            $errorMessage = $type->inactive_response ?: 'نعتذر، استقبال الشكاوى من هذا النوع متوقف حالياً.';
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], 422);
            }
            return redirect()->back()->withErrors(['complaint_type_id' => $errorMessage])->withInput();
        }

        if ($type->cooldown_days > 0) {
            $idNum = trim($request->id_num);
            $lastComplaint = Complaint::where('id_num', $idNum)
                ->where('complaint_type_id', $type->id)
                ->latest()
                ->first();

            if ($lastComplaint && $lastComplaint->created_at) {
                $daysPassed = (int)$lastComplaint->created_at->diffInDays(now());
                if ($daysPassed < $type->cooldown_days) {
                    $remainingDays = $type->cooldown_days - $daysPassed;
                    $dateFormatted = $lastComplaint->created_at->format('Y-m-d');
                    $errorMessage = "لقد قمت بتقديم شكوى من نوع ({$type->name}) مسبقاً بتاريخ {$dateFormatted}. يجب الانتظار {$remainingDays} يوم/أيام إضافية لتتمكن من تقديم شكوى جديدة من هذا النوع.";
                    if ($isJson) {
                        return response()->json(['status' => 'error', 'message' => $errorMessage], 422);
                    }
                    return redirect()->back()->withErrors(['complaint_type_id' => $errorMessage])->withInput();
                }
            }
        }

        // 5. تنظيف البيانات والتخزين
        $cleanTitle = strip_tags(trim($request->complaint_title));
        $cleanText = strip_tags(trim($request->complaint_text));

        Complaint::create([
            'id_num' => trim($request->id_num),
            'complaint_type_id' => $type->id,
            'complaint_title' => $cleanTitle,
            'complaint_text' => $cleanText,
        ]);

        if ($isJson) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'تم تسجيل الشكوى بنجاح'
            ]);
        }

        return redirect()->back()->with('success', 'تم إرسال الشكوى بنجاح');
    }

    /**
     * عرض صفحة نجاح إرسال الشكوى
     */
    public function successcomplaint()
    {
        return view('successcomplaint');
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        //
    }

    public function respond(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_complaint_title' => 'nullable|string|max:255',
            'admin_complaint_text' => 'nullable|string|max:5000',
            'response_title' => 'nullable|string|max:255',
            'response' => 'required|string|min:2',
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'responded_at' => 'nullable|date',
        ], [
            'response.required' => 'نص الرد / الحل مطلوب',
            'response.min' => 'نص الرد يجب أن يكون حرفين على الأقل',
            'status.required' => 'حالة الشكوى مطلوبة',
        ]);

        $updateData = [
            'admin_complaint_title' => $request->admin_complaint_title,
            'admin_complaint_text' => $request->admin_complaint_text,
            'response_title' => $request->response_title,
            'response' => $request->response,
            'status' => $request->status,
            'responded_at' => $request->filled('responded_at') ? \Carbon\Carbon::parse($request->responded_at) : now(),
            'responded_by' => auth()->id(),
        ];

        $complaint->update($updateData);

        flash()->success('تم حفظ الرد وإجراءات الحل وبيانات الشكوى المخصصة بنجاح');

        return redirect()->route('dashboard.complaints.index', ['tab' => 'pending']);
    }
}