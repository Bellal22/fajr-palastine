<x-layout :title="'تقرير - ' . $project->name" :breadcrumbs="['dashboard.reports.projects.show', $project]">
    <style>
        .report-card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .area-badge { background: #f8f9fa; border-radius: 12px; padding: 15px; margin-bottom: 15px; border: 1px solid #eee; text-align: center; }
        .area-badge h3 { margin-top: 5px; color: #007bff; font-weight: bold; }
        .warehouse-badge { background: #e3f2fd; border-radius: 12px; padding: 15px; margin-bottom: 15px; border: 1px solid #90caf9; text-align: center; }
        .warehouse-badge h3 { margin-top: 5px; color: #1976d2; font-weight: bold; }
        .progress-custom { height: 12px; border-radius: 6px; background: #eee; overflow: hidden; }
        .progress-bar-custom { height: 100%; transition: width 1s ease; }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card report-card overflow-hidden">
                <div class="card-header bg-primary py-3">
                    <h3 class="card-title font-weight-bold mb-0 text-white">
                        <i class="fas fa-chart-bar mr-2"></i> تقرير إحصائي: {{ $project->name }}
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Progress Section -->
                    @php
                        $percent = $project->total_candidates > 0 ? round(($project->received_count / $project->total_candidates) * 100) : 0;
                        $colorClass = $percent > 75 ? 'bg-success' : ($percent > 40 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <div class="row align-items-center mb-5">
                        <div class="col-md-3 text-center">
                            <h1 class="display-4 font-weight-bold {{ str_replace('bg-', 'text-', $colorClass) }}">{{ $percent }}%</h1>
                            <p class="text-muted text-uppercase small font-weight-bold">نسبة الإنجاز</p>
                        </div>
                        <div class="col-md-9">
                            <div class="progress-custom shadow-sm mb-2">
                                <div class="progress-bar-custom {{ $colorClass }}" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>البداية: {{ $project->start_date ? $project->start_date->format('Y-m-d') : '-' }}</span>
                                <span>اكتمال التوزيع</span>
                            </div>
                        </div>
                    </div>

                    <!-- Counter Grid -->
                    <div class="row text-center mb-5">
                        <div class="col-md-4 border-right">
                            <div class="p-3">
                                <i class="fas fa-users-cog fa-2x text-info mb-3"></i>
                                <h6 class="text-muted text-uppercase small font-weight-bold">إجمالي المرشحين</h6>
                                <h2 class="font-weight-bold">{{ number_format($project->total_candidates) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4 border-right">
                            <div class="p-3">
                                <i class="fas fa-user-check fa-2x text-success mb-3"></i>
                                <h6 class="text-muted text-uppercase small font-weight-bold">عدد المستلمين</h6>
                                <h2 class="text-success font-weight-bold">{{ number_format($project->received_count) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-user-times fa-2x text-danger mb-3"></i>
                                <h6 class="text-muted text-uppercase small font-weight-bold">غير مستلم</h6>
                                <h2 class="text-danger font-weight-bold">{{ number_format($project->not_received_count) }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Area Breakdown -->
                    <h4 class="font-weight-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-map-marked-alt text-primary mr-2"></i> توزيع المستلمين حسب المناطق المسؤول عنها
                    </h4>
                    <div class="row mb-5">
                        @forelse($areas as $area)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="area-badge shadow-sm">
                                <small class="text-muted text-uppercase font-weight-bold">{{ $area->name }}</small>
                                <h3>{{ number_format($project->area_breakdown[$area->id] ?? 0) }}</h3>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 py-5 text-center bg-light rounded">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لم يتم رصد أي عمليات استلام في أي منطقة لهذا الكوبون حتى الآن.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Warehouse Breakdown (NEW) -->
                    <h4 class="font-weight-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-warehouse text-info mr-2"></i> توزيع المستلمين حسب المخازن الفرعية
                    </h4>
                    <div class="row">
                        @forelse($subWarehouses as $warehouse)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="warehouse-badge shadow-sm">
                                <i class="fas fa-warehouse text-info mb-2"></i>
                                <small class="text-muted text-uppercase font-weight-bold d-block">{{ $warehouse->name }}</small>
                                <h3>{{ number_format($project->warehouse_breakdown[$warehouse->id] ?? 0) }}</h3>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 py-5 text-center bg-light rounded">
                            <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لم يتم تحديد مخازن فرعية للمستفيدين من هذا الكوبون.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="far fa-clock mr-1"></i> تم التحديث بتاريخ: {{ now()->translatedFormat('d F Y, h:i a') }}</span>
                        <div>
                            <a href="{{ route('dashboard.projects.show', $project) }}" class="btn btn-outline-primary px-4 mr-2">
                                <i class="fas fa-info-circle mr-1"></i> تفاصيل الكوبون
                            </a>
                            <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-primary px-4">
                                <i class="fas fa-users mr-1"></i> كشف المستفيدين
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
