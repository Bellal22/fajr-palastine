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
        // تحقق وجود id_num مع الشروط المناسبة
        $request->validate([
            'id_num' => ['required', 'numeric', 'digits:9'],
            'complaint_title' => ['required', 'string', 'max:255'],
            'complaint_text' => ['required', 'string'],
        ]);

        // التخزين في القاعدة
        Complaint::create([
            'id_num' => $request->id_num,
            'complaint_title' => $request->complaint_title,
            'complaint_text' => $request->complaint_text,
        ]);

        return redirect()->back()->with('success', 'تم إرسال الشكوى بنجاح!');
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