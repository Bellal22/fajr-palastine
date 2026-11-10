<x-layout title="الموظفون" :breadcrumbs="['dashboard.employees.index']">

    {{-- Header Options --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h5 class="text-dark font-weight-bold mb-0">
            <i class="fas fa-users text-primary mr-1"></i> إدارة الموظفين
        </h5>
        <a href="{{ route('dashboard.employees.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus"></i> إضافة موظف جديد
        </a>
    </div>

    {{-- Import Section --}}
    <div class="mb-3">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-file-upload text-primary"></i> استيراد الموظفين من إكسل
                </h6>
            </div>
            <div class="card-body py-3">
                <form action="{{ route('dashboard.employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="text-muted small mb-1">اختر ملف الإكسل <small class="text-danger">(الهوية يجب أن تكون في العمود الأول والاسم الكامل بالثاني)</small></label>
                                <input type="file" name="excel_file" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-block btn-sm">
                                <i class="fas fa-upload"></i> بدء الاستيراد
                            </button>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <a href="{{ route('dashboard.employees.download-template') }}"
                               class="btn btn-outline-success btn-block btn-sm">
                                <i class="fas fa-file-excel"></i> تحميل ملف النموذج
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="mb-3">
        <form action="{{ route('dashboard.employees.index') }}" method="GET" id="filter-form">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-filter text-primary"></i> فلترة وتصفية الموظفين
                @endslot

                <div class="row">
                    {{-- ===== بحث بالاسم ===== --}}
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-user"></i> الاسم الأول</label>
                        <input type="text" name="first_name" class="form-control form-control-sm" placeholder="الاسم الأول..." value="{{ request('first_name') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-user"></i> اسم الأب</label>
                        <input type="text" name="father_name" class="form-control form-control-sm" placeholder="اسم الأب..." value="{{ request('father_name') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-user"></i> اسم الجد</label>
                        <input type="text" name="grandfather_name" class="form-control form-control-sm" placeholder="اسم الجد..." value="{{ request('grandfather_name') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-users"></i> اسم العائلة</label>
                        <input type="text" name="family_name" class="form-control form-control-sm" placeholder="اسم العائلة..." value="{{ request('family_name') }}">
                    </div>

                    {{-- ===== بحث بالمعلومات الأخرى ===== --}}
                    <div class="col-md-4 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-id-card"></i> رقم الهوية</label>
                        <input type="text" name="id_num" class="form-control form-control-sm" placeholder="رقم الهوية..." value="{{ request('id_num') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-phone"></i> رقم الجوال</label>
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="رقم الجوال..." value="{{ request('phone') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-briefcase"></i> المسمى الوظيفي</label>
                        <input type="text" name="job_title" class="form-control form-control-sm" placeholder="المسمى الوظيفي..." value="{{ request('job_title') }}">
                    </div>
                </div>

                @slot('footer')
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> بحث وتصفية
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ml-2" id="resetFilters">
                        <i class="fas fa-eraser"></i> مسح الفلاتر
                    </button>
                @endslot
            @endcomponent
        </form>
    </div>

    {{-- Main Table --}}
    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-user-tie"></i> قائمة الموظفين
            <span class="badge badge-info badge-pill">{{ count_formatted($employees->total()) }}</span>
        @endslot

        <thead>
            <tr class="bg-light">
                <th><i class="fas fa-id-card"></i> رقم الهوية</th>
                <th><i class="fas fa-user"></i> اسم الموظف</th>
                <th><i class="fas fa-phone"></i> رقم الجوال</th>
                <th><i class="fas fa-briefcase"></i> المسمى الوظيفي</th>
                <th><i class="fas fa-calendar-alt"></i> تاريخ الإضافة</th>
                <th class="text-center" style="width: 150px"><i class="fas fa-cog"></i> الإجراءات</th>
            </tr>
        </thead>

        <tbody>
            @forelse($employees as $employee)
                <tr class="align-middle">
                    {{-- ID Num --}}
                    <td>
                        <a href="{{ route('dashboard.employees.show', $employee->id) }}" class="font-weight-bold text-primary">
                            {{ $employee->id_num }}
                        </a>
                    </td>

                    {{-- Full Name --}}
                    <td>
                        <div class="font-weight-bold">
                            {{ $employee->first_name }} {{ $employee->father_name }}
                            {{ $employee->grandfather_name }} {{ $employee->family_name }}
                        </div>
                    </td>

                    {{-- Phone --}}
                    <td>
                        @if($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="text-decoration-none text-success">
                                <i class="fas fa-phone"></i> {{ $employee->phone }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Job Title --}}
                    <td>
                        <span class="badge badge-secondary p-2">{{ $employee->job_title ?? 'غير محدد' }}</span>
                    </td>

                    {{-- Created At --}}
                    <td>
                        <small class="text-muted">
                            {{ $employee->created_at->format('Y-m-d') }}
                        </small>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <a href="{{ route('dashboard.employees.show', $employee->id) }}" class="btn btn-xs btn-outline-info mr-1" title="عرض">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('dashboard.employees.edit', $employee->id) }}" class="btn btn-xs btn-outline-primary mr-1" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('dashboard.employees.destroy', $employee->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                            <h5>لا يوجد موظفون مضافون</h5>
                            <p class="mb-0">يمكنك إضافة موظف جديد بالضغط على الزر أعلاه</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if($employees->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        عرض {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} من إجمالي {{ $employees->total() }} سجل
                    </div>
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('resetFilters')?.addEventListener('click', function () {
                document.getElementById('filter-form').reset();
                history.pushState({}, document.title, window.location.pathname);
                location.reload();
            });
        });
    </script>
    @endpush

</x-layout>
