<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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
        //
        // التحقق من وجود الشخص في الجلسة
        if (!session('person')) {
            return redirect()->route('persons.intro')->with('error', 'يجب التحقق من رقم الهوية أولاً.');
        }

        return response()->view('complaint');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
            'complaint_title' => ['required', 'string', 'max:150'],
            'complaint_text' => ['required', 'string', 'max:5000'],
        ]);

        // 2. تنظيف البيانات
        $cleanTitle = strip_tags(trim($request->complaint_title));
        $cleanText = strip_tags(trim($request->complaint_text));

        // 3. التخزين
        Complaint::create([
            'id_num' => $request->id_num,
            'complaint_title' => $cleanTitle,
            'complaint_text' => $cleanText,
        ]);

        // 4. الرد (التعديل هنا)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
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
        // تأكد أن لديك ملف في resources/views اسمه successcomplaint.blade.php
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
            'response' => 'required|string|min:10',
            'status' => 'required|in:pending,in_progress,resolved,rejected',
        ], [
            'response.required' => 'الرد على الشكوى مطلوب',
            'response.min' => 'الرد يجب أن يكون 10 أحرف على الأقل',
            'status.required' => 'حالة الشكوى مطلوبة',
        ]);

        $complaint->update([
            'response' => $request->response,
            'status' => $request->status,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        flash()->success('تم الرد على الشكوى بنجاح');

        return redirect()->route('dashboard.complaints.show', $complaint);
    }
}