<x-layout :title="'المستفيدين من مشروع: ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries', $project]">

    @push('styles')
    <style>
        .date-picker-custom {
            padding: 10px 15px !important;
            border: 2px solid #e3e6f0 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }

        .date-picker-custom:focus {
            border-color: #4e73df !important;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
        }

        .bulk-actions-bar-sticky {
            position: sticky !important;
            top: 0 !important;
            z-index: 1020 !important;
            background: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px !important;
            margin-bottom: 1rem !important;
        }

        .bulk-actions-bar-sticky .card-body {
            padding: 0.75rem 1rem !important;
        }

        .bulk-actions-bar-sticky strong {
            color: #4e73df !important;
        }

        .selected-count-badge {
            background: #f8f9fc !important;
            border: 1px solid #e3e6f0 !important;
            color: #4e73df !important;
            font-size: 1.1rem !important;
            padding: 0.3rem 0.8rem !important;
            border-radius: 5px !important;
            font-weight: bold;
        }
    </style>
    @endpush

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>عذراً، حدثت بعض الأخطاء:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning">
            <strong>أخطاء الاستيراد:</strong>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- البحث والفلتر --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.projects.beneficiaries', $project) }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="search"><i class="fas fa-search"></i> بحث برقم الهوية:</label>
                                <input type="text"
                                       name="search"
                                       id="search"
                                       class="form-control"
                                       placeholder="ابحث برقم الهوية..."
                                       value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="status"><i class="fas fa-filter"></i> الحالة:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="مستلم" {{ request('status') === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                    <option value="غير مستلم" {{ request('status') === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="date_from"><i class="fas fa-calendar"></i> التاريخ من:</label>
                                <input type="date"
                                       name="date_from"
                                       id="date_from"
                                       class="form-control date-picker-custom"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="date_to"><i class="fas fa-calendar"></i> التاريخ إلى:</label>
                                <input type="date"
                                       name="date_to"
                                       id="date_to"
                                       class="form-control date-picker-custom"
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>

                                @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                    <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('dashboard.projects.beneficiaries.bulk-actions', $project) }}" method="POST" id="bulk-actions-form">
        @csrf
        {{-- Hidden inputs - سيتم ملؤها عبر JavaScript فقط --}}
        <div id="bulk-hidden-fields"></div>

        {{-- شريط الإجراءات الجماعية --}}
        <div class="card d-none bulk-actions-bar-sticky" id="bulk-actions-bar">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-auto border-left ml-2">
                        <strong>المحددين: </strong>
                        <span class="selected-count-badge" id="selected-count">0</span>
                    </div>
                    <div class="col-md">
                        <button type="button" class="btn btn-outline-primary btn-sm open-bulk-modal mr-2">
                            <i class="fas fa-edit"></i> تعديل حالة المحددين
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm bulk-delete-btn">
                            <i class="fas fa-trash"></i> حذف المحددين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @component('dashboard::components.table-box')
            @slot('title')
                المستفيدين ({{ $beneficiaries->total() }})
            @endslot

            @slot('tools')
                <a href="{{ route('dashboard.projects.show', $project) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> العودة للمشروع
                </a>
            @endslot

            <thead>
                <tr>
                    <th colspan="100">
                        <div class="d-flex align-items-center">
                            <div class="bulk-actions-group mr-3">
                                {{-- تم نقل الإجراءات الجماعية للشريط الملتصق العلوي --}}
                            </div>

                            <div class="ml-auto">
                                <a href="{{ route('dashboard.projects.beneficiaries.filter-areas', $project) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-filter"></i> ترشيح حسب المناطق
                                </a>
                                <a href="{{ route('dashboard.projects.beneficiaries.import', $project) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-import"></i> استيراد من Excel
                                </a>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th style="width: 3%;"><x-check-all></x-check-all></th>
                    <th style="width: 3%;">#</th>
                    <th style="width: 9%;">رقم الهوية</th>
                    <th style="width: 17%;">الاسم الرباعي</th>
                    <th style="width: 8%;">رقم الجوال</th>
                    <th style="width: 10%;">المنطقة</th>
                    <th style="width: 10%;">المخزن الفرعي</th>
                    <th style="width: 5%;">الكمية</th>
                    <th style="width: 8%;">الحالة</th>
                    <th style="width: 10%;">تاريخ التسليم</th>
                    <th style="width: 12%;">الملاحظات</th>
                    <th style="width: 8%;">...</th>
                </tr>
            </thead>
            <tbody>
            @forelse($beneficiaries as $index => $beneficiary)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="items[]" value="{{ $beneficiary->id }}" class="item-checkbox" data-check-all-item>
                    </td>
                    <td class="text-center">{{ $beneficiaries->firstItem() + $index }}</td>
                    <td><strong>{{ $beneficiary->id_num }}</strong></td>
                    <td>
                        <a href="{{ route('dashboard.people.show', $beneficiary) }}" class="text-decoration-none">
                            {{ $beneficiary->first_name }} {{ $beneficiary->father_name }} {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                        </a>
                    </td>
                    <td>{{ $beneficiary->phone ?? '-' }}</td>
                    <td><small class="text-muted">{{ $beneficiary->neighborhood ?? $beneficiary->current_city ?? '-' }}</small></td>
                    <td>
                        @if($beneficiary->pivot->sub_warehouse_id && isset($subWarehouses[$beneficiary->pivot->sub_warehouse_id]))
                            <span class="badge badge-info">
                                <i class="fas fa-warehouse"></i>
                                {{ $subWarehouses[$beneficiary->pivot->sub_warehouse_id]->name }}
                            </span>
                        @else
                            <span class="text-muted">{{ $beneficiary->pivot->sub_warehouse_id ? 'محذوف' : 'غير محدد' }}</span>
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $beneficiary->pivot->quantity ?? 1 }}</strong></td>
                    <td>
                        @if($beneficiary->pivot->status === 'مستلم')
                            <span class="badge badge-success">مستلم</span>
                        @else
                            <span class="badge badge-warning">غير مستلم</span>
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->pivot->delivery_date)
                            <small><i class="fas fa-calendar-check text-success"></i> {{ \Carbon\Carbon::parse($beneficiary->pivot->delivery_date)->format('Y-m-d') }}</small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td><small>{{ $beneficiary->pivot->notes ?? '-' }}</small></td>
                    <td>
                        <a href="{{ route('dashboard.people.show', $beneficiary) }}" class="btn btn-sm btn-outline-dark" title="عرض الملف الشخصي">
                            <i class="fas fa-fw fa-eye"></i>
                        </a>

                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-toggle="modal"
                                data-target="#statusModal{{ $beneficiary->id }}"
                                title="تعديل الحالة">
                            <i class="fas fa-fw fa-edit"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-danger row-delete-btn"
                                data-action="{{ route('dashboard.projects.beneficiaries.destroy', [$project, $beneficiary]) }}"
                                onclick="if(confirm('هل أنت متأكد؟')) {
                                    $('#row-delete-form').attr('action', $(this).data('action')).submit();
                                }"
                                title="حذف من المشروع">
                            <i class="fas fa-fw fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-4">
                        @if(request('search') || request('status') || request('date_from') || request('date_to'))
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد نتائج للبحث</p>
                            <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء البحث
                            </a>
                        @else
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا يوجد مستفيدين</p>
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>

            @if($beneficiaries->hasPages())
                @slot('footer')
                    {{ $beneficiaries->links() }}
                @endslot
            @endif
        @endcomponent
    </form>

    {{-- نماذج مخفية --}}
    <form id="row-delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Modal التحديث الجماعي --}}
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i>
                        تحديث حالة المحددين (<span id="bulk-selected-count-label">0</span> مستفيدين)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>حالة الاستلام <span class="text-danger">*</span></label>
                        <select id="bulk_modal_status" class="form-control" required>
                            <option value="غير مستلم">غير مستلم</option>
                            <option value="مستلم">مستلم</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الكمية <span class="text-danger">*</span></label>
                        <input type="number" id="bulk_modal_quantity" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>تاريخ التسليم</label>
                        <input type="date" id="bulk_modal_delivery_date" class="form-control date-picker-custom" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>الملاحظات</label>
                        <textarea id="bulk_modal_notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                    <button type="button" id="submit-bulk-modal" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التغييرات
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal لكل مستفيد --}}
    @foreach($beneficiaries as $beneficiary)
        <div class="modal fade" id="statusModal{{ $beneficiary->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('dashboard.projects.beneficiaries.update-status', [$project, $beneficiary]) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-user-edit"></i>
                                تحديث حالة: {{ $beneficiary->first_name }} {{ $beneficiary->family_name }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>حالة الاستلام <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="غير مستلم" {{ $beneficiary->pivot->status === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                    <option value="مستلم" {{ $beneficiary->pivot->status === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الكمية <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" value="{{ $beneficiary->pivot->quantity ?? 1 }}" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>تاريخ التسليم</label>
                                <input type="date" name="delivery_date" class="form-control date-picker-custom" value="{{ $beneficiary->pivot->delivery_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label>الملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $beneficiary->pivot->notes }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    @push('scripts')
    <script>
    $(document).ready(function() {
        console.log('✅ Beneficiaries dynamic script loaded and positioned inside Layout');

        // 1. Focus search field
        $('#search').focus();

        // 2. Visibility and count of bulk actions bar
        function updateBulkUI() {
            const selectedCheckboxes = $('.item-checkbox:checked');
            const count = selectedCheckboxes.length;
            
            console.log('🔄 UI Update - Selected items:', count);

            if (count > 0) {
                $('#bulk-actions-bar').removeClass('d-none').show();
                $('#selected-count, #bulk-selected-count-label').text(count);
            } else {
                $('#bulk-actions-bar').hide(0, function() {
                    $(this).addClass('d-none');
                });
            }
        }

        // Update on any checkbox change or any click in the table header (Select All)
        $(document).on('change', '.item-checkbox, #bulk-actions-form input[type="checkbox"]', function() {
            setTimeout(updateBulkUI, 50);
            setTimeout(updateBulkUI, 300);
        });

        // Extra listener for the "Select All" master checkbox specifically
        $(document).on('click', 'input[data-children], th input[type="checkbox"]', function() {
            console.log('📢 Master checkbox clicked');
            setTimeout(updateBulkUI, 50);
            setTimeout(updateBulkUI, 200);
            setTimeout(updateBulkUI, 500);
        });

        // 3. Open Bulk Status Modal
        $(document).on('click', '.open-bulk-modal', function(e) {
            e.preventDefault();
            const count = $('.item-checkbox:checked').length;
            
            if (count === 0) {
                alert('⚠️ الرجاء تحديد مستفيدين أولاً');
                return;
            }

            console.log('🚀 Opening bulk modal for', count, 'items');
            $('#bulk-selected-count-label').text(count);
            $('#bulkStatusModal').modal('show');
        });

        // 4. Submit Bulk Status (Update)
        $(document).on('click', '#submit-bulk-modal', function(e) {
            e.preventDefault();
            
            const selectedItems = $('.item-checkbox:checked');
            const count = selectedItems.length;

            if (count === 0) {
                alert('⚠️ لم يتم تحديد عناصر');
                return;
            }

            const data = {
                action: 'update_status',
                status: $('#bulk_modal_status').val(),
                quantity: $('#bulk_modal_quantity').val(),
                delivery_date: $('#bulk_modal_delivery_date').val(),
                notes: $('#bulk_modal_notes').val()
            };

            if (!confirm(`هل أنت متأكد من تحديث ${count} مستفيدين؟`)) return;

            console.log('📤 Submitting update:', data);

            // Fill hidden fields in the main form
            let fieldsHtml = '';
            for (const [key, value] of Object.entries(data)) {
                fieldsHtml += `<input type="hidden" name="${key}" value="${value}">`;
            }
            $('#bulk-hidden-fields').html(fieldsHtml);

            $('#bulkStatusModal').modal('hide');
            setTimeout(() => { 
                console.log('Submitting Form Now...');
                $('#bulk-actions-form').submit(); 
            }, 300);
        });

        // 5. Bulk Delete
        $(document).on('click', '.bulk-delete-btn', function(e) {
            e.preventDefault();
            const count = $('.item-checkbox:checked').length;

            if (count === 0) {
                alert('⚠️ الرجاء تحديد مستفيدين أولاً');
                return;
            }

            if (confirm(`⚠️ هل أنت متأكد من حذف ${count} مستفيدين؟ لا يمكن التراجع!`)) {
                console.log('🗑️ Submitting bulk delete');
                $('#bulk-hidden-fields').html('<input type="hidden" name="action" value="delete">');
                $('#bulk-actions-form').submit();
            }
        });

        // Initial run
        updateBulkUI();
    });
    </script>
    @endpush
</x-layout>
