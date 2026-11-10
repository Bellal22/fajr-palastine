@extends('layouts.adminlte3.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title fw-bold"><i class="fas fa-shield-alt text-primary mr-2"></i> المركز الذكي للتدقيق والتحليل الاجتماعي</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.audit.outliers') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 mr-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> تحليل القيم الشاذة والمخاطر
                        </a>
                        <span class="badge badge-light border">{{ now()->format('Y-m-d') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.audit.index') }}" method="GET" id="auditFilterForm">
                        <div class="row">
                        <input type="hidden" name="category" id="filterCategoryInput" value="{{ request('category') }}">
                        <input type="hidden" name="unserved" id="filterUnservedInput" value="{{ request('unserved') }}">
                        <input type="hidden" name="page" id="filterPageInput" value="{{ request('page', 1) }}">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small fw-bold">مسؤولو المناطق (يمكنك تحديد أكثر من منطقة)</label>
                                <select name="area_responsible_id[]" class="form-control select2" multiple data-placeholder="-- اختر المناطق --" style="width: 100%;">
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ in_array($area->id, (array)request('area_responsible_id')) ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small fw-bold">المربع / المندوب</label>
                                <select name="block_id" class="form-control select2" onchange="$('#auditFilterForm').submit()">
                                    <option value="">-- كل المربعات --</option>
                                    @foreach($blocks as $block)
                                        <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                            {{ $block->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block mb-3 shadow-sm">
                                <i class="fas fa-sync-alt mr-1"></i> تحديث وتحليل البيانات
                            </button>
                        </div>

                        <!-- Collapse Button for Custom Criteria -->
                        <div class="col-12 mt-2">
                            <hr class="my-2">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold collapsed text-primary" data-toggle="collapse" data-target="#customCriteriaPanel" aria-expanded="false" aria-controls="customCriteriaPanel">
                                <i class="fas fa-sliders-h mr-1"></i> إعدادات متقدمة: تعديل معايير ودرجات التقييم الذكي...
                            </button>
                        </div>

                        <!-- Collapse Panel -->
                        <div class="collapse col-12 mt-3 {{ request()->has('age_threshold') ? 'show' : '' }}" id="customCriteriaPanel">
                            <div class="card card-body bg-light border-0 shadow-none p-3 mb-0" style="border-radius: 8px;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">معيار كبار السن (العمر)</label>
                                            <input type="number" name="age_threshold" class="form-control form-control-sm" value="{{ request('age_threshold', 60) }}" min="40" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">نقاط الرضيع (أقل من سنتين)</label>
                                            <input type="number" name="infant_score" class="form-control form-control-sm" value="{{ request('infant_score', 18) }}" min="0" max="50">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">نقاط الطفل (من 2 إلى 5 سنوات)</label>
                                            <input type="number" name="child_score" class="form-control form-control-sm" value="{{ request('child_score', 10) }}" min="0" max="50">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">نقاط خيمة / مركز إيواء</label>
                                            <input type="number" name="tent_score" class="form-control form-control-sm" value="{{ request('tent_score', 15) }}" min="0" max="50">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">نقاط مسكن مدمر كلياً</label>
                                            <input type="number" name="destroyed_score" class="form-control form-control-sm" value="{{ request('destroyed_score', 20) }}" min="0" max="50">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">الحد الأدنى للفئة A (أولوية قصوى)</label>
                                            <input type="number" name="cat_a_min" class="form-control form-control-sm" value="{{ request('cat_a_min', 85) }}" min="1" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">الحد الأدنى للفئة B (حالات حرجة)</label>
                                            <input type="number" name="cat_b_min" class="form-control form-control-sm" value="{{ request('cat_b_min', 60) }}" min="1" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">الحد الأدنى للفئة C (متوسطة)</label>
                                            <input type="number" name="cat_c_min" class="form-control form-control-sm" value="{{ request('cat_c_min', 35) }}" min="1" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small fw-bold text-secondary">كلمات مفتاحية إضافية للفئة A (افصل بينها بـ |)</label>
                                            <input type="text" name="custom_keywords" class="form-control form-control-sm text-right" value="{{ request('custom_keywords') }}" placeholder="مثال: بتر جزئي|فقدان البصر|كلي">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" class="custom-control-input" id="forceIncapacitatedSwitch" name="force_incapacitated" value="1" {{ (request()->has('area_responsible_id') ? request('force_incapacitated') === '1' : true) ? 'checked' : '' }}>
                                            <label class="custom-control-label small fw-bold text-secondary" for="forceIncapacitatedSwitch">تفعيل معيار الاستثناء الطبي (عجز الوالدين يمنح الفئة A فوراً)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" class="custom-control-input" id="checkAdultSonSwitch" name="check_adult_son" value="1" {{ (request()->has('area_responsible_id') ? request('check_adult_son') === '1' : true) ? 'checked' : '' }}>
                                            <label class="custom-control-label small fw-bold text-secondary" for="checkAdultSonSwitch">اعتبار الابن البالغ (18+) كمعيل بديل في حال عجز الوالدين</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(request('area_responsible_id'))
            <!-- Category Boxes -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger shadow-sm">
                        <div class="inner text-center">
                            <h3>الفئة A ({{ $stats['A'] }})</h3>
                            <p>أولوية قصوى</p>
                        </div>
                        <a href="#" class="small-box-footer filter-category-btn" data-category="A">
                            اضغط للفلترة <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner text-center">
                            <h3>الفئة B ({{ $stats['B'] }})</h3>
                            <p>حالات حرجة</p>
                        </div>
                        <a href="#" class="small-box-footer filter-category-btn" data-category="B">
                            اضغط للفلترة <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner text-center">
                            <h3>الفئة C ({{ $stats['C'] }})</h3>
                            <p>حالات متوسطة</p>
                        </div>
                        <a href="#" class="small-box-footer filter-category-btn" data-category="C">
                            اضغط للفلترة <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner text-center">
                            <h3>الفئة D ({{ $stats['D'] }})</h3>
                            <p>حالات عادية</p>
                        </div>
                        <a href="#" class="small-box-footer filter-category-btn" data-category="D">
                            اضغط للفلترة <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title fw-bold"><i class="fas fa-table mr-2"></i> قوائم العائلات حسب الأولوية <span class="badge badge-primary px-3 py-2 ml-2" style="font-size: 85%; font-weight: 800; border-radius: 20px;">{{ $families->total() }} عائلة</span></h3>
                    <div class="card-tools">
                        @if(request('unserved'))
                            <span class="badge badge-warning p-2 mr-2">عرض: الغير مستلمين أبداً</span>
                            <a href="#" class="btn btn-tool text-danger filter-unserved-btn" data-unserved=""><i class="fas fa-times"></i></a>
                        @endif
                        @if(request('category'))
                            <span class="badge badge-info p-2 mr-2">عرض الفئة: {{ request('category') }}</span>
                            <a href="#" class="btn btn-tool text-danger filter-category-btn" data-category=""><i class="fas fa-times"></i></a>
                        @endif
                        @if(auth()->user()?->isAdmin())
                            <a href="#" class="btn btn-warning btn-sm shadow-sm mr-2 filter-unserved-btn" data-unserved="1">
                                <i class="fas fa-user-times mr-1"></i> الغير مستلمين
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success btn-sm shadow-sm">
                                <i class="fas fa-file-excel mr-1"></i> تصدير البيانات إلى Excel
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-valign-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 80px">النقاط</th>
                                    <th class="text-center" style="width: 70px">الفئة</th>
                                    <th>الاسم الرباعي</th>
                                    <th class="text-center">رقم الهوية</th>
                                    <th class="text-center">الجوال</th>
                                    <th class="text-center">الأفراد</th>
                                    <th style="width: 35%">تحليل الحالة (الأسباب والعمر)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($families as $family)
                                <tr>
                                    <td class="text-center">
                                        <span class="h5 mb-0 fw-bold text-{{ $family->badge }}">{{ $family->score }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $family->badge }} py-2 px-3">{{ $family->category }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $family->full_name }}</div>
                                        <small class="text-muted"><i class="fas fa-map-pin mr-1"></i> {{ $family->address }}</small>
                                    </td>
                                    <td class="text-center text-muted fw-bold">{{ $family->id_num }}</td>
                                    <td class="text-center">
                                        <a href="tel:{{ $family->phone }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-phone mr-1 text-success"></i> {{ $family->phone }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-light border px-3">{{ $family->members_count }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            @foreach(explode(' • ', $family->reasons) as $reason)
                                                <small class="badge badge-light border text-muted m-1 p-1">
                                                    {{ $reason }}
                                                </small>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                            <p>لا توجد نتائج مطابقة لخيارات البحث الحالية</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pagination && $pagination->hasPages())
                <div class="card-footer clearfix bg-white">
                    <div class="float-right">
                        {{ $pagination->appends(request()->all())->links() }}
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-5 bg-white shadow-sm rounded">
                <i class="fas fa-mouse-pointer fa-4x text-primary mb-4 opacity-25"></i>
                <h3>بانتظار اختيار المنطقة</h3>
                <p class="text-muted">يرجى اختيار مسؤول المنطقة من القائمة أعلاه لبدء التحليل الذكي</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table-valign-middle td { vertical-align: middle !important; }
    .fw-bold { font-weight: 700 !important; }
    .card-outline.card-primary { border-top: 3px solid #007bff; }

    /* تحسين شكل حقل Select2 ليتناسب مع التصميم العصري */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d8d6de !important;
        border-radius: 0.357rem !important;
        min-height: 38px !important;
        padding: 2px !important;
    }

    /* ستايل "التاج" أو "الحبة" داخل الحقل */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f3f2f7 !important;
        border: 1px solid #dcdcdc !important;
        color: #5e5873 !important;
        padding: 4px 12px !important;
        margin: 4px !important;
        font-weight: 500;
        border-radius: 4px !important;
        display: flex;
        align-items: center;
        float: right; /* لضمان الترتيب الصحيح في RTL */
    }

    /* تنسيق زر الحذف (x) */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ea5455 !important;
        margin-left: 8px !important;
        margin-right: 0px !important;
        order: 2;
        border: none !important;
        background: transparent !important;
    }

    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 7px !important;
        margin-right: 8px !important;
    }

    /* تنسيق زر مسح الكل (allowClear) في الحقول المتعددة */
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        position: absolute !important;
        left: 10px !important;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        cursor: pointer;
        color: #ea5455;
        font-size: 1.2rem;
        margin-top: 0 !important;
    }

    .select2-container--default .select2-selection--multiple {
        padding-left: 30px !important; /* مساحة لزر مسح الكل */
        position: relative;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').each(function() {
        var $el = $(this);
        $el.select2({
            dir: "rtl",
            width: '100%',
            allowClear: true,
            closeOnSelect: $el.attr('multiple') ? false : true
        });
    });

    // معالج الفلترة التفاعلية للفئات
    $(document).on('click', '.filter-category-btn', function(e) {
        e.preventDefault();
        var cat = $(this).data('category');
        $('#filterCategoryInput').val(cat);
        $('#filterPageInput').val(1);
        $('#auditFilterForm').submit();
    });

    // معالج الفلترة التفاعلية لغير المستلمين
    $(document).on('click', '.filter-unserved-btn', function(e) {
        e.preventDefault();
        var unserved = $(this).data('unserved');
        $('#filterUnservedInput').val(unserved);
        $('#filterPageInput').val(1);
        $('#auditFilterForm').submit();
    });
});
</script>
@endpush
@endsection
