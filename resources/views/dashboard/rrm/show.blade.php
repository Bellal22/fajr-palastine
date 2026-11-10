<x-layout :title="'كشف مستفيدي RRM: ' . $batch->name" :breadcrumbs="['dashboard.rrm.show', $batch]">

    {{-- Batch Info Card (AdminLTE Outline) --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-boxes text-primary mr-1"></i> كشف تسليم مشروع الـ RRM: 
                <span class="batch-title-text text-dark">{{ $batch->name }}</span>
            </h3>
            <div class="card-tools">
                <span class="badge badge-secondary px-2 py-1 mr-2 font-monospace font-weight-bold">
                    <i class="fas fa-tag mr-1"></i>{{ $batch->code }}
                </span>
                <button type="button" class="btn btn-sm btn-outline-primary btn-open-edit-modal font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> تعديل اسم وملاحظات الكشف
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- AdminLTE Info Boxes Row --}}
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">المستفيدين في الكشف</span>
                            <span class="info-box-number">{{ number_format($beneficiaries->count()) }} مستفيد</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">إجمالي الطرود المسلمة</span>
                            <span class="info-box-number">{{ number_format($beneficiaries->sum('total_quantity')) }} طرد</span>
                            @if(optional($batch->customProject)->is_cash && optional($batch->customProject)->cash_amount)
                                @php
                                    $batchCashTotal = $beneficiaries->count() * (float) $batch->customProject->cash_amount;
                                @endphp
                                <small class="text-white font-weight-bold d-block mt-1">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    المبلغ: {{ number_format($batchCashTotal, 2) }} {{ $batch->customProject->currency_name }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-warehouse"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">المخزن الفرعي</span>
                            <span class="info-box-number text-truncate">{{ $batch->subWarehouse ? $batch->subWarehouse->name : '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="info-box bg-secondary">
                        <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">تاريخ التسليم</span>
                            <span class="info-box-number batch-date-text">{{ $batch->delivery_date ? $batch->delivery_date->format('Y-m-d') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delivery user and notes --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 bg-light border rounded mb-3">
                <div class="font-weight-bold">
                    <i class="fas fa-user-check text-success mr-1"></i>
                    القائم بالتسليم: <strong class="text-dark">{{ $batch->user ? $batch->user->name : '-' }}</strong>
                </div>
                <div class="mt-2 mt-md-0 d-inline-flex align-items-center flex-wrap" style="gap: 8px;">
                    <i class="fas fa-comment-dots text-primary mr-1"></i>
                    <span class="font-weight-bold">ملاحظات الكشف:</span>
                    <span class="batch-notes-text badge badge-light border px-2 py-1 text-dark font-weight-bold" style="font-size: 13px;">
                        {{ $batch->notes ?: 'لا توجد ملاحظات مسجلة لهذا الكشف' }}
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-primary ml-1 btn-open-edit-modal" title="تعديل اسم وملاحظات الكشف">
                        <i class="fas fa-edit mr-1"></i> تعديل الملاحظة
                    </button>
                </div>
            </div>

            {{-- Breakdown of parcel bundle --}}
            @if(!empty($parcelsSummary))
                <div class="callout callout-info mb-0">
                    <h6 class="font-weight-bold mb-2 text-dark">
                        <i class="fas fa-layer-group text-info mr-1"></i> تفصيل أصناف وكميات حزمة الـ RRM المسلمة في هذا الكشف (الكمية الإجمالية من الأنواع):
                    </h6>
                    <div class="row">
                        @foreach($parcelsSummary as $pName => $pQty)
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-2 border rounded bg-white d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-dark small text-truncate mr-2" title="{{ $pName }}">
                                        <i class="fas fa-box text-primary mr-1"></i>{{ $pName }}
                                    </span>
                                    <span class="badge badge-primary px-2 py-1 font-weight-bold">{{ number_format($pQty) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Action Toolbar & Search --}}
    <div class="card card-default mb-3">
        <div class="card-body py-3">
            <div class="row align-items-center">
                {{-- Search inside sheet --}}
                <div class="col-md-5 mb-2 mb-md-0">
                    <form method="GET" action="{{ route('dashboard.rrm.show', $batch->id) }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="بحث داخل الكشف برقم الهوية، الاسم، المندوب، الجوال..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search mr-1"></i> بحث
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('dashboard.rrm.show', $batch->id) }}" class="btn btn-default" title="إلغاء البحث">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-7 text-md-left text-right">
                    <a href="{{ route('dashboard.rrm.export', $batch->id) }}" class="btn btn-success btn-sm mr-1 font-weight-bold">
                        <i class="fas fa-file-excel mr-1"></i> تصدير الكشف (Excel)
                    </a>
                    <a href="{{ route('dashboard.rrm.print', $batch->id) }}" target="_blank" class="btn btn-secondary btn-sm mr-1 font-weight-bold">
                        <i class="fas fa-print mr-1"></i> طباعة كشف الاستلام
                    </a>
                    <a href="{{ route('dashboard.projects.deliveries', ['type' => 'rrm']) }}" class="btn btn-primary btn-sm mr-1 font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> تسليم جديد
                    </a>
                    <a href="{{ route('dashboard.rrm.index') }}" class="btn btn-default btn-sm font-weight-bold">
                        <i class="fas fa-arrow-right mr-1"></i> رجوع للكشوفات
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Beneficiaries Table Card --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-users text-primary mr-1"></i> قائمة المستفيدين الذين استلموا ضمن هذا الكشف
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary font-weight-bold">{{ number_format($beneficiaries->count()) }} مستفيد</span>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                <thead class="thead-light">
                    <tr>
                        <th width="4%" class="text-center font-weight-bold">#</th>
                        <th width="12%" class="text-center font-weight-bold">
                            <i class="fas fa-id-card text-muted ml-1"></i> رقم الهوية
                        </th>
                        <th width="18%" class="font-weight-bold">
                            <i class="fas fa-user text-muted ml-1"></i> الاسم رباعي
                        </th>
                        <th width="11%" class="text-center font-weight-bold">
                            <i class="fas fa-phone-alt text-muted ml-1"></i> رقم الجوال
                        </th>
                        <th width="13%" class="font-weight-bold">
                            <i class="fas fa-map-marker-alt text-muted ml-1"></i> المندوب / المنطقة
                        </th>
                        <th width="16%" class="font-weight-bold">
                            <i class="fas fa-boxes text-muted ml-1"></i> الطرود والكميات
                        </th>
                        <th width="6%" class="text-center font-weight-bold">
                            <i class="fas fa-calculator text-muted ml-1"></i> الكمية
                        </th>
                        <th width="6%" class="text-center font-weight-bold">
                            <i class="fas fa-check-circle text-muted ml-1"></i> الحالة
                        </th>
                        <th width="14%" class="text-center font-weight-bold">
                            <i class="fas fa-comment-dots text-primary ml-1"></i> ملاحظة المستفيد
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiaries as $idx => $b)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
                            <td class="text-center font-weight-bold font-monospace">
                                <span class="badge badge-light border text-dark font-weight-bold px-2 py-1">
                                    {{ $b['id_num'] }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($b['profile_url']))
                                    <a href="{{ $b['profile_url'] }}" target="_blank" class="font-weight-bold text-primary d-inline-flex align-items-center" style="gap: 5px;">
                                        <i class="fas fa-user-circle text-muted"></i>
                                        <span>{{ $b['name'] }}</span>
                                    </a>
                                @else
                                    <strong class="text-dark">{{ $b['name'] }}</strong>
                                @endif
                            </td>
                            <td class="text-center font-monospace">
                                @if(!empty($b['phone']) && $b['phone'] !== '-')
                                    <a href="tel:{{ $b['phone'] }}" class="text-dark font-weight-bold text-decoration-none d-inline-flex align-items-center justify-content-center" style="gap: 4px;" dir="ltr">
                                        <i class="fas fa-phone-alt text-success"></i>
                                        <span>{{ $b['phone'] }}</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="small font-weight-bold text-secondary d-inline-flex align-items-center" style="gap: 4px;">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <span>{{ $b['area_name'] }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap align-items-center" style="gap: 4px;">
                                    @foreach($b['parcels'] as $parcel)
                                        <span class="badge badge-light border text-dark font-weight-normal d-inline-flex align-items-center px-2 py-1" style="gap: 4px;">
                                            <i class="fas fa-box text-primary" style="font-size: 9px;"></i>
                                            <span>{{ $parcel['project_name'] }}</span>
                                            <span class="badge badge-primary mr-1">×{{ $parcel['quantity'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                    {{ $b['total_quantity'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($b['status'] === 'مستلم')
                                    <span class="badge badge-success px-2 py-1 d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                        <i class="fas fa-check-circle"></i>
                                        <span>مستلم</span>
                                    </span>
                                @else
                                    <span class="badge badge-warning px-2 py-1">
                                        {{ $b['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 5px;">
                                    <span class="beneficiary-note-display-{{ $b['key'] }} small font-weight-bold {{ !empty($b['notes']) && $b['notes'] !== '-' ? 'text-dark' : 'text-muted' }}" 
                                          style="max-width: 130px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                          title="{{ $b['notes'] ?: 'لا توجد ملاحظة لهذا المستفيد' }}">
                                        {{ $b['notes'] ?: '-' }}
                                    </span>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-info btn-edit-beneficiary-note" 
                                            data-key="{{ $b['key'] }}"
                                            data-row-ids="{{ implode(',', $b['row_ids'] ?? []) }}"
                                            data-name="{{ $b['name'] }}"
                                            data-note="{{ $b['notes'] !== '-' ? $b['notes'] : '' }}"
                                            title="تعديل ملاحظة المستفيد">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 text-secondary d-block"></i>
                                <h6 class="font-weight-bold">لا توجد سجلات تطابق معايير البحث في هذا الكشف</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="6" class="text-right">
                            <i class="fas fa-calculator text-success mr-1"></i> إجمالي مستفيدي هذا الكشف والكميات الإجمالية المسلمة:
                        </td>
                        <td class="text-center">
                            <span class="badge badge-dark p-2" style="font-size: 13px;">
                                {{ number_format($beneficiaries->sum('total_quantity')) }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{ $beneficiaries->count() }} مستفيد
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- AdminLTE Edit Batch Modal --}}
    <div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-edit mr-1"></i> تعديل بيانات كشف الـ RRM
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="editBatchForm" method="POST" action="{{ route('dashboard.rrm.update', $batch->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-tag text-muted mr-1"></i> اسم الكشف / التوزيعة <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="modalBatchName" class="form-control font-weight-bold" value="{{ $batch->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-calendar-alt text-muted mr-1"></i> تاريخ التسليم / التوزيع:
                            </label>
                            <input type="date" name="delivery_date" id="modalBatchDeliveryDate" class="form-control" value="{{ $batch->delivery_date ? $batch->delivery_date->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-comment-alt text-muted mr-1"></i> ملاحظات الكشف:
                            </label>
                            <textarea name="notes" id="modalBatchNotes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية عن هذا الكشف...">{{ $batch->notes }}</textarea>
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

    {{-- Modal to edit beneficiary note --}}
    <div class="modal fade" id="editBeneficiaryNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-comment-dots mr-1"></i> تعديل ملاحظة المستفيد
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="editBeneficiaryNoteForm" method="POST" action="{{ route('dashboard.rrm.beneficiary_note', $batch->id) }}">
                    @csrf
                    <input type="hidden" name="row_ids" id="modalBeneficiaryRowIds">
                    <input type="hidden" id="modalBeneficiaryKey">
                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <i class="fas fa-user mr-1 text-primary"></i>
                            المستفيد: <strong id="modalBeneficiaryName" class="text-dark"></strong>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">
                                <i class="fas fa-pencil-alt text-muted mr-1"></i> ملاحظة المستفيد:
                            </label>
                            <textarea name="note" id="modalBeneficiaryNoteText" class="form-control" rows="3" placeholder="أدخل أي ملاحظة خاصة بهذا المستفيد في هذا الكشف..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info font-weight-bold" id="btnSaveBeneficiaryNote">
                            <i class="fas fa-save mr-1"></i> حفظ ملاحظة المستفيد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Open Edit Modal for Batch
            $('.btn-open-edit-modal').on('click', function() {
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
                            $('.batch-title-text').text(res.name || $('#modalBatchName').val());
                            if (res.delivery_date || $('#modalBatchDeliveryDate').val()) {
                                $('.batch-date-text').text(res.delivery_date || $('#modalBatchDeliveryDate').val());
                            }
                            if ($('#modalBatchNotes').val()) {
                                $('.batch-notes-text').text($('#modalBatchNotes').val());
                            }
                            $('#editBatchModal').modal('hide');
                            alert('✅ ' + res.message);
                        } else {
                            alert('حدث خطأ: ' + res.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> حفظ التعديل');
                        alert('حدث خطأ أثناء حفظ التعديل.');
                    }
                });
            });

            // Open Edit Beneficiary Note Modal
            $('.btn-edit-beneficiary-note').on('click', function() {
                var key = $(this).data('key');
                var name = $(this).data('name');
                var rowIds = $(this).data('row-ids');
                var note = $(this).data('note') || '';

                $('#modalBeneficiaryKey').val(key);
                $('#modalBeneficiaryName').text(name);
                $('#modalBeneficiaryRowIds').val(rowIds);
                $('#modalBeneficiaryNoteText').val(note);
                $('#editBeneficiaryNoteModal').modal('show');
            });

            $('#editBeneficiaryNoteForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var key = $('#modalBeneficiaryKey').val();
                var btn = $('#btnSaveBeneficiaryNote');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري الحفظ...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> حفظ ملاحظة المستفيد');
                        if (res.success) {
                            var updatedNote = res.note;
                            $('.beneficiary-note-display-' + key).text(updatedNote).attr('title', updatedNote !== '-' ? updatedNote : 'لا توجد ملاحظة لهذا المستفيد');
                            if (updatedNote !== '-') {
                                $('.beneficiary-note-display-' + key).removeClass('text-muted').addClass('text-dark');
                            } else {
                                $('.beneficiary-note-display-' + key).removeClass('text-dark').addClass('text-muted');
                            }
                            // Update data-note on the button
                            $('button[data-key="' + key + '"]').data('note', updatedNote !== '-' ? updatedNote : '');

                            $('#editBeneficiaryNoteModal').modal('hide');
                            alert('✅ ' + res.message);
                        } else {
                            alert('حدث خطأ: ' + res.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> حفظ ملاحظة المستفيد');
                        alert('حدث خطأ أثناء حفظ الملاحظة.');
                    }
                });
            });
        });
    </script>
    @endpush

</x-layout>
