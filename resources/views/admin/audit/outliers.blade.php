@extends('layouts.adminlte3.master')

@section('content')
<div class="container-fluid">
    <!-- Header with Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-danger shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title fw-bold text-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i> رادار القيم الشاذة والمخاطر
                    </h3>
                    <div class="card-tools">
                        @if(request('area_responsible_id'))
                            @php
                                $queryParams = request()->query();
                                $queryParams['export'] = 1;
                                $exportUrl = route('admin.audit.outliers') . '?' . http_build_query($queryParams);
                            @endphp
                            <a href="{{ $exportUrl }}" class="btn btn-success btn-sm rounded-pill px-3 mr-2">
                                <i class="fas fa-file-excel mr-1"></i> تصدير البيانات (Excel)
                            </a>
                        @endif
                        <a href="{{ route('admin.audit.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-arrow-left mr-1"></i> العودة للتدقيق العام
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.audit.outliers') }}" method="GET" class="row align-items-end">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold mb-1">مسؤولو المناطق (يمكنك تحديد أكثر من منطقة)</label>
                            <select name="area_responsible_id[]" class="form-control select2" multiple data-placeholder="-- اختر المناطق --" style="width: 100%;">
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ in_array($area->id, (array)request('area_responsible_id')) ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold mb-1">المربع/المندوب (اختياري)</label>
                            <select name="block_id" class="form-control select2" data-placeholder="-- كل المربعات --" style="width: 100%;">
                                <option value="">-- كل المربعات --</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                        {{ $block->name }} ({{ $block->areaResponsible->name ?? 'غير محدد' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger btn-block rounded-pill fw-bold" style="height: calc(2.25rem + 2px);">
                                <i class="fas fa-sync-alt mr-1"></i> تحليل
                            </button>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-light border mb-0 py-2 text-center" style="height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
                                <span class="text-muted mr-1 small">المتوسط:</span> 
                                <strong class="text-primary fw-bold">{{ $avg }} وحدة</strong>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($message))
        <div class="text-center py-5 bg-white shadow-sm rounded-lg border">
            <i class="fas fa-search-location fa-4x text-danger mb-4 opacity-25"></i>
            <h3>{{ $message }}</h3>
            <p class="text-muted">التحليل الإحصائي يتطلب تحديد نطاق جغرافي لضمان دقة النتائج وسرعة التحميل</p>
        </div>
    @else
    <div class="row">
        <!-- 1. Over-served Families (High Risk) -->
        <div class="col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title fw-bold"><i class="fas fa-arrow-up text-danger"></i> مستفيدون فوق المعدل الطبيعي (Z-Score > 2.5)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>الاسم</th>
                                <th class="text-center">الفئة</th>
                                <th class="text-center">إجمالي الاستلام</th>
                                <th class="text-center">مرات الاستلام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overServed as $s)
                            <tr>
                                <td class="pl-3">
                                    <div class="fw-bold">{{ $s->name }}</div>
                                    <small class="text-muted">{{ $s->person_id }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $s->score >= 50 ? 'warning' : 'success' }}">{{ $s->category }}</span>
                                </td>
                                <td class="text-center text-danger fw-bold h5">{{ $s->total_qty }}</td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-outline-info rounded-pill px-2" onclick="showHistory('{{ $s->person_id }}', '{{ $s->name }}')">
                                        <i class="fas fa-history mr-1"></i> {{ $s->times }}
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">لا توجد حالات شاذة بالزيادة حالياً</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer small text-muted">
                    * هؤلاء الأشخاص استلموا كميات تتجاوز المتوسط بنسبة انحراف كبيرة جداً.
                </div>
            </div>
        </div>

        <!-- 2. Neglected High-Need Families (Critical Risk) -->
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title fw-bold"><i class="fas fa-hand-holding-heart text-warning"></i> فئات (A/B) منسية (استلام = 0 أو قديم جداً)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>الاسم</th>
                                <th class="text-center">الفئة</th>
                                <th class="text-center">آخر استلام</th>
                                <th>السبب الرئيسي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($neglected as $n)
                            <tr>
                                <td class="pl-3">
                                    <div class="fw-bold">{{ $n->name }}</div>
                                    <small class="text-muted">{{ $n->id_num }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $n->badge }}">{{ $n->category }}</span>
                                    <div class="small text-primary fw-bold">{{ $n->score }}</div>
                                </td>
                                <td class="text-center small">
                                    <span class="badge badge-pill badge-{{ $n->last_delivery == 'لم يستلم أبداً' ? 'dark' : 'danger' }} px-2">
                                        {{ $n->last_delivery }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ Str::limit($n->reasons, 40) }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">جميع حالات الاحتياج العالي استلمت مساعدات حديثة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer small text-muted">
                    * هؤلاء لديهم احتياج عالي ولكنهم لم يستلموا أبداً، أو مر على آخر استلام لهم أكثر من 5 أشهر.
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="historyTitle">سجل الكوبونات والمساعدات</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="historyLoader" class="text-center py-5 d-none">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="mt-2 text-muted">جاري تحميل السجل...</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>المشروع / الكوبون</th>
                                <th class="text-center">الكمية</th>
                                <th>التاريخ</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody id="historyContent">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fw-bold { font-weight: 700 !important; }
    
    /* تحسين شكل حقل Select2 ليتناسب مع التصميم العصري */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d8d6de !important;
        border-radius: 0.357rem !important;
        min-height: 40px !important;
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
@endpush

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
});

function showHistory(idNum, name) {
    $('#historyTitle').text('سجل الكوبونات والمساعدات: ' + name);
    $('#historyContent').empty();
    $('#historyLoader').removeClass('d-none');
    $('#historyModal').modal('show');

    fetch(`/admin/audit/history/${idNum}`)
        .then(response => response.json())
        .then(data => {
            $('#historyLoader').addClass('d-none');
            if (data.length === 0) {
                $('#historyContent').append('<tr><td colspan="4" class="text-center py-4">لا توجد سجلات استلام لهذا الشخص</td></tr>');
                return;
            }
            data.forEach(item => {
                $('#historyContent').append(`
                    <tr>
                        <td class="fw-bold">${item.project_name}</td>
                        <td class="text-center"><span class="badge badge-info px-2">${item.quantity}</span></td>
                        <td><small>${item.delivery_date || 'غير محدد'}</small></td>
                        <td><small class="text-muted">${item.notes || '-'}</small></td>
                    </tr>
                `);
            });
        })
        .catch(error => {
            $('#historyLoader').addClass('d-none');
            $('#historyContent').append('<tr><td colspan="4" class="text-center py-4 text-danger">حدث خطأ أثناء تحميل البيانات</td></tr>');
        });
}
</script>
@endpush
@endsection
