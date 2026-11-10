<x-layout :title="'المستفيدين من مشروع: ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries', $project]">

    @push('styles')
    <style>
        /* ========== البطاقة الرئيسية للبحث والفلاتر ========== */
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

        /* ========== تنسيق Labels والأيقونات ========== */
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

        /* ========== تنسيق الحقول ========== */
        .date-picker-custom,
        .custom-select-filter,
        .search-textarea,
        .form-control {
            padding: 0.625rem 0.875rem !important;
            border: 2px solid #e3e6f0 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            font-size: 0.95rem !important;
        }

        .date-picker-custom:focus,
        .custom-select-filter:focus,
        .search-textarea:focus,
        .form-control:focus {
            border-color: #4e73df !important;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
        }

        .search-textarea {
            resize: vertical;
            min-height: 60px;
        }

        .date-picker-custom {
            cursor: pointer;
        }

        /* ========== تنسيق الأزرار ========== */
        .btn-filter-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-filter-group .btn {
            white-space: nowrap;
        }

        /* ========== الفلاتر المتقدمة ========== */
        #advancedFilters {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }

        /* ========== شريط الإجراءات الجماعية ========== */
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

        /* ========== تنسيق الجدول ========== */
        .table {
            width: 100% !important;
            margin-bottom: 0 !important;
            table-layout: fixed;
            min-width: 1200px; /* زيادة بسيط لضمان الراحة في العرض */
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 10px 10px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
        }

        .table th, .table td {
            vertical-align: middle !important;
            text-align: right; /* اتجاه النصوص لليمين بما يتناسب مع العربية */
            padding: 14px 10px !important;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f3f9 !important;
            word-wrap: break-word;
        }

        .table th {
            background-color: #f8f9fc !important;
            color: #4e73df !important;
            font-weight: 700 !important;
            text-transform: none;
            font-size: 0.85rem;
            border-top: none !important;
            border-bottom: 2px solid #e3e6f0 !important;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* ========== تنسيق الـ Badges ========== */
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

        /* ========== حالة فارغة ========== */
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
            background: #fff;
        }

        .empty-state i {
            font-size: 5rem;
            color: #eaecf4;
            margin-bottom: 1.5rem;
            display: block;
        }

        .empty-state p {
            font-size: 1.25rem;
            color: #b7b9cc;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .empty-state .btn {
            padding: 0.6rem 2rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* ========== تنسيق الـ Modals ========== */
        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        /* ========== تحسينات عامة ========== */
        .d-flex.gap-2 {
            gap: 0.5rem;
        }
    </style>
    @endpush

    {{-- ========== رسائل الأخطاء والتحذيرات (محمية من الإخفاء التلقائي) ========== --}}
    @if($errors->any())
        <div class="custom-static-alert alert-danger" style="padding: 1rem; border-radius: 0.4rem; margin-bottom: 1rem; border-right: 5px solid #ea5455; background-color: #ffefef; color: #333;">
            <strong style="color: #ea5455;"><i class="fas fa-exclamation-triangle"></i> عذراً، حدثت بعض الأخطاء:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="custom-static-alert alert-warning" style="padding: 1rem; border-radius: 0.4rem; margin-bottom: 1rem; border-right: 5px solid #ff9f43; background-color: #fff8f0; color: #333;">
            <strong style="color: #ff9f43;"><i class="fas fa-exclamation-circle"></i> أخطاء الاستيراد ({{ count(session('import_errors')) }}):</strong>
            <ul class="mb-0 mt-2" style="max-height: 300px; overflow-y: auto;">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ========== بطاقة البحث والفلاتر ========== --}}
    <div class="search-filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.projects.beneficiaries', $project) }}" id="filterForm">

                <div class="row align-items-end">
                    {{-- البحث بأرقام الهوية --}}
                    <div class="col-lg-12 mb-3">
                        <label class="form-label-icon fw-bold" for="search">
                            <i class="fas fa-search text-primary"></i> بحث بأرقام الهوية (أدخل الأرقام مفصولة بمسافة أو سطر)
                        </label>
                        <textarea name="search" id="search" class="form-control search-textarea" rows="1" placeholder="مثال: 123456789 987654321..." style="min-height: 45px;">{{ request('search') }}</textarea>
                    </div>

                    {{-- الفلاتر السريعة --}}
                    <div class="col-lg-3 col-md-4 mb-3">
                        <label class="form-label-icon fw-bold small"><i class="fas fa-toggle-on text-info"></i> الحالة</label>
                        <select name="status" id="status" class="form-control custom-select-filter">
                            <option value="">الكل</option>
                            <option value="مستلم" {{ request('status') === 'مستلم' ? 'selected' : '' }}>✓ مستلم</option>
                            <option value="غير مستلم" {{ request('status') === 'غير مستلم' ? 'selected' : '' }}>✗ غير مستلم</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 mb-3">
                        <label class="form-label-icon fw-bold small"><i class="fas fa-warehouse text-success"></i> نقطة التوزيع</label>
                        <select name="sub_warehouse_id" id="sub_warehouse_id" class="form-control select2 custom-select-filter">
                            <option value="">-- الكل --</option>
                            @foreach($subWarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('sub_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="form-label-icon fw-bold small"><i class="fas fa-list-ol text-warning"></i> النتائج</label>
                        <input type="text" name="per_page" id="per_page" class="form-control text-center" value="{{ request('per_page', 50) }}">
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter"></i> تفعيل الفلتر
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'sub_warehouse_id', 'per_page', 'date_from', 'date_to', 'exact_date', 'quantity_from', 'quantity_to', 'exact_quantity']))
                                <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary" title="إعادة تعيين الفلتر"><i class="fas fa-redo"></i></a>
                            @endif
                            <button type="button" class="btn btn-outline-info" data-toggle="collapse" data-target="#advancedFilters"><i class="fas fa-sliders-h"></i> متطور</button>
                        </div>
                    </div>
                </div>

                {{-- الفلاتر المتقدمة (قابلة للطي) --}}
                <div class="collapse {{ request()->anyFilled(['date_from', 'date_to', 'exact_date', 'quantity_from', 'quantity_to', 'exact_quantity']) ? 'show' : '' }}"
                     id="advancedFilters">

                    {{-- فاصل --}}
                    <div class="row">
                        <div class="col-12">
                            <hr class="my-3">
                        </div>
                    </div>

                    {{-- فلاتر التاريخ --}}
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width: 30px; height: 30px;">
                                    <i class="fas fa-calendar text-white"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-primary">فلاتر التاريخ</h6>
                            </div>
                        </div>

                        {{-- من تاريخ --}}
                        <div class="col-lg-4 col-md-6 mb-3">
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
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label-icon" for="date_to">
                                <i class="fas fa-calendar-check"></i> إلى تاريخ
                            </label>
                            <input type="date"
                                   name="date_to"
                                   id="date_to"
                                   class="form-control date-picker-custom"
                                   value="{{ request('date_to') }}">
                        </div>

                        {{-- مطابقة تاريخ محدد --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label-icon" for="exact_date">
                                <i class="fas fa-calendar-day"></i> تاريخ محدد
                                <span class="badge bg-info text-white ms-1" style="font-size: 0.65rem;">مطابقة تامة</span>
                            </label>
                            <input type="date"
                                   name="exact_date"
                                   id="exact_date"
                                   class="form-control date-picker-custom"
                                   value="{{ request('exact_date') }}">
                        </div>
                    </div>

                    {{-- فلاتر الكمية --}}
                    <div class="row mt-3">
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width: 30px; height: 30px;">
                                    <i class="fas fa-hashtag text-white"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-success">فلاتر الكمية</h6>
                            </div>
                        </div>

                        {{-- من كمية --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label-icon" for="quantity_from">
                                <i class="fas fa-sort-numeric-up"></i> من كمية
                            </label>
                            <input type="text"
                                   name="quantity_from"
                                   id="quantity_from"
                                   class="form-control"
                                   placeholder="1"
                                   value="{{ request('quantity_from') }}">
                        </div>

                        {{-- إلى كمية --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label-icon" for="quantity_to">
                                <i class="fas fa-sort-numeric-down"></i> إلى كمية
                            </label>
                            <input type="text"
                                   name="quantity_to"
                                   id="quantity_to"
                                   class="form-control"
                                   placeholder="100"
                                   value="{{ request('quantity_to') }}">
                        </div>

                        {{-- كمية محددة --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label-icon" for="exact_quantity">
                                <i class="fas fa-equals"></i> كمية محددة
                                <span class="badge bg-info text-white ms-1" style="font-size: 0.65rem;">مطابقة تامة</span>
                            </label>
                            <input type="text"
                                   name="exact_quantity"
                                   id="exact_quantity"
                                   class="form-control"
                                   placeholder="مثال: 5"
                                   value="{{ request('exact_quantity') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ========== نموذج الإجراءات الجماعية ========== --}}
    <form action="{{ route('dashboard.projects.beneficiaries.bulk-actions', $project) }}" method="POST" id="bulk-actions-form">
        @csrf
        {{-- Hidden inputs - سيتم ملؤها عبر JavaScript --}}
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

        {{-- ========== جدول المستفيدين ========== --}}
        @component('dashboard::components.table-box')
            @slot('title')
                <i class="fas fa-users"></i> المستفيدين ({{ number_format($beneficiaries->total()) }})
                @if($project->is_cash)
                    <span class="badge badge-success ml-2" title="مشروع كوبون نقدي">
                        <i class="fas fa-money-bill-wave"></i> كوبون نقدي: {{ $project->formatted_cash_value }}
                        @if($project->cash_amount_type === 'custom')
                            (مبلغ منفصل عند التسليم)
                        @else
                            (مبلغ موحد)
                        @endif
                    </span>
                @else
                    <span class="badge badge-success ml-2" title="إجمالي الكميات المشحونة/الموزعة">
                        <i class="fas fa-cubes"></i> إجمالي الكميات: {{ number_format($totalQuantity) }}
                    </span>
                @endif
                @if(session('skipped_count'))
                    <span class="badge badge-warning ml-2" title="أسطر تم استبعادها لعدم وجود بيانات أو تعارض">
                        <i class="fas fa-exclamation-circle"></i> مستبعد: {{ number_format(session('skipped_count')) }}
                    </span>
                @endif
            @endslot

            @slot('tools')
                <a href="{{ route('dashboard.projects.beneficiaries.filter-areas', $project) }}"
                   class="btn btn-info btn-sm">
                    <i class="fas fa-map-marked-alt"></i> ترشيح حسب المناطق
                </a>
                <a href="{{ route('dashboard.projects.beneficiaries.export', [$project] + request()->all()) }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-file-export"></i> تصدير النتائج
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
                    @if($project->is_cash)
                        <th style="width: 11%;">طريقة التسليم</th>
                        <th style="width: 8%;">المبلغ</th>
                    @else
                        <th style="width: 10%;">المخزن الفرعي</th>
                        <th style="width: 5%;">الكمية</th>
                    @endif
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
                    <td>
                        <span class="badge badge-light">{{ $beneficiary->id_num }}</span>
                        @if($beneficiary->entity_type === 'incomplete')
                            <span class="badge badge-warning" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;">سجل مؤقت</span>
                        @elseif($beneficiary->entity_type === 'employee')
                            <span class="badge badge-primary" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;">موظف</span>
                        @endif
                    </td>
                    <td>
                        @if($beneficiary->entity_type === 'person')
                            <a href="{{ route('dashboard.people.show', $beneficiary->person_id) }}"
                               class="text-primary font-weight-bold text-decoration-none">
                                {{ $beneficiary->first_name }} {{ $beneficiary->father_name }}
                                {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                            </a>
                        @elseif($beneficiary->entity_type === 'employee')
                            <a href="{{ route('dashboard.employees.show', $beneficiary->employee_id) }}"
                               class="text-primary font-weight-bold text-decoration-none">
                                {{ $beneficiary->first_name }} {{ $beneficiary->father_name }}
                                {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                            </a>
                        @else
                            <span class="text-dark font-weight-bold">
                                {{ $beneficiary->first_name }} {{ $beneficiary->father_name }}
                                {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                            </span>
                        @endif
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
                    @if($project->is_cash)
                        @php
                            $deliveryMethod = $beneficiary->delivery_method ?? $project->default_delivery_method ?? 'cash';
                            $methodMeta = match($deliveryMethod) {
                                'wallet' => ['label' => 'محفظة إلكترونية', 'icon' => 'fas fa-wallet', 'badge' => 'badge-info'],
                                'bank'   => ['label' => 'حساب بنكي', 'icon' => 'fas fa-university', 'badge' => 'badge-primary'],
                                default  => ['label' => 'كاش (نقداً)', 'icon' => 'fas fa-money-bill-wave', 'badge' => 'badge-success'],
                            };
                            $beneficiaryCurrency = $beneficiary->cash_currency ?? $project->cash_currency ?? 'ILS';
                            $beneficiaryCurrencySymbol = \App\Models\Project::CURRENCY_SYMBOLS[$beneficiaryCurrency] ?? '₪';
                            $beneficiaryAmount = $beneficiary->cash_amount;
                            if ($beneficiaryAmount === null && $project->cash_amount !== null && $project->cash_amount_type !== 'custom') {
                                $beneficiaryAmount = $project->cash_amount;
                            }
                        @endphp
                        <td>
                            <span class="badge {{ $methodMeta['badge'] }}">
                                <i class="{{ $methodMeta['icon'] }} mr-1"></i> {{ $methodMeta['label'] }}
                            </span>
                            @if(!empty($beneficiary->delivery_details))
                                <br><small class="text-muted" title="{{ $beneficiary->delivery_details }}">
                                    <i class="fas fa-info-circle"></i> {{ Str::limit($beneficiary->delivery_details, 18) }}
                                </small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($beneficiaryAmount !== null)
                                <span class="badge badge-dark font-weight-bold" style="font-size: 0.9rem;">
                                    {{ number_format((float)$beneficiaryAmount, ((float)$beneficiaryAmount == (int)$beneficiaryAmount ? 0 : 2)) }} {{ $beneficiaryCurrencySymbol }}
                                </span>
                            @else
                                <span class="badge badge-warning" style="font-size: 0.75rem;" title="يتم تحديده عند التسليم">
                                    <i class="fas fa-clock"></i> عند التسليم
                                </span>
                            @endif
                        </td>
                    @else
                        <td>
                            @if($beneficiary->sub_warehouse_id && isset($subWarehouses[$beneficiary->sub_warehouse_id]))
                                <span class="badge badge-info">
                                    <i class="fas fa-warehouse"></i>
                                    {{ $subWarehouses[$beneficiary->sub_warehouse_id]->name }}
                                </span>
                            @else
                                <span class="text-muted">{{ $beneficiary->sub_warehouse_id ? 'محذوف' : 'غير محدد' }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-dark">{{ $beneficiary->quantity ?? 1 }}</span>
                        </td>
                    @endif
                    <td>
                        @if($beneficiary->status === 'مستلم')
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
                        @if($beneficiary->delivery_date)
                            <small>
                                <i class="fas fa-calendar-check text-success"></i>
                                {{ \Carbon\Carbon::parse($beneficiary->delivery_date)->format('Y-m-d') }}
                            </small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ Str::limit($beneficiary->notes ?? '-', 30) }}</small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @if($beneficiary->entity_type === 'person')
                            <a href="{{ route('dashboard.people.show', $beneficiary->person_id) }}"
                               class="btn btn-sm btn-outline-info"
                               title="عرض الملف">
                                <i class="fas fa-eye"></i>
                            </a>
                            @elseif($beneficiary->entity_type === 'employee')
                            <a href="{{ route('dashboard.employees.show', $beneficiary->employee_id) }}"
                               class="btn btn-sm btn-outline-info"
                               title="عرض الملف">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif

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
                                        $('#row-delete-form').attr('action', '{{ route('dashboard.projects.beneficiaries.destroy', [$project, $beneficiary->id]) }}').submit();
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

    {{-- ========== نماذج مخفية ========== --}}
    <form id="row-delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ========== Modal التحديث الجماعي ========== --}}
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
                    @if($project->is_cash)
                        <div class="form-group">
                            <label class="form-label-icon">
                                <i class="fas fa-hand-holding-usd text-success"></i> طريقة التسليم
                            </label>
                            <select id="bulk_modal_delivery_method" class="form-control custom-select-filter">
                                <option value="">-- بدون تغيير (الاحتفاظ بالحالي) --</option>
                                <option value="cash">كاش (نقداً باليد)</option>
                                <option value="wallet">محفظة إلكترونية</option>
                                <option value="bank">حساب بنكي / تحويل</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-money-bill-wave text-success"></i> المبلغ النقدي
                                </label>
                                <input type="number" step="0.01" id="bulk_modal_cash_amount"
                                       class="form-control" placeholder="اتركه فارغاً لعدم التعديل">
                            </div>
                            <div class="col-md-5 form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-coins text-warning"></i> العملة
                                </label>
                                <select id="bulk_modal_cash_currency" class="form-control custom-select-filter">
                                    <option value="">الافتراضية للمشروع</option>
                                    @foreach(\App\Models\Project::CURRENCY_SYMBOLS as $cKey => $cSym)
                                        <option value="{{ $cKey }}" {{ ($project->cash_currency ?? 'ILS') === $cKey ? 'selected' : '' }}>
                                            {{ $cKey }} ({{ $cSym }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label-icon">
                                <i class="fas fa-cubes"></i> الكمية
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="bulk_modal_quantity"
                                   class="form-control" value="1" min="1" required>
                        </div>
                    @endif
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

    {{-- ========== Modal لكل مستفيد ========== --}}
    @foreach($beneficiaries as $beneficiary)
        <div class="modal fade" id="statusModal{{ $beneficiary->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('dashboard.projects.beneficiaries.update-status', [$project, $beneficiary->id]) }}" method="POST">
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
                                    <option value="غير مستلم" {{ $beneficiary->status === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                    <option value="مستلم" {{ $beneficiary->status === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                </select>
                            </div>
                            @if($project->is_cash)
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-icon">
                                            <i class="fas fa-hand-holding-usd text-success"></i> طريقة التسليم
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="delivery_method" class="form-control custom-select-filter" required>
                                            @php $curMethod = $beneficiary->delivery_method ?? $project->default_delivery_method ?? 'cash'; @endphp
                                            <option value="cash" {{ $curMethod === 'cash' ? 'selected' : '' }}>كاش (نقداً باليد)</option>
                                            <option value="wallet" {{ $curMethod === 'wallet' ? 'selected' : '' }}>محفظة إلكترونية</option>
                                            <option value="bank" {{ $curMethod === 'bank' ? 'selected' : '' }}>حساب بنكي / تحويل</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-icon">
                                            <i class="fas fa-coins text-warning"></i> العملة
                                        </label>
                                        <select name="cash_currency" class="form-control custom-select-filter">
                                            @php $curCurrency = $beneficiary->cash_currency ?? $project->cash_currency ?? 'ILS'; @endphp
                                            @foreach(\App\Models\Project::CURRENCY_SYMBOLS as $cKey => $cSym)
                                                <option value="{{ $cKey }}" {{ $curCurrency === $cKey ? 'selected' : '' }}>
                                                    {{ $cKey }} ({{ $cSym }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-icon">
                                            <i class="fas fa-money-bill-wave text-success"></i> المبلغ النقدي
                                            <small class="text-muted">({{ $project->cash_amount_type === 'custom' ? 'محدد للمستفيد' : 'موحد للمشروع' }})</small>
                                        </label>
                                        <input type="number" step="0.01" name="cash_amount" class="form-control"
                                               value="{{ $beneficiary->cash_amount ?? ($project->cash_amount_type === 'fixed' ? $project->cash_amount : '') }}"
                                               placeholder="{{ $project->cash_amount ? $project->cash_amount : 'أدخل المبلغ' }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label-icon">
                                            <i class="fas fa-info-circle text-info"></i> بيانات التسليم (محفظة/بنك)
                                        </label>
                                        <input type="text" name="delivery_details" class="form-control"
                                               value="{{ $beneficiary->delivery_details }}"
                                               placeholder="رقم المحفظة أو الحساب البنكي...">
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label-icon">
                                        <i class="fas fa-cubes"></i> الكمية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="quantity" class="form-control"
                                           value="{{ $beneficiary->quantity ?? 1 }}" min="1" required>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-calendar-check"></i> تاريخ التسليم
                                </label>
                                <input type="date" name="delivery_date"
                                       class="form-control date-picker-custom"
                                       value="{{ $beneficiary->delivery_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-icon">
                                    <i class="fas fa-sticky-note"></i> الملاحظات
                                </label>
                                <textarea name="notes" class="form-control" rows="3">{{ $beneficiary->notes }}</textarea>
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

    {{-- ========== JavaScript ========== --}}
    @push('scripts')
            <script>
            $(document).ready(function() {
                console.log('✅ Beneficiaries page loaded');

                // 1. Focus على حقل البحث
                $('#search').focus();

                // 2. تحديث واجهة الإجراءات الجماعية
                function updateBulkUI() {
                    const selectedCheckboxes = $('.item-checkbox:checked');
                    const count = selectedCheckboxes.length;

                    if (count > 0) {
                        $('#bulk-actions-bar').removeClass('d-none').slideDown(200);
                        $('#selected-count, #bulk-selected-count-label').text(count);
                    } else {
                        $('#bulk-actions-bar').slideUp(200, function() {
                            $(this).addClass('d-none');
                        });
                    }
                }

                // التحديث عند تغيير Checkboxes
                $(document).on('change', '.item-checkbox, input[data-children]', function() {
                    setTimeout(updateBulkUI, 50);
                });

                // 3. فتح Modal التحديث الجماعي
                $(document).on('click', '.open-bulk-modal', function(e) {
                    e.preventDefault();
                    const count = $('.item-checkbox:checked').length;

                    if (count === 0) {
                        alert('⚠️ الرجاء تحديد مستفيدين أولاً');
                        return;
                    }

                    $('#bulk-selected-count-label').text(count);
                    $('#bulkStatusModal').modal('show');
                });

                // 4. إرسال التحديث الجماعي
                $(document).on('click', '#submit-bulk-modal', function(e) {
                    e.preventDefault();

                    const selectedItems = $('.item-checkbox:checked');
                    const count = selectedItems.length;

                    if (count === 0) {
                        alert('⚠️ لم يتم تحديد عناصر');
                        return;
                    }

                    const isCashProject = {{ $project->is_cash ? 'true' : 'false' }};
                    const data = {
                        action: 'update_status',
                        status: $('#bulk_modal_status').val(),
                        delivery_date: $('#bulk_modal_delivery_date').val(),
                        notes: $('#bulk_modal_notes').val()
                    };

                    if (isCashProject) {
                        const bulkMethod = $('#bulk_modal_delivery_method').val();
                        const bulkCashAmt = $('#bulk_modal_cash_amount').val();
                        const bulkCashCurr = $('#bulk_modal_cash_currency').val();
                        if (bulkMethod) data.delivery_method = bulkMethod;
                        if (bulkCashAmt !== '') data.cash_amount = bulkCashAmt;
                        if (bulkCashCurr) data.cash_currency = bulkCashCurr;
                    } else {
                        data.quantity = $('#bulk_modal_quantity').val();
                    }

                    if (!confirm(`هل أنت متأكد من تحديث ${count} مستفيدين؟`)) return;

                    // ملء الحقول المخفية
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

                // 5. الحذف الجماعي
                $(document).on('click', '.bulk-delete-btn', function(e) {
                    e.preventDefault();
                    const count = $('.item-checkbox:checked').length;

                    if (count === 0) {
                        alert('⚠️ الرجاء تحديد مستفيدين أولاً');
                        return;
                    }

                    if (confirm(`⚠️ هل أنت متأكد من حذف ${count} مستفيدين؟ لا يمكن التراجع!`)) {
                        $('#bulk-hidden-fields').html('<input type="hidden" name="action" value="delete">');
                        $('#bulk-actions-form').submit();
                    }
                });

                // التحقق الأولي
                updateBulkUI();
            });

            // ========== كود التحميل التلقائي لملف الأخطاء ==========

            function checkForErrors() {
                const projectId = {{ $project->id ?? 0 }};
                if (!projectId) return;

                const url = "{{ route('dashboard.projects.check_import_errors', $project->id) }}";

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Check Status Response:', data);

                        if (data.status === 'finished') {
                            if (data.has_errors && data.download_url) {
                                console.log('Finished with errors. downloading...');
                                window.location.href = data.download_url;
                                // تحديث الصفحة بعد ثانية لإظهار البيانات الجديدة وإيقاف الفحص
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                console.log('Finished with no errors.');
                                window.location.reload();
                            }
                        } else if (data.status === 'pending') {
                            console.log('Import still processing...');
                            setTimeout(checkForErrors, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking status:', error);
                        // محاولة مجددة في حال فشل الاتصال
                        setTimeout(checkForErrors, 10000);
                    });
            }

            // بدء التحقق عند تحميل الصفحة فقط إذا كان هناك جلسة 'importing'
            @if(session('importing'))
                console.log('Import session detected, starting checks...');
                setTimeout(checkForErrors, 3000); // انتظر 3 ثواني ثم ابدأ التحقق
            @endif
        </script>
    @endpush
</x-layout>
