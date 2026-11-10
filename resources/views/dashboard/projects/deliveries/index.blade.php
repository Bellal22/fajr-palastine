<x-layout :title="'تسليم الطرود والكوبونات'" :breadcrumbs="['dashboard.projects.deliveries']">

    @push('styles')
    <style>
        .delivery-card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: all 0.25s ease;
        }
        .category-toggle-btn, .mode-selector-btn {
            border-radius: 6px;
            font-weight: 600;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        /* الأزرار غير النشطة: خلفية فاتحة وأيقونة زرقاء دائماً (لا تظهر سوداء أو حمراء) */
        .category-toggle-btn.btn-default,
        .mode-selector-btn.btn-default {
            background-color: #f8f9fa !important;
            border-color: #ced4da !important;
            color: #343a40 !important;
        }
        .category-toggle-btn.btn-default i,
        .mode-selector-btn.btn-default i,
        .category-toggle-btn.btn-default .cat-icon,
        .mode-selector-btn.btn-default .mode-icon {
            color: #007bff !important;
        }
        /* الأزرار النشطة: خلفية زرقاء وأيقونة بيضاء واضحة ومتناسقة */
        .category-toggle-btn.btn-primary,
        .mode-selector-btn.btn-primary,
        .category-toggle-btn.active,
        .mode-selector-btn.active {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0,123,255,0.3) !important;
        }
        .category-toggle-btn.btn-primary i,
        .mode-selector-btn.btn-primary i,
        .category-toggle-btn.active i,
        .mode-selector-btn.active i,
        .category-toggle-btn.btn-primary .cat-icon,
        .mode-selector-btn.btn-primary .mode-icon,
        .category-toggle-btn.active .cat-icon,
        .mode-selector-btn.active .mode-icon {
            color: #ffffff !important;
        }
        .coupon-check-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 8px;
            background: #fff;
            transition: all 0.2s;
        }
        .coupon-check-item:hover {
            border-color: #007bff;
            background: #f8fbff;
        }
        .coupon-check-item.selected {
            border-color: #007bff;
            background: #eef6ff;
        }
        .id-input-scanner {
            font-size: 1.35rem;
            letter-spacing: 2px;
            font-weight: 700;
            text-align: center;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 12px;
        }
        .id-input-scanner:focus {
            box-shadow: 0 0 0 0.25rem rgba(0,123,255,0.25);
            border-color: #0056b3;
        }
        .result-badge {
            font-size: 0.95rem;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .pulse-loader {
            display: inline-block;
            animation: pulse 1s infinite alternate;
        }
        @keyframes pulse {
            0% { opacity: 0.4; }
            100% { opacity: 1; }
        }
    </style>
    @endpush

    <div class="row">
        <!-- القسم الرئيسي: واجهة التسليم والتحقق -->
        <div class="col-lg-8">
            <div class="card delivery-card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-boxes text-primary mr-1"></i>
                        تسليم الطرود والكوبونات
                    </h3>
                </div>

                <div class="card-body">
                    <!-- خيار نوع عملية التسليم: عادي OR المشاريع المخصصة (RRM وغيرها) -->
                    <div class="card card-outline card-primary mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="d-flex flex-wrap align-items-center mb-2 mb-md-0">
                                    <span class="font-weight-bold text-dark mr-3 mb-1" style="font-size: 14px;">
                                        <i class="fas fa-layer-group text-primary mr-1"></i> نوع عملية التسليم:
                                    </span>
                                    <div class="btn-group btn-group-toggle flex-wrap" data-toggle="buttons">
                                        <label class="btn btn-default btn-sm px-3 py-1 font-weight-bold category-toggle-btn border mb-1" id="btnTypeStandard">
                                            <input type="radio" name="delivery_category" value="standard" autocomplete="off">
                                            <i class="fas fa-box text-primary mr-1 cat-icon"></i> تسليم عادي
                                        </label>
                                        @if(isset($customProjects) && $customProjects->count() > 0)
                                            @foreach($customProjects as $cp)
                                                <label class="btn btn-default btn-sm px-3 py-1 font-weight-bold category-toggle-btn border mb-1 btn-custom-project"
                                                       id="btnTypeCustomProject_{{ $cp->id }}"
                                                       data-custom-project-id="{{ $cp->id }}">
                                                    <input type="radio" name="delivery_category" value="custom_{{ $cp->id }}" data-custom-project-id="{{ $cp->id }}" autocomplete="off">
                                                    <i class="{{ $cp->icon ?: 'fas fa-first-aid' }} text-{{ $cp->color ?: 'primary' }} mr-1 cat-icon"></i> تسليم {{ $cp->name }}
                                                </label>
                                            @endforeach
                                        @else
                                            <label class="btn btn-default btn-sm px-3 py-1 font-weight-bold category-toggle-btn border mb-1" id="btnTypeRrm">
                                                <input type="radio" name="delivery_category" value="rrm" autocomplete="off">
                                                <i class="fas fa-first-aid text-primary mr-1 cat-icon"></i> تسليم مشروع الـ RRM
                                            </label>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('dashboard.rrm.index') }}" class="btn btn-sm btn-default border font-weight-bold shadow-sm mr-1">
                                        <i class="fas fa-clipboard-list text-primary mr-1"></i> كشوفات المشاريع المخصصة
                                    </a>
                                    <a href="{{ route('dashboard.custom_projects.index') }}" class="btn btn-sm btn-outline-primary font-weight-bold shadow-sm">
                                        <i class="fas fa-sliders-h mr-1"></i> إدارة وتخصيص المشاريع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- رسالة تنبيه أولية لحث المستخدم على اختيار نوع التسليم (تظهر عندما لا يكون أي خيار محدداً) -->
                    <div id="selectCategoryNotice" class="text-center py-5 px-3 my-4 bg-light rounded border border-dashed">
                        <div class="mb-3">
                            <i class="fas fa-hand-pointer fa-3x text-primary"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-2">يرجى اختيار نوع عملية التسليم للبدء</h5>
                        <p class="text-muted mb-0" style="max-width: 560px; margin: 0 auto; font-size: 14px;">
                            اختر <strong class="text-primary">"تسليم عادي"</strong> للتسليم الفردي لكوبون محدد أو مجموعة طرود، أو اختر أحد <strong class="text-primary">"المشاريع المخصصة (مثل الـ RRM)"</strong> لتطبيق إعداداته المخصصة تلقائياً وتسجيل الكشف ضمن قسمه المستقل.
                        </p>
                    </div>

                    <!-- حاوية إجراءات التسليم (تكون مغلقة افتراضياً وتفتح فقط فور اختيار نوع التسليم) -->
                    <div id="deliveryWorkflowContainer" style="display: none;">

                    <!-- بطاقة تنبيه وخيارات كشف المشروع المخصص المنفصل (AdminLTE Callout) -->
                    <div id="rrmBatchConfigAlert" class="callout callout-warning mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="font-weight-bold text-dark mb-0" id="customProjectCalloutTitle">
                                <i class="fas fa-first-aid text-warning mr-1"></i> كشف تسليم مشروع الـ RRM (آلية الاستجابة السريعة)
                            </h5>
                            <div>
                                <span class="badge badge-warning font-weight-bold px-2 py-1" id="customProjectCalloutBadge">كشف RRM مستقل</span>
                                <a href="#" id="customProjectEditConfigLink" class="btn btn-xs btn-outline-secondary ml-1" style="display: none;" title="تخصيص طريقة التعامل لهذا المشروع">
                                    <i class="fas fa-cog"></i> تخصيص المشروع
                                </a>
                            </div>
                        </div>
                        <p class="small text-muted mb-2" id="customProjectCalloutDesc">
                            في هذا النمط، يتم تطبيق إعدادات المشروع المخصص تلقائياً (الطرود الافتراضية، المخزن، وسياسات التسليم)، وسيقوم النظام بتسجيل كشف تسليم مستقل باسم مخصص في <a href="{{ route('dashboard.rrm.index') }}" class="font-weight-bold text-primary">قسم كشوفات المشاريع المخصصة</a> لتتمكن من مراجعة المستفيدين وتصديرهم وطباعة كشوفات الاستلام الميدانية.
                        </p>

                        <div class="row">
                            @if(isset($rrmBatches) && $rrmBatches->count() > 0)
                                <div class="col-md-6 mb-2">
                                    <label for="rrmBatchSelect" class="font-weight-bold text-dark small mb-1">
                                        <i class="fas fa-list-alt text-secondary mr-1"></i> اختيار كشف قائم (للإضافة عليه):
                                    </label>
                                    <select id="rrmBatchSelect" class="form-control">
                                        <option value="">-- أو أنشئ كشفاً جديداً بالأسفل --</option>
                                        @foreach($rrmBatches as $rb)
                                            <option value="{{ $rb->id }}" 
                                                    data-custom-project-id="{{ $rb->custom_project_id }}"
                                                    data-name="{{ $rb->name }}" 
                                                    data-date="{{ $rb->delivery_date ? $rb->delivery_date->format('Y-m-d') : '' }}"
                                                    data-warehouse="{{ $rb->sub_warehouse_id }}"
                                                    data-projects="{{ json_encode($rb->project_ids ?? []) }}"
                                                    data-quantities="{{ json_encode($rb->project_quantities ?? []) }}">
                                                {{ $rb->name }} ({{ $rb->code }}) - {{ $rb->beneficiaries_count }} مستفيد
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="{{ isset($rrmBatches) && $rrmBatches->count() > 0 ? 'col-md-6' : 'col-12' }} mb-2">
                                <label for="rrmBatchCustomName" class="font-weight-bold text-dark small mb-1">
                                    <i class="fas fa-pen text-secondary mr-1"></i> اسم الكشف / التوزيعة <span class="text-danger">*</span>:
                                </label>
                                <input type="text" id="rrmBatchCustomName" class="form-control" 
                                       placeholder="مثلاً: KHY-FIR-005، أو Fire 500، أو كشف مواصي خان يونس...">
                            </div>
                        </div>
                        <small class="text-muted d-block">
                            <i class="fas fa-info-circle text-info mr-1"></i> يمكنك اختيار كشف قائم لإضافة مستفيدين جدد إليه، أو كتابة اسم كشف جديد.
                        </small>
                    </div>

                    <!-- خيار نطاق الكوبونات: طرد واحد أو مجموعة طرود -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold d-block text-muted mb-2">
                            <i class="fas fa-layer-group text-info mr-1"></i>
                            نطاق تسليم الكوبونات:
                        </label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-default border mode-selector-btn w-50 py-2 font-weight-bold" id="btnModeSingle">
                                <input type="radio" name="coupon_scope" value="single" autocomplete="off">
                                <i class="fas fa-box text-primary mr-1 mode-icon"></i>
                                تسليم طرد واحد (كوبون محدد)
                            </label>
                            <label class="btn btn-default border mode-selector-btn w-50 py-2 font-weight-bold" id="btnModeMultiple">
                                <input type="radio" name="coupon_scope" value="multiple" autocomplete="off">
                                <i class="fas fa-boxes text-primary mr-1 mode-icon"></i>
                                تسليم مجموعة طرود (كوبونات متعددة)
                            </label>
                        </div>
                    </div>

                    <!-- اختيار الكوبونات حسب النطاق -->
                    <!-- النطاق 1: كوبون واحد -->
                    <div id="singleCouponContainer" class="form-group mb-4">
                        <label for="single_project_id" class="font-weight-bold d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-box text-primary mr-1"></i>
                                اختر الكوبون / الطرد المراد تسليمه <span class="text-danger">*</span>
                            </span>
                            <small class="text-muted font-weight-normal">
                                إجمالي الكوبونات: <strong>{{ count($projects) }}</strong>
                            </small>
                        </label>



                        <select id="single_project_id" class="form-control form-control-lg select2" style="width: 100%;">
                            <option value="">-- اختر الكوبون / الطرد --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}"
                                        {{ (isset($selectedProjectId) && $selectedProjectId == $proj->id) ? 'selected' : '' }}
                                        data-status="{{ $proj->status }}"
                                        data-is-cash="{{ $proj->is_cash ? '1' : '0' }}"
                                        data-cash-value="{{ $proj->formatted_cash_value }}"
                                        data-name="{{ $proj->name }}">
                                    {{ $proj->name }}{{ $proj->is_cash ? ' [نقدي: ' . $proj->formatted_cash_value . ']' : '' }} ({{ $proj->status === 'active' ? 'نشط' : 'مكتمل/موقف' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- النطاق 2: مجموعة كوبونات -->
                    <div id="multipleCouponsContainer" class="form-group mb-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold mb-0">
                                <i class="fas fa-boxes text-info mr-1"></i>
                                حدد مجموعة الكوبونات / الطرود المراد تسليمها معاً <span class="text-danger">*</span>
                            </label>
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary mr-1" id="selectAllCouponsBtn">
                                    <i class="fas fa-check-double"></i> تحديد الكل
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="deselectAllCouponsBtn">
                                    <i class="fas fa-times"></i> إلغاء التحديد
                                </button>
                            </div>
                        </div>

                        <!-- فلتر بحث باسم الكوبون في المجموعة -->
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light text-info font-weight-bold">
                                    <i class="fas fa-filter mr-1"></i> تصفية الكوبونات:
                                </span>
                            </div>
                            <input type="text"
                                   id="filterMultipleCouponsInput"
                                   class="form-control"
                                   placeholder="ابحث باسم الكوبون لتصفية الخيارات أدناه...">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white small text-muted">
                                    المطابق: <strong id="filteredCouponsCount" class="text-primary mx-1">{{ count($projects) }}</strong> من {{ count($projects) }}
                                </span>
                                <button class="btn btn-outline-secondary" type="button" id="btnClearMultipleFilter" title="مسح الفلتر">
                                    <i class="fas fa-times"></i> مسح
                                </button>
                            </div>
                        </div>

                        <div class="p-3 border rounded bg-light" style="max-height: 250px; overflow-y: auto;">
                            <div class="row" id="multiCouponsRow">
                                @forelse($projects as $proj)
                                    <div class="col-md-6 mb-2 coupon-item-col" data-coupon-name="{{ mb_strtolower($proj->name) }}">
                                        <div class="custom-control custom-checkbox coupon-check-item">
                                            <input type="checkbox"
                                                   class="custom-control-input multi-coupon-checkbox"
                                                   id="proj_chk_{{ $proj->id }}"
                                                   value="{{ $proj->id }}">
                                            <label class="custom-control-label font-weight-normal w-100 cursor-pointer" for="proj_chk_{{ $proj->id }}">
                                                <span class="font-weight-bold text-dark coupon-title">{{ $proj->name }}</span>
                                                @if($proj->is_cash)
                                                    <span class="badge badge-success mr-1">نقدي: {{ $proj->formatted_cash_value }}</span>
                                                @endif
                                                <span class="badge {{ $proj->status === 'active' ? 'badge-success' : 'badge-secondary' }} float-left">
                                                    {{ $proj->status === 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-2">لا توجد كوبونات متاحة حالياً</div>
                                @endforelse
                                <div id="noMatchingCouponsNotice" style="display: none;" class="col-12 text-center text-muted py-4">
                                    <i class="fas fa-search-minus fa-2x text-muted mb-2 d-block"></i>
                                    لا توجد كوبونات تطابق كلمة البحث المدخلة.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إعدادات التوزيع المشتركة (المخزن، التاريخ، الكمية) -->
                    <div class="row bg-light p-3 rounded mb-4 border">
                        <div class="col-md-5" id="warehouseContainer">
                            <div class="form-group mb-md-0">
                                <label for="delivery_warehouse_id" class="small font-weight-bold">
                                    <i class="fas fa-warehouse text-secondary mr-1"></i> المخزن الفرعي للتسليم
                                    <span id="warehouseRequiredStar" class="text-danger">*</span>
                                    <span id="warehouseOptionalBadge" class="badge badge-secondary mr-1" style="display: none; font-size: 0.72rem;">غير مطلوب للكوبونات النقدية</span>
                                </label>
                                <select id="delivery_warehouse_id" class="form-control">
                                    <option value="">-- اختر المخزن الفرعي --</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="delivery_date" class="small font-weight-bold">
                                    <i class="fas fa-calendar-alt text-secondary mr-1"></i> تاريخ التسليم
                                </label>
                                <input type="date" id="delivery_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="default_quantity" class="small font-weight-bold">
                                    <i class="fas fa-cubes text-secondary mr-1"></i> الكمية لكل طرد
                                </label>
                                <input type="number" id="default_quantity" class="form-control" value="1" min="1">
                            </div>
                        </div>

                        <!-- إعدادات الكوبون النقدي (طريقة التسليم وتفاصيل الحساب) -->
                        <div class="col-12 mt-3 pt-3 border-top" id="cashDeliveryOptionsSection" style="display: none;">
                            <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center">
                                <i class="fas fa-money-bill-wave fa-lg mr-2"></i>
                                <div>
                                    <strong class="mr-1">إعدادات الكوبون النقدي:</strong>
                                    <span id="cashProjectInfoText" class="small"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0">
                                        <label for="cash_delivery_method" class="small font-weight-bold">
                                            <i class="fas fa-hand-holding-usd text-success mr-1"></i> طريقة التسليم
                                        </label>
                                        <select id="cash_delivery_method" class="form-control">
                                            <option value="cash">كاش (نقداً باليد)</option>
                                            <option value="wallet">محفظة إلكترونية</option>
                                            <option value="bank">حساب بنكي / تحويل</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group mb-md-0">
                                        <label for="cash_delivery_details" class="small font-weight-bold">
                                            <i class="fas fa-info-circle text-info mr-1"></i> بيانات التسليم (رقم المحفظة / الحساب) <small class="text-muted">(اختياري)</small>
                                        </label>
                                        <input type="text" id="cash_delivery_details" class="form-control" placeholder="أدخل رقم المحفظة أو رقم الحساب البنكي...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-md-0">
                                        <label for="cash_global_amount" class="small font-weight-bold">
                                            <i class="fas fa-coins text-warning mr-1"></i> المبلغ النقدي الافتراضي
                                        </label>
                                        <input type="number" step="0.01" id="cash_global_amount" class="form-control" placeholder="المبلغ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تبويبات نمط الفحص: فحص لحظي مباشر OR إدخال جماعي -->
                    <ul class="nav nav-pills mb-3 nav-justified" id="deliveryTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="instant-tab" data-toggle="pill" href="#instantPane" role="tab">
                                <i class="fas fa-search-location mr-1"></i>
                                فحص الهوية المباشر (فحص تلقائي بالجافاسكربت)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="batch-tab" data-toggle="pill" href="#batchPane" role="tab">
                                <i class="fas fa-list-ol mr-1"></i>
                                فحص وتدقيق قائمة هويات (جماعي)
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="deliveryTabContent">
                        <!-- ========================= النمط الأول: الفحص الفردي التلقائي ========================= -->
                        <div class="tab-pane fade show active" id="instantPane" role="tabpanel">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <label for="scannerIdInput" class="h6 font-weight-bold text-dark d-block text-center mb-2">
                                        <i class="fas fa-barcode text-primary mr-1"></i>
                                        أدخل أو امسح رقم هوية المستفيد:
                                    </label>
                                    <div class="position-relative mb-2">
                                        <input type="text"
                                               id="scannerIdInput"
                                               class="form-control id-input-scanner text-center"
                                               placeholder="اكتب أو امسح رقم الهوية هنا (فحص تلقائي بدون زر)..."
                                               maxlength="10"
                                               autocomplete="off"
                                               autofocus>
                                        <button type="button" class="btn btn-sm text-muted position-absolute" id="btnClearScanner" style="left: 15px; top: 50%; transform: translateY(-50%); font-size: 1.25rem; background: none; border: none;" title="تفريغ الحقل">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted text-center font-weight-bold">
                                        <i class="fas fa-magic text-primary mr-1"></i>
                                        يتم الفحص تلقائياً بمجرد كتابة رقم الهوية دون الحاجة للضغط على أي زر.
                                    </small>

                                    <!-- مؤشر التحميل اللحظي -->
                                    <div id="instantLoading" class="text-center py-4" style="display: none;">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <div class="mt-2 text-primary font-weight-bold pulse-loader">
                                            جاري الفحص التلقائي بالجافاسكربت والتحقق من الكوبونات...
                                        </div>
                                    </div>

                                    <!-- رسالة خطأ عند عدم وجود الهوية -->
                                    <div id="instantNotFoundAlert" class="alert alert-danger mt-3" style="display: none;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-times-circle fa-2x mr-3"></i>
                                            <div>
                                                <h6 class="font-weight-bold mb-1" id="notFoundTitle">رقم الهوية غير مسجل</h6>
                                                <p class="mb-0 small" id="notFoundText"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- منطقة نتيجة الفحص اللحظية -->
                                    <div id="instantResultContainer" class="mt-4" style="display: none;">
                                        <!-- كارت معلومات المستفيد -->
                                        <div class="card card-outline card-info bg-light mb-3">
                                            <div class="card-body p-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-7">
                                                        <h5 class="font-weight-bold text-dark mb-1">
                                                            <i class="fas fa-user-check text-info mr-1"></i>
                                                            <span id="resPersonName">-</span>
                                                        </h5>
                                                        <div class="text-muted small">
                                                            <span class="mr-3"><i class="fas fa-id-badge"></i> رقم الهوية: <strong id="resPersonId">-</strong></span>
                                                            <span class="mr-3"><i class="fas fa-phone"></i> الجوال: <span id="resPersonPhone">-</span></span>
                                                            <span><i class="fas fa-users"></i> عدد الأفراد: <span id="resFamilyCount" class="badge badge-info">-</span></span>
                                                        </div>
                                                        <div id="resRelativeAlert" class="mt-2 text-warning small font-weight-bold" style="display: none;">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            تنبيه: هذا الشخص تابع لرب الأسرة: <span id="resHeadName">-</span> (هوية: <span id="resHeadId">-</span>)
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 text-md-left text-center mt-2 mt-md-0">
                                                        <span class="badge badge-primary px-3 py-2" id="resTypeBadge">مستفيد مسجل</span>
                                                        <span class="badge badge-secondary px-3 py-2" id="resBlockBadge">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- جدول / كروت حالة الكوبونات المفحوصة -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-weight-bold text-muted mb-0">
                                                <i class="fas fa-clipboard-check text-success mr-1"></i>
                                                حالة الكوبونات وخيارات التسليم والزيادة:
                                            </h6>
                                            <small class="text-muted font-weight-bold">
                                                حدد الإجراءات المطلوبة للكوبونات حسب رغبتك
                                            </small>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover bg-white mb-3">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>اسم الكوبون / الطرد</th>
                                                        <th class="text-center">الحالة الحالية</th>
                                                        <th class="text-center">الكمية</th>
                                                        <th>المخزن الحالي / تاريخ الاستلام</th>
                                                        <th style="min-width: 270px;">الإجراء المطلوب (حسب رغبتك)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponsStatusTableBody">
                                                    <!-- Dynamic rows rendered by JS -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- شريط تطبيق الإجراءات المحددة -->
                                        <div class="card border-primary" id="actionsApplyCard" style="display: none;">
                                            <div class="card-body p-3 bg-light">
                                                <div class="row align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="form-group mb-2" id="instantGroupNameRow">
                                                            <label for="instantGroupName" class="small font-weight-bold mb-1 text-primary">
                                                                <i class="fas fa-tag mr-1"></i> مسمى عملية التسليم / المجموعة (اختياري - يظهر بسجل المستفيد):
                                                            </label>
                                                            <input type="text" id="instantGroupName" class="form-control form-control-sm" placeholder="مثلاً: سلة الإيواء والمياه (يظهر في سجل المستفيد بدلاً من تسليم مجموعة طرود)">
                                                        </div>
                                                        <div class="form-group mb-md-0">
                                                            <label for="instantNotes" class="small font-weight-bold mb-1">
                                                                <i class="fas fa-comment-alt mr-1"></i> ملاحظات العملية (اختياري):
                                                            </label>
                                                            <input type="text" id="instantNotes" class="form-control form-control-sm" placeholder="ملاحظات تضاف للعملية الحالية...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 text-md-left text-center mt-2 mt-md-0">
                                                        <button type="button" class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm" id="btnSubmitSelectedActions">
                                                            <i class="fas fa-save mr-1"></i>
                                                            تطبيق الإجراءات المحددة (<span id="selectedCountBadge">0</span>)
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================= النمط الثاني: التسليم الجماعي ========================= -->
                        <div class="tab-pane fade" id="batchPane" role="tabpanel">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="batchIdNums" class="font-weight-bold">
                                            <i class="fas fa-align-left text-info mr-1"></i>
                                            أدخل أرقام الهويات (كل رقم بسطر أو مفصولة بفواصل):
                                        </label>
                                        <textarea id="batchIdNums"
                                                  class="form-control"
                                                  rows="5"
                                                  placeholder="أدخل أرقام الهويات هنا...&#10;401234567&#10;409876543"></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button type="button" class="btn btn-info font-weight-bold" id="btnRunBatchCheck">
                                            <i class="fas fa-search-plus mr-1"></i> فحص وتدقيق القائمة بالجافاسكربت
                                        </button>
                                        <small class="text-muted">
                                            يقوم النظام بالتحقق اللحظي من كل رقم هوية وتحديد المستحقين للتسليم.
                                        </small>
                                    </div>

                                    <!-- مؤشر التحميل الجماعي -->
                                    <div id="batchLoading" class="text-center py-4" style="display: none;">
                                        <div class="spinner-border text-info" role="status"></div>
                                        <div class="mt-2 text-info font-weight-bold pulse-loader">
                                            جاري تدقيق أرقام الهويات مع الكوبونات المحددة...
                                        </div>
                                    </div>

                                    <!-- ملخص نتائج التدقيق الجماعي -->
                                    <div id="batchSummaryContainer" style="display: none;" class="mt-4">
                                        <div class="row mb-3">
                                            <div class="col-md-3 col-6 mb-2">
                                                <div class="info-box bg-light border">
                                                    <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">إجمالي الهويات</span>
                                                        <span class="info-box-number" id="batchStatTotal">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6 mb-2">
                                                <div class="info-box bg-light border">
                                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">مستحق للتسليم</span>
                                                        <span class="info-box-number text-success" id="batchStatEligible">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6 mb-2">
                                                <div class="info-box bg-light border">
                                                    <span class="info-box-icon bg-warning"><i class="fas fa-history"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">مستلم مسبقاً</span>
                                                        <span class="info-box-number text-warning" id="batchStatDelivered">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6 mb-2">
                                                <div class="info-box bg-light border">
                                                    <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">غير مضاف / مفقود</span>
                                                        <span class="info-box-number text-danger" id="batchStatNotFound">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- جدول الهويات المدققة -->
                                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                            <table class="table table-sm table-bordered table-striped bg-white">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 120px;">رقم الهوية</th>
                                                        <th style="width: 160px;">الاسم</th>
                                                        <th style="width: 140px;" class="text-center">حالة الاستحقاق</th>
                                                        <th>الطرود المفحوصة وتعديل الكميات المسلّمة (لكل فرد على حِدة)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="batchTableBody">
                                                    <!-- Dynamic rows -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- شروط وخيارات التسليم الجماعي -->
                                        <div class="p-3 bg-light border rounded mt-3">
                                            <!-- كارت تعديل كميات الطرود المختارة على حِدة للدفعة -->
                                            <div class="card card-outline card-primary mb-3 shadow-sm" id="batchParcelQuantitiesCard">
                                                <div class="card-header bg-white py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="font-weight-bold text-primary mb-0">
                                                            <i class="fas fa-boxes mr-1"></i>
                                                            تعديل الكميات المسلّمة لكل طرد مختار على حِدة (المعتمدة لهذه الدفعة):
                                                        </h6>
                                                        <span class="badge badge-info px-2 py-1" id="batchSelectedProjectsCountBadge">0 طرود مختارة</span>
                                                    </div>
                                                </div>
                                                <div class="card-body p-3 bg-light">
                                                    <div class="row" id="batchSelectedProjectsQuantities">
                                                        <!-- Dynamic inputs per parcel -->
                                                    </div>
                                                    <div class="mt-2 text-muted small">
                                                        <i class="fas fa-info-circle text-info mr-1"></i>
                                                        تغيير كمية أي طرد هنا يطبق تلقائياً على كافة الهويات في الجدول، كما يمكنك تخصيص كمية أي فرد على حِدة مباشرة في جدول المعاينة بالأعلى.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-2" id="batchGroupNameContainer">
                                                <label for="batchGroupName" class="small font-weight-bold text-primary">
                                                    <i class="fas fa-tag mr-1"></i> مسمى عملية التسليم / اسم المجموعة (اختياري - يظهر في سجل المستفيد):
                                                </label>
                                                <input type="text" id="batchGroupName" class="form-control" placeholder="مثلاً: مساعدات الشتاء، سلة الكرامة والمياه، دفعة الإيواء... (إذا تُرك فارغاً سيكون الافتراضي: تسليم مجموعة طرود)">
                                                <small class="text-muted font-weight-normal d-block mt-1">
                                                    <i class="fas fa-info-circle mr-1 text-info"></i> هذا الاسم سيظهر في كشف المستفيد وملفه الشخصي كاسم لهذه المجموعة بدلاً من "تسليم مجموعة طرود".
                                                </small>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label for="batchNotes" class="small font-weight-bold">
                                                    ملاحظات الدفعة (تضاف لكافة المستفيدين):
                                                </label>
                                                <input type="text" id="batchNotes" class="form-control" placeholder="ملاحظات التسليم الجماعي">
                                            </div>

                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="batchSkipDelivered" checked>
                                                <label class="custom-control-label small font-weight-bold" for="batchSkipDelivered">
                                                    تجاوز المستلمين مسبقاً (عدم التكرار)
                                                </label>
                                            </div>

                                            <div class="custom-control custom-checkbox mb-3">
                                                <input type="checkbox" class="custom-control-input" id="batchAutoAddMissing">
                                                <label class="custom-control-label small text-danger font-weight-bold" for="batchAutoAddMissing">
                                                    إضافة غير المضافين تلقائياً وتسليمهم مع الدفعة
                                                </label>
                                            </div>

                                            <button type="button" class="btn btn-success btn-lg font-weight-bold" id="btnSubmitBatchDelivery">
                                                <i class="fas fa-check-double mr-1"></i> تنفيذ التسليم الجماعي للهويات المستحقة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- نهاية حاوية إجراءات التسليم deliveryWorkflowContainer -->
                </div>
            </div>
        </div>

        <!-- القسم الجانبي: تعليمات وسجل العمليات -->
        <div class="col-lg-4">
            <!-- كرت ملاحظات هامة -->
            <div class="card delivery-card card-warning card-outline mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                        ملاحظات وإرشادات هامة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-warehouse mr-1"></i>
                        <strong>تنبيه:</strong> تأكد من اختيار <strong>المخزن الفرعي</strong> الصحيح الذي يتم التسليم منه.
                    </div>

                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 pb-2 border-bottom">
                            <i class="fas fa-check-circle text-success mr-1"></i>
                            <strong>طرد واحد:</strong> للتسليم الفردي لكوبون محدد من القائمة المنسدلة.
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <i class="fas fa-check-circle text-success mr-1"></i>
                            <strong>مجموعة طرود:</strong> يتيح تسليم عدة كوبونات مجتمعة للمستفيد بنقرة زر واحدة.
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <i class="fas fa-bolt text-primary mr-1"></i>
                            <strong>الفحص بالجافاسكربت:</strong> يفحص مباشرة ما إذا كان رقم الهوية مضافاً في الكوبون، ومستحقاً أو استلم مسبقاً.
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <i class="fas fa-users text-info mr-1"></i>
                            <strong>العائلات والتابعين:</strong> إذا أدخلت رقم هوية تابع، يتعرف النظام تلقائياً على رب الأسرة ويفحص استحقاق العائلة.
                        </li>
                        <li>
                            <i class="fas fa-shield-alt text-secondary mr-1"></i>
                            يتم تسجيل هوية الموزع / المستخدم الحالي وتاريخ التسليم في كل عملية.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        // CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 if available
        if ($.fn.select2) {
            $('#single_project_id').select2({
                placeholder: "-- اختر الكوبون / الطرد --",
                dir: "rtl",
                width: '100%',
                allowClear: true,
                minimumResultsForSearch: 0,
                language: {
                    noResults: function() {
                        return "لا توجد نتائج مطابقة";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    }
                }
            });
            $('#delivery_warehouse_id').select2({
                placeholder: "-- اختر المخزن الفرعي --",
                dir: "rtl",
                width: '100%',
                minimumResultsForSearch: 0
            });
        }

        // Global Map of all projects: id -> name
        const allProjectsMap = {
            @foreach($projects as $proj)
                {{ $proj->id }}: @json($proj->name),
            @endforeach
        };

        // Full metadata map of all projects
        const allProjectsData = {
            @foreach($projects as $proj)
                {{ $proj->id }}: {
                    id: {{ $proj->id }},
                    name: @json($proj->name),
                    is_cash: {{ $proj->is_cash ? 'true' : 'false' }},
                    cash_amount_type: @json($proj->cash_amount_type ?? 'fixed'),
                    cash_amount: @json($proj->cash_amount),
                    cash_currency: @json($proj->cash_currency ?? 'ILS'),
                    currency_symbol: @json($proj->currency_symbol),
                    default_delivery_method: @json($proj->default_delivery_method ?? 'cash'),
                    formatted_cash_value: @json($proj->formatted_cash_value)
                },
            @endforeach
        };

        // Global Map of Custom Projects configuration
        const customProjectsConfig = {
            @if(isset($customProjects))
                @foreach($customProjects as $cp)
                    {{ $cp->id }}: {
                        id: {{ $cp->id }},
                        name: @json($cp->name),
                        slug: @json($cp->slug),
                        code_prefix: @json($cp->code_prefix ?: 'RRM'),
                        icon: @json($cp->icon ?: 'fas fa-first-aid'),
                        color: @json($cp->color ?: 'warning'),
                        description: @json($cp->description),
                        coupon_scope: @json($cp->coupon_scope ?: 'multiple'),
                        default_project_ids: @json($cp->default_project_ids ?? []),
                        default_project_quantities: @json($cp->default_project_quantities ?? []),
                        default_sub_warehouse_id: @json($cp->default_sub_warehouse_id),
                        auto_add_if_missing: {{ $cp->auto_add_if_missing ? 'true' : 'false' }},
                        skip_already_delivered: {{ $cp->skip_already_delivered ? 'true' : 'false' }},
                        edit_url: @json(route('dashboard.custom_projects.edit', $cp->id))
                    },
                @endforeach
            @endif
        };

        // Global State
        let currentMode = null; // 'single' or 'multiple'
        let currentDeliveryCategory = null; // 'standard' or 'rrm' (null initially until user clicks)
        let currentCustomProjectId = null; // ID of active CustomProject when category === 'rrm'
        let currentResolvedData = null;
        let batchParcelQuantities = {};
        let customRowQuantities = {}; // id_num -> { project_id: qty }

        // Functions to switch delivery category & mode cleanly
        function applyCouponMode(mode) {
            currentMode = mode;
            $('input[name="coupon_scope"]').prop('checked', false);
            $('input[name="coupon_scope"][value="' + mode + '"]').prop('checked', true);

            if (mode === 'single') {
                $('#btnModeSingle')
                    .removeClass('btn-default')
                    .addClass('active btn-primary');
                $('#btnModeMultiple')
                    .removeClass('active btn-primary')
                    .addClass('btn-default');
                $('#singleCouponContainer').slideDown(200);
                $('#multipleCouponsContainer').slideUp(200);
            } else {
                $('#btnModeMultiple')
                    .removeClass('btn-default')
                    .addClass('active btn-primary');
                $('#btnModeSingle')
                    .removeClass('active btn-primary')
                    .addClass('btn-default');
                $('#singleCouponContainer').slideUp(200);
                $('#multipleCouponsContainer').slideDown(200);
            }
            if (typeof resetInstantCheck === 'function') {
                resetInstantCheck();
            }
            if (typeof renderBatchParcelControls === 'function') {
                renderBatchParcelControls();
            }
        }

        function applyDeliveryCategory(category, customProjectId = null) {
            // Reset all category toggle buttons
            $('.category-toggle-btn').removeClass('active btn-primary').addClass('btn-default');
            $('input[name="delivery_category"]').prop('checked', false);

            // Hide the initial notice and reveal workflow container smoothly
            $('#selectCategoryNotice').slideUp(200);
            $('#deliveryWorkflowContainer').slideDown(300);

            if (category === 'rrm' || String(category).startsWith('custom_')) {
                currentDeliveryCategory = 'rrm';
                if (!customProjectId && String(category).startsWith('custom_')) {
                    customProjectId = parseInt(String(category).replace('custom_', '')) || null;
                }
                if (!customProjectId) {
                    const firstKeys = Object.keys(customProjectsConfig);
                    if (firstKeys.length > 0) {
                        customProjectId = parseInt(firstKeys[0]);
                    }
                }
                currentCustomProjectId = customProjectId;

                if (customProjectId && $('#btnTypeCustomProject_' + customProjectId).length) {
                    $('#btnTypeCustomProject_' + customProjectId)
                        .removeClass('btn-default')
                        .addClass('active btn-primary')
                        .find('input[type="radio"]').prop('checked', true);
                } else {
                    $('#btnTypeRrm')
                        .removeClass('btn-default')
                        .addClass('active btn-primary')
                        .find('input[type="radio"]').prop('checked', true);
                }

                // Apply CustomProject configuration if available
                const cpConfig = customProjectId ? customProjectsConfig[customProjectId] : null;
                if (cpConfig) {
                    $('#customProjectCalloutTitle').html(
                        '<i class="' + cpConfig.icon + ' text-' + cpConfig.color + ' mr-1"></i> كشف تسليم: ' + cpConfig.name
                    );
                    $('#customProjectCalloutBadge')
                        .attr('class', 'badge badge-' + cpConfig.color + ' font-weight-bold px-2 py-1')
                        .text('بادئة الكشف: ' + cpConfig.code_prefix);
                    if (cpConfig.edit_url) {
                        $('#customProjectEditConfigLink').attr('href', cpConfig.edit_url).show();
                    }

                    // Filter #rrmBatchSelect options to only show batches of this CustomProject
                    $('#rrmBatchSelect').val('');
                    $('#rrmBatchSelect option').each(function() {
                        const val = $(this).val();
                        if (!val) return;
                        const optCpId = parseInt($(this).data('custom-project-id')) || null;
                        if (!optCpId || optCpId === customProjectId) {
                            $(this).show().prop('disabled', false);
                        } else {
                            $(this).hide().prop('disabled', true);
                        }
                    });

                    // Apply default warehouse if configured
                    if (cpConfig.default_sub_warehouse_id) {
                        $('#delivery_warehouse_id').val(cpConfig.default_sub_warehouse_id);
                    }

                    // Apply default batch switches
                    $('#batchAutoAddMissing').prop('checked', !!cpConfig.auto_add_if_missing);
                    $('#batchSkipDelivered').prop('checked', !!cpConfig.skip_already_delivered);

                    // Apply coupon scope & default parcels
                    const defPids = Array.isArray(cpConfig.default_project_ids) ? cpConfig.default_project_ids.map(id => parseInt(id)) : [];
                    const defQtys = cpConfig.default_project_quantities || {};

                    batchParcelQuantities = {};
                    Object.keys(defQtys).forEach(function(k) {
                        batchParcelQuantities[parseInt(k)] = parseInt(defQtys[k]) || 1;
                    });

                    if (cpConfig.coupon_scope === 'single') {
                        applyCouponMode('single');
                        if (defPids.length > 0) {
                            $('#single_project_id').val(defPids[0]);
                        }
                    } else {
                        applyCouponMode('multiple');
                        if (defPids.length > 0) {
                            $('.multi-coupon-checkbox').each(function() {
                                const pid = parseInt($(this).val());
                                $(this).prop('checked', defPids.includes(pid));
                            });
                            renderBatchParcelControls();
                        }
                    }
                } else {
                    applyCouponMode('multiple');
                }

                $('#rrmBatchConfigAlert').slideDown(250);
                setTimeout(function() {
                    $('#rrmBatchCustomName').focus();
                }, 350);
            } else {
                currentDeliveryCategory = 'standard';
                currentCustomProjectId = null;

                $('#btnTypeStandard')
                    .removeClass('btn-default')
                    .addClass('active btn-primary')
                    .find('input[type="radio"]').prop('checked', true);

                $('#rrmBatchConfigAlert').slideUp(250);
                if (!currentMode) {
                    applyCouponMode('single');
                }
            }
        }

        // Event listeners for Category & Mode
        $(document).on('click', '#btnTypeStandard', function() {
            applyDeliveryCategory('standard');
        });
        $(document).on('click', '#btnTypeRrm', function() {
            applyDeliveryCategory('rrm');
        });
        $(document).on('click', '.btn-custom-project', function() {
            const cpId = parseInt($(this).data('custom-project-id')) || null;
            applyDeliveryCategory('custom_' + cpId, cpId);
        });
        $('input[name="delivery_category"]').on('change', function() {
            const val = $(this).val();
            const cpId = parseInt($(this).data('custom-project-id')) || null;
            applyDeliveryCategory(val, cpId);
        });

        $(document).on('click', '#btnModeSingle', function() {
            applyCouponMode('single');
        });
        $(document).on('click', '#btnModeMultiple', function() {
            applyCouponMode('multiple');
        });
        $('input[name="coupon_scope"]').on('change', function() {
            applyCouponMode($(this).val());
        });

        // Sync RRM name input with batch group name input
        $('#rrmBatchCustomName').on('input', function() {
            $('#batchGroupName').val($(this).val());
            $('#instantGroupName').val($(this).val());
        });

        // Selecting an existing RRM batch
        $('#rrmBatchSelect').on('change', function() {
            const $selected = $(this).find('option:selected');
            const batchId = $(this).val();
            if (batchId) {
                const name = $selected.data('name') || '';
                const bDate = $selected.data('date') || '';
                const bWh = $selected.data('warehouse') || '';

                $('#rrmBatchCustomName').val(name);
                $('#batchGroupName').val(name);
                $('#instantGroupName').val(name);

                if (bDate) {
                    $('#delivery_date').val(bDate);
                }
                if (bWh) {
                    $('#delivery_warehouse_id').val(bWh);
                }

                // Auto-select the projects that belong to this batch
                const projects = $selected.data('projects') || [];
                if (Array.isArray(projects) && projects.length > 0) {
                    $('.multi-coupon-checkbox').each(function() {
                        const pid = $(this).val();
                        const isMatch = projects.includes(parseInt(pid)) || projects.includes(String(pid));
                        $(this).prop('checked', isMatch);
                    });
                    renderBatchParcelControls();
                }
            }
        });

        // Auto-select custom project if passed via URL (?custom_project_id=... or ?type=rrm)
        @if(!empty($selectedCustomProjectId))
            applyDeliveryCategory('custom_{{ (int)$selectedCustomProjectId }}', {{ (int)$selectedCustomProjectId }});
        @elseif(request()->get('type') === 'rrm')
            applyDeliveryCategory('rrm');
        @endif

        // Render parcel quantity controls for batch delivery
        function renderBatchParcelControls() {
            const selectedIds = getSelectedProjectIds();
            const $container = $('#batchSelectedProjectsQuantities');
            const $card = $('#batchParcelQuantitiesCard');
            const $badge = $('#batchSelectedProjectsCountBadge');

            if (selectedIds.length === 0) {
                $card.hide();
                $container.empty();
                return;
            }

            $card.show();
            $badge.text(selectedIds.length + ' طرد/كوبون مختار');

            let defaultVal = parseInt($('#default_quantity').val()) || 1;
            let html = '';
            selectedIds.forEach(function(pId) {
                if (batchParcelQuantities[pId] === undefined) {
                    batchParcelQuantities[pId] = defaultVal;
                }
                const pName = allProjectsMap[pId] || ('كوبون #' + pId);
                const qty = batchParcelQuantities[pId];

                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-between shadow-sm h-100">
                            <div class="mr-2" style="max-width: 62%;">
                                <strong class="d-block text-truncate text-dark" title="${pName}">
                                    <i class="fas fa-box text-primary mr-1"></i>${pName}
                                </strong>
                                <small class="text-muted">الكمية لكل فرد:</small>
                            </div>
                            <div class="input-group input-group-sm" style="width: 125px;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary btn-batch-qty-minus" data-project-id="${pId}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <input type="number" 
                                       class="form-control text-center font-weight-bold batch-parcel-global-qty" 
                                       id="batch_qty_proj_${pId}" 
                                       data-project-id="${pId}" 
                                       value="${qty}" 
                                       min="1" 
                                       max="100">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary btn-batch-qty-plus" data-project-id="${pId}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $container.html(html);
        }

        // Steppers and inputs for batch parcel quantities
        $(document).on('click', '.btn-batch-qty-plus', function() {
            const pId = $(this).data('project-id');
            const $input = $('#batch_qty_proj_' + pId);
            let val = parseInt($input.val()) || 1;
            val++;
            $input.val(val).trigger('change');
        });

        $(document).on('click', '.btn-batch-qty-minus', function() {
            const pId = $(this).data('project-id');
            const $input = $('#batch_qty_proj_' + pId);
            let val = parseInt($input.val()) || 1;
            if (val > 1) {
                val--;
                $input.val(val).trigger('change');
            }
        });

        $(document).on('change input', '.batch-parcel-global-qty', function() {
            const pId = $(this).data('project-id');
            let val = parseInt($(this).val()) || 1;
            if (val < 1) val = 1;
            $(this).val(val);
            batchParcelQuantities[pId] = val;

            // Synchronize this parcel quantity into all rows in batch table that haven't been manually customized
            $('.batch-row-parcel-qty[data-project-id="' + pId + '"]').each(function() {
                const idNum = $(this).data('id-num');
                if (!customRowQuantities[idNum] || customRowQuantities[idNum][pId] === undefined) {
                    $(this).val(val);
                }
            });
        });

        $(document).on('change input', '.batch-row-parcel-qty', function() {
            const idNum = $(this).data('id-num');
            const pId = $(this).data('project-id');
            let val = parseInt($(this).val()) || 1;
            if (val < 1) val = 1;
            $(this).val(val);

            if (!customRowQuantities[idNum]) {
                customRowQuantities[idNum] = {};
            }
            customRowQuantities[idNum][pId] = val;
            $(this).addClass('border-primary text-primary font-weight-bold');
        });

        // Default quantity input at top of page
        $('#default_quantity').on('change input', function() {
            let defVal = parseInt($(this).val()) || 1;
            if (defVal < 1) defVal = 1;
            const selectedIds = getSelectedProjectIds();
            selectedIds.forEach(function(pId) {
                batchParcelQuantities[pId] = defVal;
                $('#batch_qty_proj_' + pId).val(defVal);
                $('.batch-row-parcel-qty[data-project-id="' + pId + '"]').each(function() {
                    const idNum = $(this).data('id-num');
                    if (!customRowQuantities[idNum] || customRowQuantities[idNum][pId] === undefined) {
                        $(this).val(defVal);
                    }
                });
            });
        });

        // فلتر البحث لمجموعة الكوبونات
        $('#filterMultipleCouponsInput').on('input', function() {
            let term = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.coupon-item-col').each(function() {
                let name = ($(this).data('coupon-name') || $(this).find('.coupon-title').text() || '').toLowerCase();
                if (!term || name.indexOf(term) !== -1) {
                    $(this).show();
                    matchCount++;
                } else {
                    $(this).hide();
                }
            });

            $('#filteredCouponsCount').text(matchCount);
            if (matchCount === 0) {
                $('#noMatchingCouponsNotice').show();
            } else {
                $('#noMatchingCouponsNotice').hide();
            }
        });

        $('#btnClearMultipleFilter').on('click', function() {
            $('#filterMultipleCouponsInput').val('').trigger('input').focus();
        });


        $('#single_project_id').on('change', function() {
            resetInstantCheck();
            renderBatchParcelControls();
            updateCashDeliveryOptions();
        });

        $('#selectAllCouponsBtn').on('click', function() {
            $('.coupon-item-col:visible .multi-coupon-checkbox').prop('checked', true).closest('.coupon-check-item').addClass('selected');
            resetInstantCheck();
            renderBatchParcelControls();
            updateCashDeliveryOptions();
        });

        $('#deselectAllCouponsBtn').on('click', function() {
            $('.coupon-item-col:visible .multi-coupon-checkbox').prop('checked', false).closest('.coupon-check-item').removeClass('selected');
            resetInstantCheck();
            renderBatchParcelControls();
            updateCashDeliveryOptions();
        });

        $('.multi-coupon-checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.coupon-check-item').addClass('selected');
            } else {
                $(this).closest('.coupon-check-item').removeClass('selected');
            }
            resetInstantCheck();
            renderBatchParcelControls();
            updateCashDeliveryOptions();
        });

        // Initialize batch parcel controls on load
        renderBatchParcelControls();
        updateCashDeliveryOptions();

        // Helper: Get currently selected project IDs
        function getSelectedProjectIds() {
            if (currentMode === 'single') {
                const val = $('#single_project_id').val();
                return val ? [val] : [];
            } else if (currentMode === 'multiple') {
                const selected = [];
                $('.multi-coupon-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                return selected;
            }
            return [];
        }

        function updateCashDeliveryOptions() {
            const projectIds = getSelectedProjectIds();
            let hasAnyCash = false;
            let allAreCash = projectIds.length > 0;
            let cashInfoList = [];

            projectIds.forEach(function(pId) {
                const proj = allProjectsData[pId];
                if (proj && proj.is_cash) {
                    hasAnyCash = true;
                    if (proj.cash_amount_type === 'custom') {
                        cashInfoList.push(`<strong>${proj.name}</strong>: منفصل يحدد عند التسليم (${proj.currency_symbol || '₪'})`);
                    } else {
                        cashInfoList.push(`<strong>${proj.name}</strong>: موحد ${proj.cash_amount || 0} ${proj.currency_symbol || '₪'}`);
                    }
                } else {
                    allAreCash = false;
                }
            });

            if (projectIds.length === 0) {
                allAreCash = false;
            }

            if (hasAnyCash) {
                $('#cashDeliveryOptionsSection').slideDown(200);
                $('#cashProjectInfoText').html(cashInfoList.join(' | '));
                const firstCashProj = projectIds.map(id => allProjectsData[id]).find(p => p && p.is_cash);
                if (firstCashProj) {
                    if (firstCashProj.default_delivery_method) {
                        $('#cash_delivery_method').val(firstCashProj.default_delivery_method);
                    }
                    if (firstCashProj.cash_amount_type === 'fixed' && firstCashProj.cash_amount) {
                        $('#cash_global_amount').val(firstCashProj.cash_amount);
                    }
                }
            } else {
                $('#cashDeliveryOptionsSection').slideUp(200);
            }

            if (allAreCash) {
                $('#warehouseRequiredStar').hide();
                $('#warehouseOptionalBadge').show();
            } else {
                $('#warehouseRequiredStar').show();
                $('#warehouseOptionalBadge').hide();
            }
        }

        function resetInstantCheck() {
            $('#instantResultContainer').slideUp(150);
            $('#instantNotFoundAlert').slideUp(150);
            currentResolvedData = null;
        }

        $('#btnClearScanner').on('click', function() {
            $('#scannerIdInput').val('').focus();
            resetInstantCheck();
        });

        // Scanner Auto-Check on typing (without any button!)
        let checkDebounceTimer = null;
        $('#scannerIdInput').on('input', function() {
            const raw = $(this).val().trim();
            const clean = raw.replace(/\D/g, '');
            if (clean.length === 0) {
                resetInstantCheck();
                return;
            }

            clearTimeout(checkDebounceTimer);
            // Automatic check when 8 or 9 digits entered
            if (clean.length >= 8) {
                checkDebounceTimer = setTimeout(function() {
                    performInstantCheck(clean);
                }, 150);
            }
        });

        $('#scannerIdInput').on('keypress paste', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const raw = $(this).val().trim().replace(/\D/g, '');
                if (raw.length >= 7) {
                    clearTimeout(checkDebounceTimer);
                    performInstantCheck(raw);
                }
            }
        });

        // Perform Instant JS Check
        function performInstantCheck(idNum) {
            const projectIds = getSelectedProjectIds();
            if (projectIds.length === 0) {
                alert(currentMode === 'single' ? 'يرجى اختيار الكوبون أولاً من القائمة' : 'يرجى تحديد كوبون واحد على الأقل من القائمة');
                return;
            }

            resetInstantCheck();
            $('#instantLoading').show();

            $.ajax({
                url: "{{ route('dashboard.projects.deliveries.check-id') }}",
                type: "POST",
                data: {
                    id_num: idNum,
                    project_ids: projectIds
                },
                success: function(res) {
                    $('#instantLoading').hide();

                    if (!res.success || !res.exists) {
                        $('#notFoundTitle').text('رقم الهوية (' + idNum + ') غير مسجل');
                        $('#notFoundText').text(res.message || 'هذا الرقم غير مسجل في قاعدة بيانات النظام.');
                        $('#instantNotFoundAlert').slideDown(200);
                        return;
                    }

                    // Success: Person exists!
                    currentResolvedData = res;
                    renderInstantResults(res);
                },
                error: function(xhr) {
                    $('#instantLoading').hide();
                    let msg = 'حدث خطأ أثناء فحص الهوية بالجافاسكربت.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        }

        // Render Instant Results
        function renderInstantResults(res) {
            const person = res.person;
            const coupons = res.coupons;
            const summary = res.summary;

            // Fill Person Info
            $('#resPersonName').text(person.name);
            $('#resPersonId').text(person.id_num);
            $('#resPersonPhone').text(person.phone || '-');
            $('#resFamilyCount').text(person.family_count);
            $('#resTypeBadge').text(person.type_label);
            $('#resBlockBadge').text(person.block_name || 'بدون مربع');

            if (person.is_relative) {
                $('#resHeadName').text(person.head_name);
                $('#resHeadId').text(person.head_id_num);
                $('#resRelativeAlert').show();
            } else {
                $('#resRelativeAlert').hide();
            }

            // Fill Coupons Table with granular per-coupon actions
            let tbodyHtml = '';
            coupons.forEach(function(cp) {
                let badgeClass = 'badge-danger';
                let icon = '<i class="fas fa-times-circle text-danger"></i>';

                if (cp.status === 'مستلم') {
                    badgeClass = 'badge-warning';
                    icon = '<i class="fas fa-check-circle text-warning"></i>';
                } else if (cp.status === 'غير مستلم') {
                    badgeClass = 'badge-success';
                    icon = '<i class="fas fa-box text-success"></i>';
                }

                let extraInfo = '';
                if (cp.is_added) {
                    if (cp.status === 'مستلم') {
                        extraInfo = '<small class="text-muted d-block"><i class="fas fa-calendar"></i> استلم بتاريخ: ' + (cp.delivery_date || '-') + '</small>';
                    }
                } else {
                    extraInfo = '<small class="text-danger d-block">غير مدرج في كشوفات هذا الكوبون</small>';
                }

                // Cash specific controls
                let cashControls = '';
                if (cp.is_cash) {
                    let amtInput = '';
                    if (cp.cash_amount_type === 'custom') {
                        amtInput = `
                            <div class="input-group input-group-sm mt-1" style="width: 140px;">
                                <div class="input-group-prepend"><span class="input-group-text px-1 small font-weight-bold">${cp.currency_symbol || '₪'}</span></div>
                                <input type="number" step="0.01" class="form-control form-control-sm text-center font-weight-bold instant-cash-input" id="act_cash_${cp.project_id}" value="${cp.cash_amount || ''}" placeholder="المبلغ">
                            </div>
                        `;
                    }
                    let methodSelect = `
                        <select class="form-control form-control-sm mt-1 instant-method-select" id="act_method_${cp.project_id}" style="width: 140px;">
                            <option value="cash" ${(cp.delivery_method || 'cash') === 'cash' ? 'selected' : ''}>كاش (نقداً)</option>
                            <option value="wallet" ${cp.delivery_method === 'wallet' ? 'selected' : ''}>محفظة إلكترونية</option>
                            <option value="bank" ${cp.delivery_method === 'bank' ? 'selected' : ''}>حساب بنكي</option>
                        </select>
                    `;
                    let detailsInput = `
                        <input type="text" class="form-control form-control-sm mt-1 instant-details-input" id="act_details_${cp.project_id}" value="${cp.delivery_details || ''}" placeholder="بيانات المحفظة/البنك" style="width: 140px;">
                    `;
                    cashControls = `<div class="d-flex flex-column align-items-start mt-1">${amtInput}${methodSelect}${detailsInput}</div>`;
                }

                // Action cell per user's requirement:
                // 1. If already delivered: allow increasing quantity (+1 or custom)
                // 2. If not added: allow adding to this coupon and delivering it
                // 3. If eligible: allow delivering now
                let actionCell = '';
                if (cp.status === 'مستلم') {
                    actionCell = `
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-checkbox mr-2">
                                    <input type="checkbox" class="custom-control-input coupon-action-checkbox" 
                                           id="chk_act_${cp.project_id}" 
                                           data-project-id="${cp.project_id}" 
                                           data-action="increase" 
                                           data-current-qty="${cp.quantity}">
                                    <label class="custom-control-label font-weight-bold text-warning cursor-pointer" for="chk_act_${cp.project_id}">
                                        <i class="fas fa-plus-circle mr-1"></i> ${cp.is_cash ? 'تسليم إضافي' : 'زيادة الكمية'}
                                    </label>
                                </div>
                                ${!cp.is_cash ? `
                                <div class="input-group input-group-sm ml-2" style="width: 105px;">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">+</span></div>
                                    <input type="number" class="form-control text-center font-weight-bold inc-qty-input" id="inc_qty_${cp.project_id}" value="1" min="1" max="20" title="الكمية الإضافية">
                                </div>` : ''}
                            </div>
                            ${cashControls}
                        </div>
                    `;
                } else if (cp.status === 'غير مضاف') {
                    actionCell = `
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-checkbox mr-2">
                                    <input type="checkbox" class="custom-control-input coupon-action-checkbox" 
                                           id="chk_act_${cp.project_id}" 
                                           data-project-id="${cp.project_id}" 
                                           data-action="add">
                                    <label class="custom-control-label font-weight-bold text-danger cursor-pointer" for="chk_act_${cp.project_id}">
                                        <i class="fas fa-user-plus mr-1"></i> إضافة وتسليم
                                    </label>
                                </div>
                                ${!cp.is_cash ? `
                                <div class="input-group input-group-sm ml-2" style="width: 110px;">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold small">الكمية</span></div>
                                    <input type="number" class="form-control text-center font-weight-bold act-qty-input" id="act_qty_${cp.project_id}" value="${batchParcelQuantities[cp.project_id] || 1}" min="1" max="100" title="الكمية المراد تسليمها">
                                </div>` : ''}
                            </div>
                            ${cashControls}
                        </div>
                    `;
                } else {
                    // غير مستلم (مستحق)
                    actionCell = `
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-checkbox mr-2">
                                    <input type="checkbox" class="custom-control-input coupon-action-checkbox" 
                                           id="chk_act_${cp.project_id}" 
                                           data-project-id="${cp.project_id}" 
                                           data-action="deliver" 
                                           checked>
                                    <label class="custom-control-label font-weight-bold text-success cursor-pointer" for="chk_act_${cp.project_id}">
                                        <i class="fas fa-check-circle mr-1"></i> تسليم الآن
                                    </label>
                                </div>
                                ${!cp.is_cash ? `
                                <div class="input-group input-group-sm ml-2" style="width: 110px;">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold small">الكمية</span></div>
                                    <input type="number" class="form-control text-center font-weight-bold act-qty-input" id="act_qty_${cp.project_id}" value="${batchParcelQuantities[cp.project_id] || cp.quantity || 1}" min="1" max="100" title="الكمية المراد تسليمها">
                                </div>` : ''}
                            </div>
                            ${cashControls}
                        </div>
                    `;
                }

                let cashTag = '';
                if (cp.is_cash) {
                    cashTag = '<br><span class="badge badge-success small"><i class="fas fa-money-bill-wave mr-1"></i>كوبون نقدي</span>';
                }

                let qtyDisplay = cp.quantity;
                if (cp.is_cash) {
                    if (cp.cash_amount) {
                        qtyDisplay = `<span class="badge badge-dark" style="font-size: 0.9rem;">${cp.cash_amount} ${cp.currency_symbol || '₪'}</span>`;
                    } else if (cp.cash_amount_type === 'custom') {
                        qtyDisplay = `<span class="badge badge-warning" style="font-size: 0.75rem;">يحدد عند التسليم</span>`;
                    } else {
                        qtyDisplay = `<span class="badge badge-dark" style="font-size: 0.9rem;">${cp.project_cash_amount || 0} ${cp.currency_symbol || '₪'}</span>`;
                    }
                }

                let whDisplay = cp.sub_warehouse_name || '-';
                if (cp.is_cash) {
                    let method = cp.delivery_method || 'cash';
                    let mLabel = 'كاش (نقداً)';
                    let mBadge = 'badge-success';
                    if (method === 'wallet') {
                        mLabel = 'محفظة إلكترونية';
                        mBadge = 'badge-info';
                    } else if (method === 'bank') {
                        mLabel = 'حساب بنكي';
                        mBadge = 'badge-primary';
                    }
                    whDisplay = `<span class="badge ${mBadge}"><i class="fas fa-hand-holding-usd mr-1"></i>${mLabel}</span>`;
                    if (cp.delivery_details) {
                        whDisplay += `<br><small class="text-muted"><i class="fas fa-info-circle mr-1"></i>${cp.delivery_details}</small>`;
                    }
                }

                tbodyHtml += '<tr>';
                tbodyHtml += '<td><strong>' + cp.project_name + '</strong>' + cashTag + '</td>';
                tbodyHtml += '<td class="text-center">' + icon + ' <span class="badge ' + badgeClass + ' p-2">' + cp.status_label + '</span>' + extraInfo + '</td>';
                tbodyHtml += '<td class="text-center font-weight-bold" id="row_qty_' + cp.project_id + '">' + qtyDisplay + '</td>';
                tbodyHtml += '<td>' + whDisplay + '</td>';
                tbodyHtml += '<td>' + actionCell + '</td>';
                tbodyHtml += '</tr>';
            });
            $('#couponsStatusTableBody').html(tbodyHtml);

            updateSelectedActionsState();
            $('#instantResultContainer').slideDown(250);
        }

        // Listener for changes on coupon action checkboxes
        $(document).on('change', '.coupon-action-checkbox', function() {
            updateSelectedActionsState();
        });

        function updateSelectedActionsState() {
            const checkedCount = $('.coupon-action-checkbox:checked').length;
            $('#selectedCountBadge').text(checkedCount);
            if (checkedCount > 0) {
                $('#actionsApplyCard').slideDown(150);
            } else {
                $('#actionsApplyCard').slideUp(150);
            }
        }

        // Submit Selected Actions (Add to selected coupons OR Increase quantity for delivered coupons)
        $('#btnSubmitSelectedActions').on('click', function() {
            if (!currentResolvedData || !currentResolvedData.person) {
                alert('لا يوجد مستفيد محدد للتسليم');
                return;
            }

            const actions = [];
            const projectIds = [];
            $('.coupon-action-checkbox:checked').each(function() {
                const pId = $(this).data('project-id');
                const actType = $(this).data('action');
                let targetQty = 1;
                let addQty = 1;
                if (actType === 'increase') {
                    addQty = parseInt($('#inc_qty_' + pId).val()) || 1;
                    targetQty = addQty;
                } else {
                    targetQty = parseInt($('#act_qty_' + pId).val()) || 1;
                    addQty = targetQty;
                }

                const cashAmt = $('#act_cash_' + pId).val() || $('#cash_global_amount').val();
                const delivMethod = $('#act_method_' + pId).val() || $('#cash_delivery_method').val();
                const delivDetails = $('#act_details_' + pId).val() || $('#cash_delivery_details').val();

                actions.push({
                    project_id: pId,
                    action: actType,
                    quantity: targetQty,
                    additional_quantity: addQty,
                    cash_amount: cashAmt,
                    delivery_method: delivMethod,
                    delivery_details: delivDetails
                });
                projectIds.push(pId);
            });

            if (actions.length === 0) {
                alert('يرجى تحديد خيار واحد على الأقل من الجدول');
                return;
            }

            const warehouseId = $('#delivery_warehouse_id').val();
            const deliveryDate = $('#delivery_date').val();
            const notes = $('#instantNotes').val();
            const groupName = ($('#rrmBatchCustomName').val().trim() || $('#instantGroupName').val().trim());
            const isRrm = (currentDeliveryCategory === 'rrm') ? 1 : 0;
            const rrmBatchName = $('#rrmBatchCustomName').val().trim() || groupName;

            const allSelectedAreCash = actions.every(a => allProjectsData[a.project_id] && allProjectsData[a.project_id].is_cash);

            if (!warehouseId && !allSelectedAreCash) {
                alert('يرجى اختيار المخزن الفرعي قبل حفظ العملية');
                $('#delivery_warehouse_id').focus();
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري حفظ الإجراءات...');

            $.ajax({
                url: "{{ route('dashboard.projects.deliveries.deliver') }}",
                type: "POST",
                data: {
                    project_ids: projectIds,
                    id_nums: currentResolvedData.person.id_num,
                    actions: actions,
                    sub_warehouse_id: warehouseId,
                    delivery_date: deliveryDate,
                    delivery_method: $('#cash_delivery_method').val(),
                    delivery_details: $('#cash_delivery_details').val(),
                    cash_amount: $('#cash_global_amount').val(),
                    notes: notes,
                    group_name: groupName,
                    is_rrm: isRrm,
                    custom_project_id: currentCustomProjectId,
                    rrm_batch_id: $('#rrmBatchSelect').val() || null,
                    rrm_batch_name: rrmBatchName,
                    delivery_mode: currentMode
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> تطبيق الإجراءات المحددة (<span id="selectedCountBadge">0</span>)');

                    if (res.success) {
                        alert('✅ ' + res.message);
                        performInstantCheck(currentResolvedData.person.id_num);
                    } else {
                        alert('حدث خطأ: ' + res.message);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> تطبيق الإجراءات المحددة (<span id="selectedCountBadge">0</span>)');
                    let msg = 'حدث خطأ أثناء حفظ الإجراءات.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert('❌ ' + msg);
                }
            });
        });



        // ========================= الباتش: التدقيق الجماعي =========================
        $('#btnRunBatchCheck').on('click', function() {
            const rawText = $('#batchIdNums').val().trim();
            if (!rawText) {
                alert('يرجى إدخال قائمة أرقام الهويات أولاً');
                $('#batchIdNums').focus();
                return;
            }

            const projectIds = getSelectedProjectIds();
            if (projectIds.length === 0) {
                alert('يرجى تحديد الكوبونات المراد فحصها أولاً');
                return;
            }

            $('#batchSummaryContainer').hide();
            $('#batchLoading').show();

            $.ajax({
                url: "{{ route('dashboard.projects.deliveries.check-batch') }}",
                type: "POST",
                data: {
                    id_nums: rawText,
                    project_ids: projectIds
                },
                success: function(res) {
                    $('#batchLoading').hide();
                    if (!res.success) {
                        alert('حدث خطأ أثناء فحص الدفعة');
                        return;
                    }

                    renderBatchResults(res);
                },
                error: function(xhr) {
                    $('#batchLoading').hide();
                    let msg = 'حدث خطأ أثناء تدقيق القائمة.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });

        function renderBatchResults(res) {
            const summary = res.summary;
            const rows = res.rows;

            $('#batchStatTotal').text(summary.total);
            $('#batchStatEligible').text(summary.eligible);
            $('#batchStatDelivered').text(summary.delivered);
            $('#batchStatNotFound').text(summary.not_added + summary.not_found);

            // Re-render batch parcel controls for selected coupons
            renderBatchParcelControls();

            let tableHtml = '';
            rows.forEach(function(row) {
                let badgeClass = 'badge-' + row.status_class;
                let relInfo = row.is_relative ? '<br><small class="text-warning"><i class="fas fa-link mr-1"></i>تابع لـ: ' + row.head_name + '</small>' : '';

                let detailsHtml = '';
                if (row.details && row.details.length > 0) {
                    detailsHtml = '<div class="d-flex flex-wrap align-items-center">';
                    row.details.forEach(function(d) {
                        let statusClass = 'badge-secondary';
                        let icon = 'fa-box';
                        if (d.status === 'مستلم') {
                            statusClass = 'badge-warning';
                            icon = 'fa-check-circle';
                        } else if (d.status === 'غير مستلم') {
                            statusClass = 'badge-success';
                            icon = 'fa-box-open';
                        } else if (d.status === 'غير مضاف') {
                            statusClass = 'badge-danger';
                            icon = 'fa-plus-circle';
                        }

                        // Determine initial quantity: custom if already set, or from batchParcelQuantities, or default
                        let currentQty = 1;
                        if (customRowQuantities[row.id_num] && customRowQuantities[row.id_num][d.project_id] !== undefined) {
                            currentQty = customRowQuantities[row.id_num][d.project_id];
                        } else if (batchParcelQuantities[d.project_id] !== undefined) {
                            currentQty = batchParcelQuantities[d.project_id];
                        } else if (d.quantity) {
                            currentQty = d.quantity;
                        }

                        let isCustom = (customRowQuantities[row.id_num] && customRowQuantities[row.id_num][d.project_id] !== undefined);

                        let cashInputHtml = '';
                        if (d.is_cash) {
                            if (d.cash_amount_type === 'custom') {
                                cashInputHtml = `
                                    <div class="input-group input-group-sm mr-2" style="width: 125px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text px-1 small font-weight-bold">${d.currency_symbol || '₪'}</span>
                                        </div>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control form-control-sm text-center font-weight-bold batch-row-cash-amt" 
                                               data-id-num="${row.id_num}" 
                                               data-project-id="${d.project_id}" 
                                               value="${d.cash_amount || ''}" 
                                               placeholder="المبلغ">
                                    </div>
                                `;
                            } else {
                                cashInputHtml = `
                                    <span class="badge badge-dark mr-2" style="font-size: 0.85rem;">
                                        ${d.cash_amount} ${d.currency_symbol || '₪'}
                                    </span>
                                `;
                            }
                        }

                        detailsHtml += `
                            <div class="border rounded p-1 px-2 bg-white shadow-sm mr-2 mb-2 d-inline-flex align-items-center">
                                <span class="font-weight-bold text-dark mr-2" style="font-size: 0.88rem;">
                                    <i class="fas ${icon} text-info mr-1"></i>${d.project_name}
                                    ${d.is_cash ? '<span class="badge badge-success small mr-1"><i class="fas fa-money-bill-wave"></i> نقدي</span>' : ''}
                                </span>
                                <span class="badge ${statusClass} mr-2">${d.status}</span>
                                ${cashInputHtml}
                                ${!d.is_cash ? `
                                <div class="input-group input-group-sm" style="width: 110px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text px-1 small font-weight-bold">الكمية:</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control form-control-sm text-center font-weight-bold batch-row-parcel-qty ${isCustom ? 'border-primary text-primary' : ''}" 
                                           data-id-num="${row.id_num}" 
                                           data-project-id="${d.project_id}" 
                                           value="${currentQty}" 
                                           min="1" 
                                           max="100" 
                                           title="تعديل كمية ${d.project_name} لهذا المستفيد">
                                </div>` : ''}
                            </div>
                        `;
                    });
                    detailsHtml += '</div>';
                }

                tableHtml += '<tr>';
                tableHtml += '<td style="vertical-align: middle;"><strong>' + row.id_num + '</strong></td>';
                tableHtml += '<td style="vertical-align: middle;"><strong>' + row.name + '</strong>' + relInfo + '</td>';
                tableHtml += '<td class="text-center" style="vertical-align: middle;"><span class="badge ' + badgeClass + ' p-2">' + row.status_label + '</span></td>';
                tableHtml += '<td style="vertical-align: middle;">' + (detailsHtml || '<span class="text-muted">-</span>') + '</td>';
                tableHtml += '</tr>';
            });

            $('#batchTableBody').html(tableHtml);
            $('#batchSummaryContainer').slideDown(250);
        }

        // Submit Batch Delivery
        $('#btnSubmitBatchDelivery').on('click', function() {
            const rawText = $('#batchIdNums').val().trim();
            if (!rawText) {
                alert('يرجى إدخال أرقام الهويات');
                return;
            }

            const projectIds = getSelectedProjectIds();
            const warehouseId = $('#delivery_warehouse_id').val();
            const deliveryDate = $('#delivery_date').val();
            const defaultQty = $('#default_quantity').val() || 1;
            const notes = $('#batchNotes').val();
            const isRrm = (currentDeliveryCategory === 'rrm') ? 1 : 0;
            const rrmBatchName = $('#rrmBatchCustomName').val().trim();
            const groupName = rrmBatchName || $('#batchGroupName').val().trim();
            const autoAdd = $('#batchAutoAddMissing').is(':checked') ? 1 : 0;
            const skipDelivered = $('#batchSkipDelivered').is(':checked') ? 1 : 0;

            const allAreCash = projectIds.length > 0 && projectIds.every(id => allProjectsData[id] && allProjectsData[id].is_cash);

            if (!warehouseId && !allAreCash) {
                alert('يرجى اختيار المخزن الفرعي قبل التسليم');
                $('#delivery_warehouse_id').focus();
                return;
            }

            // Collect project quantities
            const projectQuantities = {};
            $('.batch-parcel-global-qty').each(function() {
                const pId = $(this).data('project-id');
                projectQuantities[pId] = parseInt($(this).val()) || 1;
            });

            // Collect custom quantities per row
            const customQuantities = {};
            $('.batch-row-parcel-qty').each(function() {
                const idNum = $(this).data('id-num');
                const pId = $(this).data('project-id');
                const qty = parseInt($(this).val()) || 1;
                if (!customQuantities[idNum]) {
                    customQuantities[idNum] = {};
                }
                customQuantities[idNum][pId] = qty;
            });

            // Collect custom cash amounts per row
            const customCashAmounts = {};
            $('.batch-row-cash-amt').each(function() {
                const idNum = $(this).data('id-num');
                const pId = $(this).data('project-id');
                const val = $(this).val();
                if (val !== '') {
                    if (!customCashAmounts[idNum]) customCashAmounts[idNum] = {};
                    customCashAmounts[idNum][pId] = val;
                }
            });

            const activeCpName = (currentCustomProjectId && customProjectsConfig[currentCustomProjectId])
                ? customProjectsConfig[currentCustomProjectId].name
                : 'مشروع الـ RRM';
            const confirmMsg = isRrm 
                ? 'هل أنت متأكد من تنفيذ تسليم كشف ' + activeCpName + ' (' + (groupName || 'كشف جديد') + ') للهويات المحددة؟'
                : 'هل أنت متأكد من تنفيذ التسليم الجماعي للهويات المحددة؟';

            if (!confirm(confirmMsg)) {
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري تنفيذ التسليم...');

            $.ajax({
                url: "{{ route('dashboard.projects.deliveries.deliver') }}",
                type: "POST",
                data: {
                    project_ids: projectIds,
                    id_nums: rawText,
                    sub_warehouse_id: warehouseId,
                    delivery_date: deliveryDate,
                    quantity: defaultQty,
                    project_quantities: projectQuantities,
                    custom_quantities: customQuantities,
                    custom_cash_amounts: customCashAmounts,
                    delivery_method: $('#cash_delivery_method').val(),
                    delivery_details: $('#cash_delivery_details').val(),
                    cash_amount: $('#cash_global_amount').val(),
                    notes: notes,
                    group_name: groupName,
                    is_rrm: isRrm,
                    custom_project_id: currentCustomProjectId,
                    rrm_batch_id: $('#rrmBatchSelect').val() || null,
                    rrm_batch_name: rrmBatchName,
                    auto_add_if_missing: autoAdd,
                    skip_already_delivered: skipDelivered,
                    delivery_mode: currentMode
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> تنفيذ التسليم الجماعي للهويات المستحقة');

                    if (res.success) {
                        $('#batchIdNums').val('');
                        $('#batchSummaryContainer').slideUp(200);
                        customRowQuantities = {};

                        if (res.is_rrm && res.rrm_batch_url) {
                            if (confirm('🎉 ' + res.message + '\n\nهل ترغب بالانتقال الآن لعرض كشف المستفيدين في قسم الـ RRM؟')) {
                                window.location.href = res.rrm_batch_url;
                                return;
                            }
                        } else {
                            alert('🎉 ' + res.message);
                        }
                    } else {
                        alert('حدث خطأ: ' + res.message);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> تنفيذ التسليم الجماعي للهويات المستحقة');
                    let msg = 'حدث خطأ أثناء تنفيذ التسليم الجماعي.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert('❌ ' + msg);
                }
            });
        });
    });
    </script>
    @endpush

</x-layout>
