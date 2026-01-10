<x-layout :title="'المستفيدين من مشروع: ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries', $project]">

    @push('styles')
    <style>
        .search-filter-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .search-filter-card .card-body {
            padding: 1.5rem 1.25rem;
        }

        .form-label-icon {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label-icon i {
            color: #4e73df;
            font-size: 0.9rem;
        }

        .date-picker-custom {
            padding: 0.625rem 0.875rem !important;
            border: 2px solid #e3e6f0 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            font-size: 0.95rem !important;
        }

        .date-picker-custom:focus {
            border-color: #4e73df !important;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
        }

        .search-textarea {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            resize: none;
        }

        .search-textarea:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .custom-select-filter {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .custom-select-filter:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .btn-filter-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-filter-group .btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .bulk-actions-bar-sticky {
            position: sticky !important;
            top: 0 !important;
            z-index: 1020 !important;
            background: #fff !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12) !important;
            border-radius: 10px !important;
            margin-bottom: 1.25rem !important;
        }

        .bulk-actions-bar-sticky .card-body {
            padding: 1rem 1.25rem !important;
        }

        .selected-count-badge {
            background: #4e73df !important;
            color: white !important;
            font-size: 1.15rem !important;
            padding: 0.4rem 1rem !important;
            border-radius: 8px !important;
            font-weight: bold !important;
        }

        .bulk-actions-bar-sticky .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .bulk-actions-bar-sticky .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.001);
        }

        .badge {
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .btn-sm {
            padding: 0.375rem 0.625rem;
            font-size: 0.85rem;
            border-radius: 6px;
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d3e2;
            margin-bottom: 1.5rem;
        }

        .empty-state p {
            font-size: 1.1rem;
            color: #858796;
            margin-bottom: 1rem;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
    @endpush

    {{-- رسائل الأخطاء --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> عذراً، حدثت بعض الأخطاء:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle"></i> أخطاء الاستيراد:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- البحث والفلتر --}}
    <div class="search-filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.projects.beneficiaries', $project) }}">
                <div class="row">
                    {{-- البحث بأرقام الهوية --}}
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label-icon" for="search">
                            <i class="fas fa-search"></i> بحث بأرقام الهوية
                        </label>
                        <textarea name="search"
                                  id="search"
                                  class="form-control search-textarea"
                                  rows="2"
                                  placeholder="أدخل أرقام الهوية مفصولة بمسافة أو سطر...">{{ request('search') }}</textarea>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle"></i> مثال: 123456789 987654321
                        </small>
                    </div>

                    {{-- الحالة --}}
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label-icon" for="status">
                            <i class="fas fa-toggle-on"></i> الحالة
                        </label>
                        <select name="status" id="status" class="form-control custom-select-filter">
                            <option value="">الكل</option>
                            <option value="مستلم" {{ request('status') === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                            <option value="غير مستلم" {{ request('status') === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                        </select>
                    </div>

                    {{-- من تاريخ --}}
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label-icon" for="date_from">
                            <i class="fas fa-calendar-alt"></i> من تاريخ
                        </label>
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               class="form-control date-picker-custom"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- إلى تاريخ --}}
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label-icon" for="date_to">
                            <i class="fas fa-calendar-check"></i> إلى تاريخ
                        </label>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               class="form-control date-picker-custom"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- عدد العرض --}}
                    <div class="col-lg-1 col-md-6 mb-3">
                        <label class="form-label-icon" for="per_page">
                            <i class="fas fa-list-ol"></i> العرض
                        </label>
                        <select name="per_page" id="per_page" class="form-control custom-select-filter">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ request('per_page') == 50 || !request('per_page') ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                    </div>

                    {{-- أزرار البحث --}}
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label-icon d-block">&nbsp;</label>
                        <div class="btn-filter-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to', 'per_page']))
                                <a href="{{ route('dashboard.projects.beneficiaries', $project) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
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
                    <div class="col-auto">
                        <span class="selected-count-badge">
                            <i class="fas fa-check-circle"></i>
                            <span id="selected-count">0</span> محدد
                        </span>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-sm open-bulk-modal mr-2">
                            <i class="fas fa-edit"></i> تعديل حالة المحددين
                        </button>
                        <button type="button" class="btn btn-danger btn-sm bulk-delete-btn">
                            <i class="fas fa-trash-alt"></i> حذف المحددين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @component('dashboard::components.table-box')
            @slot('title')
                <i class="fas fa-users"></i> المستفيدين ({{ number_format($beneficiaries->total()) }})
            @endslot

            @slot('tools')
                <a href="{{ route('dashboard.projects.beneficiaries.filter-areas', $project) }}"
                   class="btn btn-info btn-sm">
                    <i class="fas fa-map-marked-alt"></i> ترشيح حسب المناطق
                </a>
                <a href="{{ route('dashboard.projects.beneficiaries.import', $project) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-import"></i> استيراد Excel
                </a>
                <a href="{{ route('dashboard.projects.show', $project) }}"
                   class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> عودة للمشروع
                </a>
            @endslot

            <thead>
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
                    <th style="width: 8%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($beneficiaries as $index => $beneficiary)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="items[]" value="{{ $beneficiary->id }}"
                               class="item-checkbox" data-check-all-item>
                    </td>
                    <td class="text-center"><strong>{{ $beneficiaries->firstItem() + $index }}</strong></td>
                    <td><span class="badge badge-light">{{ $beneficiary->id_num }}</span></td>
                    <td>
                        <a href="{{ route('dashboard.people.show', $beneficiary) }}"
                           class="text-primary font-weight-bold text-decoration-none">
                            {{ $beneficiary->first_name }} {{ $beneficiary->father_name }}
                            {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                        </a>
                    </td>
                    <td dir="ltr" class="text-left">
                        @if($beneficiary->phone)
                            <i class="fas fa-phone text-success"></i> {{ $beneficiary->phone }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $beneficiary->neighborhood ?? $beneficiary->current_city ?? '-' }}
                        </small>
                    </td>
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
                    <td class="text-center">
                        <span class="badge badge-dark">{{ $beneficiary->pivot->quantity ?? 1 }}</span>
                    </td>
                    <td>
                        @if($beneficiary->pivot->status === 'مستلم')
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> مستلم
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> غير مستلم
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->pivot->delivery_date)
                            <small>
                                <i class="fas fa-calendar-check text-success"></i>
                                {{ \Carbon\Carbon::parse($beneficiary->pivot->delivery_date)->format('Y-m-d') }}
                            </small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ Str::limit($beneficiary->pivot->notes ?? '-', 30) }}</small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('dashboard.people.show', $beneficiary) }}"
                               class="btn btn-sm btn-outline-info"
                               title="عرض الملف">
                                <i class="fas fa-eye"></i>
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-toggle="modal"
                                    data-target="#statusModal{{ $beneficiary->id }}"
                                    title="تعديل الحالة">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="if(confirm('هل أنت متأكد من الحذف؟')) {
                                        $('#row-delete-form').attr('action', '{{ route('dashboard.projects.beneficiaries.destroy', [$project, $beneficiary]) }}').submit();
                                    }"
                                    title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">
                        <div class="empty-state">
                            @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                <i class="fas fa-search"></i>
                                <p>لا توجد نتائج للبحث</p>
                                <a href="{{ route('dashboard.projects.beneficiaries', $project) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> إلغاء البحث
                                </a>
                            @else
                                <i class="fas fa-inbox"></i>
                                <p>لا يوجد مستفيدين في هذا المشروع</p>
                                <a href="{{ route('dashboard.projects.beneficiaries.import', $project) }}"
                                   class="btn btn-success">
                                    <i class="fas fa-file-import"></i> استيراد مستفيدين
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>

            @if($beneficiaries->hasPages())
                @slot('footer')
                    {{ $beneficiaries->appends(request()->query())->links() }}
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-users-cog"></i>
                        تحديث حالة المحددين (<span id="bulk-selected-count-label">0</span>)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label-icon">
                            <i class="fas fa-toggle-on"></i> حالة الاستلام
                            <span class="text-danger">*</span>
                        </label>
                        <select id="bulk_modal_status" class="form-control custom-select-filter" required>
                            <option value="غير مستلم">غير مستلم</option>
                            <option value="مستلم">مستلم</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label-icon">
                            <i class="fas fa-cubes"></i> الكمية
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="bulk_modal_quantity"
                               class="form-control" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label-icon">
                            <i class="fas fa-calendar-check"></i> تاريخ التسليم
                        </label>
                        <input type="date" id="bulk_modal_delivery_date"
                               class="form-control date-picker-custom" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label-icon">
                            <i class="fas fa-sticky-note"></i> الملاحظات
                        </label>
                        <textarea id="bulk_modal_notes" class="form-control"
                                  rows="3" placeholder="أدخل ملاحظات إضافية..."></textarea>
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
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('dashboard.projects.beneficiaries.update-status', [$project, $beneficiary]) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-user-edit"></i>
                                تحديث حالة: {{ $beneficiary->first_name }} {{ $beneficiary->family_name }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-toggle-on"></i> حالة الاستلام
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-control custom-select-filter" required>
                                    <option value="غير مستلم" {{ $beneficiary->pivot->status === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                    <option value="مستلم" {{ $beneficiary->pivot->status === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-cubes"></i> الكمية
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="quantity" class="form-control"
                                       value="{{ $beneficiary->pivot->quantity ?? 1 }}" min="1" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-calendar-check"></i> تاريخ التسليم
                                </label>
                                <input type="date" name="delivery_date"
                                       class="form-control date-picker-custom"
                                       value="{{ $beneficiary->pivot->delivery_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-sticky-note"></i> الملاحظات
                                </label>
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
        console.log('✅ Beneficiaries script loaded');

        // 1. Focus search field
        $('#search').focus();

        // 2. Update bulk actions UI
        function updateBulkUI() {
            const selectedCheckboxes = $('.item-checkbox:checked');
            const count = selectedCheckboxes.length;

            console.log('🔄 Selected items:', count);

            if (count > 0) {
                $('#bulk-actions-bar').removeClass('d-none').slideDown(200);
                $('#selected-count, #bulk-selected-count-label').text(count);
            } else {
                $('#bulk-actions-bar').slideUp(200, function() {
                    $(this).addClass('d-none');
                });
            }
        }

        // Update on checkbox change
        $(document).on('change', '.item-checkbox, input[data-children]', function() {
            setTimeout(updateBulkUI, 50);
        });

        // 3. Open Bulk Modal
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

        // 4. Submit Bulk Update
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

            // Fill hidden fields
            let fieldsHtml = '';
            for (const [key, value] of Object.entries(data)) {
                fieldsHtml += `<input type="hidden" name="${key}" value="${value}">`;
            }
            $('#bulk-hidden-fields').html(fieldsHtml);

            $('#bulkStatusModal').modal('hide');
            setTimeout(() => {
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
                console.log('🗑️ Deleting', count, 'items');
                $('#bulk-hidden-fields').html('<input type="hidden" name="action" value="delete">');
                $('#bulk-actions-form').submit();
            }
        });

        // 6. Auto-submit on per_page change
        $(document).on('change', '#per_page', function() {
            $(this).closest('form').submit();
        });

        // Initial check
        updateBulkUI();
    });
    </script>
    @endpush
</x-layout>
