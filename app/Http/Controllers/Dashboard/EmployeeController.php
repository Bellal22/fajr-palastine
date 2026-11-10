<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesTemplateExport;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // --- بحث بالاسم الأول ---
        if ($request->filled('first_name')) {
            $query->where('first_name', 'LIKE', '%' . $request->first_name . '%');
        }

        // --- بحث باسم الأب ---
        if ($request->filled('father_name')) {
            $query->where('father_name', 'LIKE', '%' . $request->father_name . '%');
        }

        // --- بحث باسم الجد ---
        if ($request->filled('grandfather_name')) {
            $query->where('grandfather_name', 'LIKE', '%' . $request->grandfather_name . '%');
        }

        // --- بحث باسم العائلة ---
        if ($request->filled('family_name')) {
            $query->where('family_name', 'LIKE', '%' . $request->family_name . '%');
        }

        // --- بحث برقم الهوية ---
        if ($request->filled('id_num')) {
            $query->where('id_num', 'LIKE', '%' . $request->id_num . '%');
        }

        // --- بحث برقم الجوال ---
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', '%' . $request->phone . '%');
        }

        // --- بحث بالمسمى الوظيفي ---
        if ($request->filled('job_title')) {
            $query->where('job_title', 'LIKE', '%' . $request->job_title . '%');
        }

        $perPage = max(1, min(1000, (int) $request->input('perPage', 20)));
        $employees = $query->latest()->paginate($perPage);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('dashboard.employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'grandfather_name' => 'nullable|string|max:255',
            'family_name' => 'required|string|max:255',
            'id_num' => 'required|string|size:9|unique:employees,id_num',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Employee::create($data);

        flash()->success('تم إضافة الموظف بنجاح');
        return redirect()->route('dashboard.employees.index');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        // جلب سجل الكوبونات/الطرود المستلمة وغير المستلمة للموظف
        $coupons = DB::table('project_beneficiaries')
            ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
            ->leftJoin('sub_warehouses', 'sub_warehouses.id', '=', 'project_beneficiaries.sub_warehouse_id')
            ->select(
                'projects.name as project_name',
                'projects.description as project_description',
                'project_beneficiaries.status',
                'project_beneficiaries.quantity',
                'project_beneficiaries.delivery_date',
                'sub_warehouses.name as warehouse_name',
                'project_beneficiaries.notes',
                'project_beneficiaries.created_at as nomination_date'
            )
            ->where('project_beneficiaries.employee_id', $employee->id)
            ->orderBy('project_beneficiaries.delivery_date', 'desc')
            ->get();

        return view('dashboard.employees.show', compact('employee', 'coupons'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('dashboard.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'grandfather_name' => 'nullable|string|max:255',
            'family_name' => 'required|string|max:255',
            'id_num' => 'required|string|size:9|unique:employees,id_num,' . $id,
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $employee->update($data);

        flash()->success('تم تحديث بيانات الموظف بنجاح');
        return redirect()->route('dashboard.employees.show', $id);
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
        flash()->success('تم حذف الموظف بنجاح');
        return redirect()->route('dashboard.employees.index');
    }

    public function downloadTemplate()
    {
        return Excel::download(new EmployeesTemplateExport, 'employees_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $data = Excel::toArray([], $request->file('excel_file'));
        $sheet = $data[0];

        $imported = 0;
        $skipped = 0;

        foreach ($sheet as $index => $row) {
            if ($index == 0) continue; // تخطي رأس الجدول

            $idNum = trim($row[0] ?? '');
            if (empty($idNum)) continue;
            $idNum = str_pad($idNum, 9, '0', STR_PAD_LEFT);

            // التحقق من وجود الموظف مسبقاً
            $exists = Employee::where('id_num', $idNum)->exists();

            if ($exists) {
                $skipped++;
            } else {
                $phone = $this->formatPhone($row[2] ?? '');
                $nameParts = $this->splitName($row[1] ?? '');
                $gender = trim($row[4] ?? '');
                if ($gender !== 'ذكر' && $gender !== 'أنثى') {
                    $gender = 'ذكر';
                }

                $dob = null;
                $dobRaw = trim($row[5] ?? '');
                if (!empty($dobRaw)) {
                    try {
                        $dob = \Carbon\Carbon::parse($dobRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dob = null;
                    }
                }

                Employee::create(array_merge($nameParts, [
                    'id_num' => $idNum,
                    'phone' => $phone,
                    'job_title' => trim($row[3] ?? ''),
                    'gender' => $gender,
                    'dob' => $dob,
                    'notes' => trim($row[6] ?? '') ?: 'مستورد من إكسل',
                ]));
                $imported++;
            }
        }

        flash()->success("تم استيراد الموظفين بنجاح. المضافين: {$imported}، المتخطى (موجود مسبقاً): {$skipped}");
        return back();
    }

    private function splitName($fullName)
    {
        $fullName = trim($fullName);
        $parts = explode(' ', $fullName);
        return [
            'first_name' => $parts[0] ?? null,
            'father_name' => $parts[1] ?? null,
            'grandfather_name' => $parts[2] ?? null,
            'family_name' => count($parts) > 3 ? implode(' ', array_slice($parts, 3)) : null,
        ];
    }

    private function formatPhone($phone)
    {
        $phone = trim($phone);
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (strlen($phone) == 9 && (str_starts_with($phone, '59') || str_starts_with($phone, '56'))) {
            return $phone;
        }

        return $phone;
    }
}
