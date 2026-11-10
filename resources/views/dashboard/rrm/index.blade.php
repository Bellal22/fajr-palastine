<x-layout :title="isset($currentCustomProject) && $currentCustomProject ? ($currentCustomProject->name . ' - كشوفات وتوزيعات الطرود') : 'المشاريع المخصصة - كشوفات وتوزيعات الطرود'" :breadcrumbs="['dashboard.rrm.index']">

    {{-- شريط تبويب المشاريع المخصصة --}}
    @if(isset($customProjects) && $customProjects->count() > 0)
        <div class="card card-outline card-primary mb-3 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center w-100" style="gap: 10px;">
                    <div class="d-flex flex-wrap align-items-center" style="gap: 6px;">
                        <span class="font-weight-bold text-dark ml-2">
                            <i class="fas fa-layer-group text-primary ml-1"></i> المشروع المخصص:
                        </span>
                        <a href="{{ route('dashboard.rrm.index') }}"
                           class="btn btn-sm {{ empty($currentCustomProject) ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                            الكل ({{ \App\Models\RrmBatch::count() }})
                        </a>
                        @foreach($customProjects as $cpItem)
                            <a href="{{ route('dashboard.rrm.index', ['custom_project_id' => $cpItem->id]) }}"
                               class="btn btn-sm {{ (isset($currentCustomProject) && $currentCustomProject && $currentCustomProject->id === $cpItem->id) ? 'btn-' . ($cpItem->color ?: 'primary') . ' font-weight-bold' : 'btn-outline-secondary' }}">
                                <i class="{{ $cpItem->icon ?: 'fas fa-first-aid' }} ml-1"></i>
                                {{ $cpItem->name }}
                            </a>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center" style="gap: 6px;">
                        @if(isset($currentCustomProject) && $currentCustomProject)
                            <a href="{{ route('dashboard.custom_projects.edit', $currentCustomProject) }}"
                               class="btn btn-sm btn-outline-info font-weight-bold">
                                <i class="fas fa-sliders-h ml-1"></i> تخصيص طريقة التعامل مع ({{ $currentCustomProject->name }})
                            </a>
                        @endif
                        <a href="{{ route('dashboard.custom_projects.index') }}"
                           class="btn btn-sm btn-default border font-weight-bold">
                            <i class="fas fa-cogs text-primary ml-1"></i> إدارة المشاريع المخصصة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- AdminLTE Small Boxes (Statistics) --}}
    <div class="row">
        <div class="col-lg-4 col-sm-6 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalBatches) }}</h3>
                    <p>إجمالي الكشوفات {{ isset($currentCustomProject) && $currentCustomProject ? '(' . $currentCustomProject->name . ')' : '' }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="#batchesTableCard" class="small-box-footer">
                    عرض جميع الكشوفات <i class="fas fa-arrow-circle-down mr-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalBeneficiaries) }}</h3>
                    <p>المستفيدين المسجلين في الكشوفات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <span class="small-box-footer">
                    مستفيد مسجل <i class="fas fa-check-circle mr-1"></i>
                </span>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($totalParcels) }}</h3>
                    <p>إجمالي الطرود المسلمة</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <a href="{{ route('dashboard.projects.deliveries', array_filter(['type' => 'rrm', 'custom_project_id' => $currentCustomProject->id ?? null])) }}" class="small-box-footer text-dark font-weight-bold">
                    تنفيذ تسليم كشف جديد <i class="fas fa-plus-circle mr-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- AdminLTE Filter Card --}}
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-filter text-primary mr-1"></i> فلترة وبحث كشوفات وتوزيعات {{ isset($currentCustomProject) && $currentCustomProject ? $currentCustomProject->name : 'المشاريع المخصصة' }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('dashboard.projects.deliveries', array_filter(['type' => 'rrm', 'custom_project_id' => $currentCustomProject->id ?? null])) }}" class="btn btn-success btn-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> تسليم كشف {{ isset($currentCustomProject) && $currentCustomProject ? $currentCustomProject->code_prefix : 'RRM' }} جديد
                </a>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.rrm.index') }}">
                @if(isset($currentCustomProject) && $currentCustomProject)
                    <input type="hidden" name="custom_project_id" value="{{ $currentCustomProject->id }}">
                @endif
                <div class="row align-items-end">
                    {{-- Search Input --}}
                    <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark">
                            <i class="fas fa-search text-muted mr-1"></i> البحث بالاسم أو الرمز:
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="ابحث باسم الكشف أو رمز الكشف..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Warehouse Select --}}
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark">
                            <i class="fas fa-warehouse text-muted mr-1"></i> المخزن الفرعي:
                        </label>
                        <select name="sub_warehouse_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- كل المخازن الفرعية --</option>
                            @foreach($warehouses as $whId => $whName)
                                <option value="{{ $whId }}" {{ request('sub_warehouse_id') == $whId ? 'selected' : '' }}>{{ $whName }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-lg-2 col-md-3 col-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark">
                            <i class="fas fa-calendar-alt text-muted mr-1"></i> من تاريخ:
                        </label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-lg-2 col-md-3 col-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark">
                            <i class="fas fa-calendar-alt text-muted mr-1"></i> إلى تاريخ:
                        </label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-lg-1 col-md-6">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary" title="تطبيق التصفية">
                                <i class="fas fa-filter"></i>
                            </button>
                            @if(request()->hasAny(['search', 'sub_warehouse_id', 'date_from', 'date_to']))
                                <a href="{{ route('dashboard.rrm.index') }}" class="btn btn-default" title="إلغاء الفلترة">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- AdminLTE Table Card --}}
    <div class="card card-primary card-outline" id="batchesTableCard">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-clipboard-list text-primary mr-1"></i> قائمة كشوفات وتوزيعات {{ isset($currentCustomProject) && $currentCustomProject ? $currentCustomProject->name : 'المشاريع المخصصة' }} المسجلة
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary font-weight-bold">{{ number_format($batches->total()) }} كشف</span>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                <thead class="thead-light">
                    <tr>
                        <th width="4%" class="text-center font-weight-bold">#</th>
                        <th width="28%" class="font-weight-bold">
                            <i class="fas fa-file-alt text-muted ml-1"></i> اسم الكشف / التوزيعة
                        </th>
                        <th width="12%" class="text-center font-weight-bold">
                            <i class="fas fa-barcode text-muted ml-1"></i> رمز الكشف
                        </th>
                        <th width="12%" class="text-center font-weight-bold">
                            <i class="fas fa-calendar-alt text-muted ml-1"></i> تاريخ التسليم
                        </th>
                        <th width="13%" class="font-weight-bold">
                            <i class="fas fa-warehouse text-muted ml-1"></i> المخزن الفرعي
                        </th>
                        <th width="9%" class="text-center font-weight-bold">
                            <i class="fas fa-users text-muted ml-1"></i> المستفيدين
                        </th>
                        <th width="9%" class="text-center font-weight-bold">
                            <i class="fas fa-boxes text-muted ml-1"></i> إجمالي الطرود
                        </th>
                        <th width="11%" class="text-center font-weight-bold">
                            <i class="fas fa-user-check text-muted ml-1"></i> منفذ الكشف
                        </th>
                        <th width="12%" class="text-center font-weight-bold">
                            <i class="fas fa-cog text-muted ml-1"></i> الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $index => $batch)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">
                                {{ $batches->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <a href="{{ route('dashboard.rrm.show', $batch->id) }}" class="font-weight-bold text-primary d-inline-flex align-items-center" style="font-size: 15px; gap: 6px;">
                                        <i class="{{ optional($batch->customProject)->icon ?: 'fas fa-first-aid' }} text-{{ optional($batch->customProject)->color ?: 'danger' }}"></i>
                                        <span class="batch-name-{{ $batch->id }}" dir="auto">{{ $batch->name }}</span>
                                        @if($batch->customProject)
                                            <span class="badge badge-{{ $batch->customProject->color ?: 'warning' }} font-weight-normal mr-1" style="font-size: 10px;">
                                                {{ $batch->customProject->name }}
                                            </span>
                                            @if($batch->customProject->is_cash)
                                                <span class="badge badge-success font-weight-bold mr-1" style="font-size: 10px;">
                                                    <i class="fas fa-money-bill-wave ml-1"></i>
                                                    نقدي: {{ $batch->customProject->formatted_cash_value }}
                                                </span>
                                            @endif
                                        @endif
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-edit-batch ml-2" 
                                            data-id="{{ $batch->id }}" 
                                            data-name="{{ $batch->name }}"
                                            data-notes="{{ $batch->notes }}"
                                            title="تعديل اسم وملاحظات الكشف">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>

                                {{-- Display parcel bundle contents --}}
                                @php
                                    $projects = $batch->projects;
                                    $quantities = $batch->project_quantities ?? [];
                                @endphp
                                @if($projects->count() > 0)
                                    <div class="mt-1 pt-1 border-top d-flex flex-wrap align-items-center" style="gap: 4px;">
                                        <small class="text-muted d-inline-flex align-items-center ml-2">
                                            <i class="fas fa-boxes text-secondary ml-1"></i> تشكيلة الحزمة:
                                        </small>
                                        @foreach($projects as $p)
                                            <span class="badge badge-light border text-dark font-weight-normal d-inline-flex align-items-center px-2 py-1" style="font-size: 11px; gap: 4px;">
                                                <i class="fas fa-box text-primary" style="font-size: 9px;"></i>
                                                <span>{{ $p->name }}</span>
                                                @if(isset($quantities[$p->id]))
                                                    <strong class="text-primary">(×{{ $quantities[$p->id] }})</strong>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary px-2 py-1 font-monospace d-inline-flex align-items-center justify-content-center" style="letter-spacing: 0.5px; gap: 5px;" dir="ltr">
                                    <i class="fas fa-barcode"></i>
                                    <span>{{ $batch->code }}</span>
                                </span>
                            </td>
                            <td class="text-center font-weight-bold">
                                @if($batch->delivery_date)
                                    <span class="text-dark d-inline-flex align-items-center justify-content-center" style="gap: 5px;" dir="ltr">
                                        <i class="far fa-calendar-alt text-info"></i>
                                        <span>{{ $batch->delivery_date->format('Y-m-d') }}</span>
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($batch->subWarehouse)
                                    <span class="text-dark font-weight-bold d-inline-flex align-items-center" style="gap: 5px;">
                                        <i class="fas fa-warehouse text-secondary"></i>
                                        <span>{{ $batch->subWarehouse->name }}</span>
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info px-2 py-1 font-weight-bold d-inline-flex align-items-center justify-content-center" style="font-size: 13px; gap: 5px;" title="عدد المستفيدين">
                                    <i class="fas fa-users"></i>
                                    <span>{{ number_format($batch->beneficiaries_count) }}</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success px-2 py-1 font-weight-bold d-inline-flex align-items-center justify-content-center" style="font-size: 13px; gap: 5px;" title="إجمالي الطرود">
                                    <i class="fas fa-boxes"></i>
                                    <span>{{ number_format($batch->total_parcels) }}</span>
                                </span>
                            </td>
                            <td class="text-center small text-muted font-weight-bold">
                                <span class="d-inline-flex align-items-center justify-content-center" style="gap: 5px;" dir="auto">
                                    <i class="far fa-user text-muted"></i>
                                    <span>{{ $batch->user ? $batch->user->name : '-' }}</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                    <a href="{{ route('dashboard.rrm.show', $batch->id) }}" class="btn btn-sm btn-info" title="عرض تفاصيل كشف المستفيدين">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.rrm.export', $batch->id) }}" class="btn btn-sm btn-success" title="تصدير كشف المستفيدين إلى Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                    <a href="{{ route('dashboard.rrm.print', $batch->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="طباعة كشف الاستلام الرسمي">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-batch" data-id="{{ $batch->id }}" data-name="{{ $batch->name }}" title="حذف الكشف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-first-aid fa-4x text-muted mb-3 d-block"></i>
                                <h4 class="font-weight-bold text-dark">لا توجد كشوفات مسجلة لمشروع الـ RRM حتى الآن</h4>
                                <p class="text-muted mb-3">يمكنك البدء بإجراء تسليم طرود واختيار نمط الـ RRM لحفظ أول كشف مستقل.</p>
                                <a href="{{ route('dashboard.projects.deliveries', ['type' => 'rrm']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle mr-1"></i> تنفيذ أول تسليم لمشروع الـ RRM
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="card-footer clearfix bg-white">
                <div class="d-flex justify-content-center">
                    {{ $batches->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- AdminLTE Edit Modal --}}
    <div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-edit mr-1"></i> تعديل بيانات كشف الـ RRM
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="editBatchForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-tag text-muted mr-1"></i> اسم الكشف / التوزيعة <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="modalBatchName" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">
                                <i class="fas fa-comment-alt text-muted mr-1"></i> ملاحظات الكشف:
                            </label>
                            <textarea name="notes" id="modalBatchNotes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية عن هذا الكشف..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="btnSaveBatch">
                            <i class="fas fa-save mr-1"></i> حفظ التعديل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- AdminLTE Delete Modal --}}
    <div class="modal fade" id="deleteBatchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-exclamation-triangle mr-1"></i> تأكيد حذف كشف الـ RRM
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="deleteBatchForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="font-weight-bold mb-3">هل أنت متأكد من رغبتك في حذف كشف: <strong id="deleteBatchName" class="text-danger"></strong>؟</p>
                        
                        <div class="form-check p-3 bg-light rounded border">
                            <input class="form-check-input" type="checkbox" name="revert_deliveries" value="1" id="revertDeliveries">
                            <label class="form-check-label font-weight-bold text-danger mr-3" for="revertDeliveries">
                                التراجع عن التسليم وحذف سجلات الطرود المسلمة لجميع مستفيدي هذا الكشف أيضاً
                            </label>
                            <small class="text-muted d-block mt-1">
                                إذا لم يتم التحديد، سيتم فقط حذف تصنيف الكشف مع بقاء الطرود مسجلة كمستلمة للمستفيدين.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-danger font-weight-bold">
                            <i class="fas fa-trash-alt mr-1"></i> نعم، حذف الكشف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Edit batch modal
            $('.btn-edit-batch').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var notes = $(this).data('notes') || '';

                $('#modalBatchName').val(name);
                $('#modalBatchNotes').val(notes);
                $('#editBatchForm').attr('action', "{{ url('dashboard/rrm') }}/" + id);
                $('#editBatchModal').modal('show');
            });

            $('#editBatchForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var btn = $('#btnSaveBatch');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري الحفظ...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> حفظ التعديل');
                        if (res.success) {
                            $('#editBatchModal').modal('hide');
                            alert('✅ ' + res.message);
                            location.reload();
                        } else {
                            alert('حدث خطأ: ' + res.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> حفظ التعديل');
                        alert('حدث خطأ أثناء الحفظ.');
                    }
                });
            });

            // Delete batch modal
            $('.btn-delete-batch').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#deleteBatchName').text(name);
                $('#deleteBatchForm').attr('action', "{{ url('dashboard/rrm') }}/" + id);
                $('#deleteBatchModal').modal('show');
            });
        });
    </script>
    @endpush

</x-layout>
