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
            return redirect()->route('loginView')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        return response()->view('complaint');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // جلب رقم الهوية من الجلسة
        $idNum = session('id_num');

        // التحقق من صحة الحقول الأخرى باستثناء id_num
        $validatedData = $request->validate([
            'complaint_title' => 'required|string|max:255',
            'complaint_text'  => 'required|string',
        ]);

        // حذف الحقل id_num من الطلب (لحماية إضافية)
        $request->request->remove('id_num');

        // تخزين البيانات مع رقم الهوية من الجلسة
        Complaint::create([
            'id_num'          => $idNum, // رقم الهوية من الجلسة
            'complaint_title' => $validatedData['complaint_title'], // عنوان الشكوى
            'complaint_text'  => $validatedData['complaint_text'], // نص الشكوى
        ]);

        // إعادة المستخدم مع رسالة نجاح
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
}