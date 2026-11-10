<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ComplaintResponseTemplateRequest;
use App\Models\ComplaintResponseTemplate;
use Illuminate\Http\Request;

class ComplaintResponseTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $templates = ComplaintResponseTemplate::latest()->paginate();

        return view('dashboard.complaint_response_templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.complaint_response_templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\ComplaintResponseTemplateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ComplaintResponseTemplateRequest $request)
    {
        ComplaintResponseTemplate::create($request->validated());

        if (function_exists('flash')) {
            flash()->success('تم إضافة الرد الجاهز بنجاح');
        }

        return redirect()->route('dashboard.complaint_response_templates.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComplaintResponseTemplate  $complaintResponseTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(ComplaintResponseTemplate $complaintResponseTemplate)
    {
        return view('dashboard.complaint_response_templates.edit', [
            'template' => $complaintResponseTemplate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Dashboard\ComplaintResponseTemplateRequest  $request
     * @param  \App\Models\ComplaintResponseTemplate  $complaintResponseTemplate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComplaintResponseTemplateRequest $request, ComplaintResponseTemplate $complaintResponseTemplate)
    {
        $complaintResponseTemplate->update($request->validated());

        if (function_exists('flash')) {
            flash()->success('تم تحديث الرد الجاهز بنجاح');
        }

        return redirect()->route('dashboard.complaint_response_templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComplaintResponseTemplate  $complaintResponseTemplate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ComplaintResponseTemplate $complaintResponseTemplate)
    {
        $complaintResponseTemplate->delete();

        if (function_exists('flash')) {
            flash()->success('تم حذف الرد الجاهز بنجاح');
        }

        return redirect()->route('dashboard.complaint_response_templates.index');
    }
}
