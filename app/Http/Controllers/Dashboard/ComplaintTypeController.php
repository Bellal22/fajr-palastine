<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ComplaintType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ComplaintTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = ComplaintType::query()
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->latest()
            ->paginate($request->integer('perPage', 10));

        return view('dashboard.complaint_types.index', compact('types'));
    }

    public function create()
    {
        return view('dashboard.complaint_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'cooldown_days' => 'required|integer|min:0',
            'inactive_response' => 'nullable|string|max:2000',
        ]);

        $type = ComplaintType::create([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
            'cooldown_days' => $request->cooldown_days,
            'inactive_response' => $request->inactive_response,
        ]);

        flash()->success('تم إضافة نوع الشكوى بنجاح');
        return redirect()->route('dashboard.complaint_types.index');
    }

    public function edit(ComplaintType $complaint_type)
    {
        return view('dashboard.complaint_types.edit', compact('complaint_type'));
    }

    public function update(Request $request, ComplaintType $complaint_type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'cooldown_days' => 'required|integer|min:0',
            'inactive_response' => 'nullable|string|max:2000',
        ]);

        $complaint_type->update([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
            'cooldown_days' => $request->cooldown_days,
            'inactive_response' => $request->inactive_response,
        ]);

        flash()->success('تم تحديث نوع الشكوى بنجاح');
        return redirect()->route('dashboard.complaint_types.index');
    }

    public function destroy(ComplaintType $complaint_type)
    {
        $complaint_type->delete();
        flash()->success('تم حذف نوع الشكوى بنجاح');
        return redirect()->route('dashboard.complaint_types.index');
    }
}
