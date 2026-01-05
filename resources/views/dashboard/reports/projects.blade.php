<x-layout :title="trans('projects.reports')" :breadcrumbs="['dashboard.reports.projects']">
    @push('style')
        <style>
            .report-card {
                border-radius: 10px;
                border: none;
                box-shadow: 0 2px 6px rgba(0,0,0,0.06);
                transition: all 0.3s ease;
                background: white;
            }
            .report-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            .stat-icon {
                font-size: 2.5rem;
                opacity: 0.2;
                position: absolute;
                right: 20px;
                top: 20px;
            }
            .area-badge {
                background: #f8f9fc;
                border-radius: 8px;
                padding: 12px 10px;
                margin-bottom: 10px;
                border: 1px solid #e3e6f0;
                text-align: center;
                height: 100%;
                transition: all 0.2s;
            }
            .area-badge:hover {
                background: white;
                border-color: #007bff;
                box-shadow: 0 2px 8px rgba(0,123,255,0.1);
            }
            .area-badge h4 {
                margin-top: 5px;
                color: #007bff;
                font-weight: bold;
                margin-bottom: 0;
            }
            .area-badge small {
                font-size: 0.75rem;
                color: #6c757d;
            }
            .warehouse-badge {
                background: #e3f2fd;
                border-radius: 8px;
                padding: 12px 10px;
                margin-bottom: 10px;
                border: 1px solid #90caf9;
                text-align: center;
                height: 100%;
                transition: all 0.2s;
            }
            .warehouse-badge:hover {
                background: white;
                border-color: #1976d2;
                box-shadow: 0 2px 8px rgba(25,118,210,0.15);
            }
            .warehouse-badge h4 {
                margin-top: 5px;
                color: #1976d2;
                font-weight: bold;
                margin-bottom: 0;
            }
            .warehouse-badge small {
                font-size: 0.75rem;
                color: #5c6bc0;
            }
            .progress-custom {
                height: 10px;
                border-radius: 10px;
                background: #e9ecef;
                overflow: hidden;
            }
            .progress-bar-custom {
                height: 100%;
                transition: width 0.6s ease;
                border-radius: 10px;
            }
            .table-summary th {
                background: #f8f9fc;
                text-align: center;
                font-weight: 600;
                color: #5a5c69;
                font-size: 0.85rem;
                padding: 0.75rem;
            }
            .table-summary td {
                text-align: center;
                vertical-align: middle !important;
                padding: 0.75rem;
            }
        </style>
    @endpush

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="mb-0 font-weight-bold">
            <i class="fas fa-chart-bar text-primary ml-2"></i>
            تقارير الكوبونات
            <span class="badge badge-light border px-3 py-2 mr-2" style="font-size: 0.75rem; font-weight: normal;">
                <i class="far fa-calendar ml-1"></i>
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </h3>
    </div>

    <!-- General Summary Section -->
    <div class="row mb-4">
        <div class="col-md-7 mb-3">
            <div class="row">
                @foreach([
                    'daily' => ['color' => '#17a2b8', 'icon' => 'calendar-day', 'label' => 'اليومية'],
                    'weekly' => ['color' => '#28a745', 'icon' => 'calendar-week', 'label' => 'الأسبوعية'],
                    'monthly' => ['color' => '#007bff', 'icon' => 'calendar-alt', 'label' => 'الشهرية'],
                    'yearly' => ['color' => '#dc3545', 'icon' => 'history', 'label' => 'السنوية']
                ] as $key => $config)
                <div class="col-sm-6 col-12 mb-3">
                    <a href="{{ route('dashboard.reports.projects.period', $key) }}" class="text-decoration-none">
                        <div class="card report-card text-white h-100" style="background: {{ $config['color'] }}; cursor: pointer;">
                            <div class="card-body position-relative">
                                <i class="fas fa-{{ $config['icon'] }} stat-icon"></i>
                                <h6 class="mb-3 opacity-90">الكوبونات {{ $config['label'] }}</h6>
                                <h2 class="mb-3 font-weight-bold">{{ $stats[$key]['count'] }}</h2>
                                <div class="pt-2" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>المستلمين</small>
                                        <strong>{{ number_format($stats[$key]['recipients']) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-5">
            <div class="card report-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-list-ul text-primary ml-2"></i>
                        ملخص الفترة الإجمالي
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-summary mb-0">
                        <thead>
                            <tr>
                                <th class="text-right">الفترة</th>
                                <th>الكوبونات</th>
                                <th>المستلمين</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['daily' => 'اليوم', 'weekly' => 'الأسبوع', 'monthly' => 'الشهر', 'yearly' => 'السنة'] as $key => $label)
                            <tr>
                                <td class="text-right font-weight-bold">{{ $label }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $stats[$key]['count'] }}</span>
                                </td>
                                <td class="text-muted">{{ number_format($stats[$key]['recipients']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Projects Section -->
    <h4 class="mb-3 font-weight-bold">
        <i class="fas fa-box text-primary ml-2"></i>
        تقرير تفصيلي لكل كوبون
    </h4>

    @forelse($projects as $project)
        <div class="card report-card mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">{{ $project->name }}</h5>
                    <span class="badge badge-primary px-3 py-2">
                        <i class="far fa-calendar ml-1"></i>
                        {{ $project->start_date ? $project->start_date->format('Y-m-d') : 'غير محدد' }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Progress Bar -->
                @php
                    $percent = $project->total_candidates > 0 ? round(($project->received_count / $project->total_candidates) * 100) : 0;
                    $colorClass = $percent > 75 ? 'bg-success' : ($percent > 40 ? 'bg-warning' : 'bg-danger');
                @endphp
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="font-weight-600">نسبة الإنجاز</span>
                        <span class="badge badge-light border">{{ $percent }}%</span>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-bar-custom {{ $colorClass }}" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">إجمالي المرشحين</h6>
                            <h3 class="font-weight-bold mb-0 text-info">{{ number_format($project->total_candidates) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">المستلمين</h6>
                            <h3 class="font-weight-bold mb-0 text-success">{{ number_format($project->received_count) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">غير مستلم</h6>
                            <h3 class="font-weight-bold mb-0 text-danger">{{ number_format($project->not_received_count) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Areas Breakdown -->
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-map-marker-alt text-danger ml-1"></i>
                    توزيع المستلمين حسب المناطق
                </h6>
                <div class="row mb-4">
                    @php
                        $activeAreas = collect($areas)->filter(fn($area) => isset($project->area_breakdown[$area->id]) && $project->area_breakdown[$area->id] > 0);
                    @endphp

                    @forelse($activeAreas as $area)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="area-badge">
                            <small>{{ $area->name }}</small>
                            <h4>{{ number_format($project->area_breakdown[$area->id]) }}</h4>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center mb-0">
                            <i class="fas fa-info-circle text-muted ml-1"></i>
                            لا يوجد مستلمين حتى الآن
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Warehouses Breakdown (NEW) -->
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-warehouse text-info ml-1"></i>
                    توزيع المستلمين حسب المخازن الفرعية
                </h6>
                <div class="row">
                    @php
                        $activeWarehouses = collect($subWarehouses)->filter(fn($warehouse) => isset($project->warehouse_breakdown[$warehouse->id]) && $project->warehouse_breakdown[$warehouse->id] > 0);
                    @endphp

                    @forelse($activeWarehouses as $warehouse)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="warehouse-badge">
                            <i class="fas fa-warehouse" style="font-size: 0.9rem;"></i>
                            <small class="d-block">{{ $warehouse->name }}</small>
                            <h4>{{ number_format($project->warehouse_breakdown[$warehouse->id]) }}</h4>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center mb-0" style="background: #e3f2fd; border-color: #90caf9;">
                            <i class="fas fa-warehouse text-info ml-1"></i>
                            لم يتم تحديد مخازن فرعية للمستفيدين
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="far fa-clock ml-1"></i>
                        آخر تحديث: {{ now()->translatedFormat('d M Y, h:i a') }}
                    </small>
                    <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-sm btn-primary">
                        عرض التفاصيل
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="card report-card text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.2;"></i>
            <h5 class="text-muted mb-0">لا توجد كوبونات مسجلة</h5>
        </div>
    @endforelse
</x-layout>
