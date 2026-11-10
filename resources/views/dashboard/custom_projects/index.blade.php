<x-layout :title="'إدارة المشاريع المخصصة (RRM وغيرها)'" :breadcrumbs="['dashboard.custom_projects.index']">

    {{-- الشريط العلوي للتعريف والإضافة --}}
    <div class="card card-outline card-primary mb-4 shadow-sm">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <div class="col-lg-9 col-md-8 text-right mb-3 mb-md-0">
                    <h5 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-layer-group text-primary ml-2"></i>
                        نظام المشاريع المخصصة (مشروع الـ RRM والمشاريع المشابهة)
                    </h5>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        من هنا يمكنك إدارة <strong>مشروع الـ RRM</strong> وإضافة مشاريع مخصصة جديدة تعمل بنفس الآلية (كشوفات وتوزيعات مستقلة)، مع تخصيص طريقة التعامل مع كل مشروع (تحديد الطرود/الكوبونات الافتراضية المرتبطة به، نطاق التسليم، المخزن الافتراضي، وسياسة الإضافة والتكرار).
                    </p>
                </div>
                <div class="col-lg-3 col-md-4 text-md-left text-right">
                    <a href="{{ route('dashboard.custom_projects.create') }}" class="btn btn-success font-weight-bold px-3 py-2 shadow-sm text-white">
                        <i class="fas fa-plus-circle ml-1"></i> إضافة مشروع مخصص جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- بطاقات المشاريع المخصصة --}}
    <div class="row">
        @forelse($customProjects as $cp)
            @php
                $stats = $batchStats->get($cp->id);
                $batchesCount = $stats ? (int) $stats->batches_count : 0;
                $totalBeneficiaries = $stats ? (int) $stats->total_beneficiaries : 0;
                $totalParcels = $stats ? (int) $stats->total_parcels : 0;
                $defaultIds = $cp->default_project_ids ?? [];
                $defaultQtys = $cp->default_project_quantities ?? [];
            @endphp
            <div class="{{ $customProjects->count() === 1 ? 'col-lg-8 col-md-10 mx-auto' : 'col-lg-6' }} col-12 mb-4">
                <div class="card card-outline card-{{ $cp->color ?: 'primary' }} h-100 shadow-sm mb-0">
                    {{-- رأس البطاقة --}}
                    <div class="card-header bg-white py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center text-right">
                                <div class="rounded bg-{{ $cp->color ?: 'primary' }} d-flex align-items-center justify-content-center ml-3 shadow-xs"
                                     style="width: 46px; height: 46px; font-size: 1.25rem; flex-shrink: 0;">
                                    <i class="{{ $cp->icon ?: 'fas fa-first-aid' }}"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                        <h5 class="font-weight-bold mb-0 text-dark">
                                            {{ $cp->name }}
                                        </h5>
                                        @if($cp->slug === 'rrm')
                                            <span class="badge badge-warning text-dark px-2 py-1">المشروع الأساسي</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small mt-1">
                                        بادئة الكشوفات: <code class="font-weight-bold px-1 bg-light rounded border">{{ $cp->code_prefix }}-</code>
                                        @if($cp->is_cash)
                                            <span class="badge badge-success px-2 py-1 mr-2">
                                                <i class="fas fa-money-bill-wave ml-1"></i>
                                                مشروع نقدي: {{ $cp->formatted_cash_value }}
                                            </span>
                                        @else
                                            <span class="badge badge-info px-2 py-1 mr-2">
                                                <i class="fas fa-boxes ml-1"></i>
                                                مشروع عيني
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($cp->is_active)
                                    <span class="badge badge-success px-3 py-2" style="font-size: 0.85rem;">
                                        <i class="fas fa-check-circle ml-1"></i> نشط
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-3 py-2" style="font-size: 0.85rem;">
                                        <i class="fas fa-pause-circle ml-1"></i> متوقف
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- محتوى البطاقة --}}
                    <div class="card-body px-4 py-3 text-right">
                        @if($cp->description)
                            <p class="text-muted small mb-3">{{ $cp->description }}</p>
                        @endif

                        {{-- إحصائيات المشروع --}}
                        <div class="row text-center bg-light rounded py-3 mb-3 border mx-0">
                            <div class="col-4 border-left">
                                <small class="text-muted d-block mb-1">عدد الكشوفات</small>
                                <strong class="h5 font-weight-bold text-primary mb-0">{{ number_format($batchesCount) }}</strong>
                            </div>
                            <div class="col-4 border-left">
                                <small class="text-muted d-block mb-1">المستفيدين</small>
                                <strong class="h5 font-weight-bold text-success mb-0">{{ number_format($totalBeneficiaries) }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">إجمالي الطرود</small>
                                <strong class="h5 font-weight-bold text-warning mb-0">{{ number_format($totalParcels) }}</strong>
                            </div>
                        </div>

                        {{-- إعدادات وطريقة التعامل المخصصة --}}
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-dark small mb-2">
                                <i class="fas fa-sliders-h text-info ml-1"></i> إعدادات وطريقة التعامل المخصصة:
                            </h6>
                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                <span class="badge badge-light border text-dark px-2 py-2" style="font-size: 0.82rem;">
                                    <i class="fas fa-layer-group text-primary ml-1"></i>
                                    النطاق: <strong>{{ $cp->coupon_scope_label }}</strong>
                                </span>

                                @if($cp->is_cash)
                                    <span class="badge badge-light border text-success px-2 py-2 font-weight-bold" style="font-size: 0.82rem;">
                                        <i class="fas fa-money-bill-wave text-success ml-1"></i>
                                        المبلغ النقدي للمستفيد: <strong>{{ $cp->formatted_cash_value }} ({{ $cp->currency_label }})</strong>
                                    </span>
                                @endif

                                @if($cp->defaultSubWarehouse)
                                    <span class="badge badge-light border text-dark px-2 py-2" style="font-size: 0.82rem;">
                                        <i class="fas fa-warehouse text-secondary ml-1"></i>
                                        المخزن الافتراضي: <strong>{{ $cp->defaultSubWarehouse->name }}</strong>
                                    </span>
                                @endif

                                <span class="badge badge-light border text-dark px-2 py-2" style="font-size: 0.82rem;">
                                    <i class="fas {{ $cp->auto_add_if_missing ? 'fa-user-plus text-success' : 'fa-user-lock text-danger' }} ml-1"></i>
                                    {{ $cp->auto_add_if_missing ? 'يسمح بإضافة غير المدرجين تلقائياً' : 'يشترط الإدراج المسبق' }}
                                </span>

                                <span class="badge badge-light border text-dark px-2 py-2" style="font-size: 0.82rem;">
                                    <i class="fas {{ $cp->skip_already_delivered ? 'fa-shield-alt text-info' : 'fa-redo text-warning' }} ml-1"></i>
                                    {{ $cp->skip_already_delivered ? 'منع تكرار الاستلام تلقائياً' : 'يسمح بتكرار الاستلام' }}
                                </span>
                            </div>
                        </div>

                        {{-- الكوبونات / الطرود الافتراضية المرتبطة --}}
                        <div class="bg-light rounded border p-3">
                            <h6 class="font-weight-bold text-dark small mb-2">
                                <i class="fas fa-boxes text-secondary ml-1"></i> الكوبونات / الطرود الافتراضية المرتبطة:
                            </h6>
                            @if(!empty($defaultIds))
                                <div class="d-flex flex-wrap" style="gap: 6px;">
                                    @foreach($defaultIds as $pid)
                                        @if(isset($projectsMap[$pid]))
                                            <span class="badge badge-info px-2 py-1 font-weight-normal" style="font-size: 0.85rem;">
                                                <i class="fas fa-box ml-1"></i>
                                                {{ $projectsMap[$pid] }}
                                                <strong>(×{{ $defaultQtys[$pid] ?? 1 }})</strong>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle ml-1"></i> لم يتم تقييد طرود افتراضية (يمكن اختيار أي طرد/حزمة عند التسليم).
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- ذيل البطاقة (الأزرار والإجراءات) --}}
                    <div class="card-footer bg-white py-3 px-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center w-100" style="gap: 10px;">
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <a href="{{ route('dashboard.rrm.index', ['custom_project_id' => $cp->id]) }}"
                                   class="btn btn-sm btn-primary font-weight-bold px-3 py-1">
                                    <i class="fas fa-clipboard-list ml-1"></i> الكشوفات ({{ $batchesCount }})
                                </a>
                                <a href="{{ route('dashboard.rrm.deliver', ['custom_project_id' => $cp->id]) }}"
                                   class="btn btn-sm btn-success font-weight-bold px-3 py-1">
                                    <i class="fas fa-plus-circle ml-1"></i> تسليم كشف جديد
                                </a>
                            </div>

                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="{{ route('dashboard.custom_projects.edit', $cp) }}"
                                   class="btn btn-sm btn-outline-info font-weight-bold px-3 py-1"
                                   title="تعديل وتخصيص طريقة التعامل">
                                    <i class="fas fa-sliders-h ml-1"></i> تخصيص وتعديل
                                </a>

                                <form action="{{ route('dashboard.custom_projects.toggle_status', $cp) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $cp->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} px-2 py-1"
                                            title="{{ $cp->is_active ? 'إيقاف مؤقت' : 'تفعيل' }}">
                                        <i class="fas {{ $cp->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>

                                @if($cp->slug !== 'rrm' && $batchesCount === 0)
                                    <form action="{{ route('dashboard.custom_projects.destroy', $cp) }}"
                                          method="POST"
                                          class="d-inline m-0"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع المخصص؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center py-5 border">
                    لا توجد مشاريع مخصصة مضافة حالياً.
                </div>
            </div>
        @endforelse
    </div>

</x-layout>
