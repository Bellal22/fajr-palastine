<x-layout :title="'تكليف الموزعين'" :breadcrumbs="['dashboard.mobile_app.nominate']">
    <div class="row">
        <div class="col-md-4">
            <form action="{{ route('dashboard.mobile_app.nominate.store') }}" method="POST">
                @csrf
                @component('dashboard::components.box')
                    @slot('title', 'إضافة تكليفات جديدة')

                    @if(session('import_errors'))
                        <div class="alert alert-danger p-2 mb-3">
                            <ul class="mb-0 small">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="fw-bold">المشروع المستهدف <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control select2" required>
                            <option value="">-- اختر المشروع --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ ($selectedProjectId == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">الموزع المكلف <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control select2" required>
                            <option value="">-- اختر الموزع --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ ($selectedUserId == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">نقطة التوزيع (المخزن الفرعي) <span class="text-danger">*</span></label>
                        <select name="sub_warehouse_id" class="form-control select2" required>
                            <option value="">-- اختر المخزن --</option>
                            @foreach($subWarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('sub_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">أرقام الهويات <span class="text-danger">*</span></label>
                        <textarea name="id_nums" rows="6" class="form-control" placeholder="ضع أرقام الهويات هنا، كل رقم في سطر منفصل..." required>{{ old('id_nums') }}</textarea>
                    </div>

                    @slot('footer')
                        <button type="submit" class="btn btn-primary btn-block py-2 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> حفظ وتكليف الموزع
                        </button>
                    @endslot
                @endcomponent
            </form>
        </div>

        <div class="col-md-8">
            <div class="mb-3 bg-white p-3 border rounded shadow-sm">
                <form action="{{ route('dashboard.mobile_app.nominate') }}" method="GET" class="row">
                    <div class="col-md-5">
                        <select name="project_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">-- عرض حسب المشروع --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="user_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">-- عرض حسب الموزع --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('dashboard.mobile_app.nominate') }}" class="btn btn-default btn-block">تصفير</a>
                    </div>
                </form>
            </div>

            @component('dashboard::components.table-box')
                @slot('title')
                    <i class="fas fa-list-ul"></i> التكليفات الحالية للموزع
                    <span class="badge badge-success badge-pill ml-2">{{ count($currentAssignments) }}</span>
                @endslot

                @slot('tools')
                    @if($selectedProjectId && $selectedUserId && count($currentAssignments) > 0)
                        <a href="{{ route('dashboard.mobile_app.nominate.export', ['project_id' => $selectedProjectId, 'user_id' => $selectedUserId]) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </a>
                    @endif
                @endslot

                <thead>
                <tr class="bg-light">
                    <th>اسم المستفيد</th>
                    <th>رقم الهوية</th>
                    <th>الحالة</th>
                    <th class="text-center" style="width: 80px">إبطال</th>
                </tr>
                </thead>

                <tbody>
                @forelse($currentAssignments as $assignment)
                    <tr class="align-middle">
                        <td>{{ $assignment->first_name }} {{ $assignment->family_name }}</td>
                        <td><code class="text-dark">{{ $assignment->id_num }}</code></td>
                        <td>
                            @php
                                $statusColor = $assignment->status == 'مستلم' ? 'success' : 'warning';
                            @endphp
                            <span class="badge badge-{{ $statusColor }} px-2 py-1">{{ $assignment->status }}</span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('dashboard.mobile_app.nominate.unassign', $assignment->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء تكليف هذا المستفيد؟')">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-search fa-2x text-muted mb-2 d-block"></i>
                            <div class="text-muted">اختر المشروع والموزع لعرض التكليفات الجارية</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            @endcomponent
        </div>
    </div>
</x-layout>
