<x-layout :title="'تقارير الكوبونات - ' . $label" :breadcrumbs="['dashboard.reports.projects.period', $period]">

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    {{-- <h3 class="mb-2"><i class="fas fa-chart-bar ml-2"></i> تقرير الكوبونات - <span class="text-primary">{{ $label }}</span></h3> --}}
                    <p class="mb-0 text-muted">نظرة شاملة على العمليات خلال هذه الفترة</p>
                </div>
                <div class="col-md-4 text-left">
                    <form method="GET" action="{{ route('dashboard.reports.projects.period', $period) }}">
                        <input type="date"
                               name="date"
                               value="{{ $today->format('Y-m-d') }}"
                               class="form-control"
                               style="max-width: 200px; margin-left: auto;"
                               onchange="this.form.submit()">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">كوبونات تم إنشاؤها</h6>
                        <i class="fas fa-plus-circle fa-2x opacity-50"></i>
                    </div>
                    <h2 class="mb-0 font-weight-bold">{{ $createdProjects->count() }}</h2>
                    <small class="text-white-50">كوبونات جديدة</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">كوبونات نشطة</h6>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                    <h2 class="mb-0 font-weight-bold">{{ $activeProjects->count() }}</h2>
                    <small class="text-white-50">تمت عليها عمليات تسليم</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">إجمالي المستفيدين</h6>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                    <h2 class="mb-0 font-weight-bold">{{ number_format($recipients->total()) }}</h2>
                    <small class="text-white-50">مستفيد استلم كوبون</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Created Coupons -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-plus-circle text-info ml-2"></i>
                الكوبونات المضافة حديثاً
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>اسم الكوبون</th>
                            <th class="text-center">تاريخ الإضافة</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($createdProjects as $project)
                        <tr>
                            <td class="font-weight-bold">{{ $project->name }}</td>
                            <td class="text-center">{{ $project->created_at->format('Y-m-d') }}</td>
                            <td class="text-center">
                                @if($project->status === 'active')
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">{{ $project->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('dashboard.projects.show', $project) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                لا توجد كوبونات جديدة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Coupons & Areas -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle text-success ml-2"></i>
                        الكوبونات النشطة
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>اسم الكوبون</th>
                                    <th class="text-center">عدد المستفيدين</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeProjects as $project)
                                <tr>
                                    <td class="font-weight-bold">{{ $project->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary badge-pill">
                                            {{ number_format($project->period_received_count) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dashboard.reports.projects.show', $project) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-pie"></i> الإحصائيات
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">لا توجد كوبونات نشطة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt text-danger ml-2"></i>
                        التوزيع الجغرافي
                    </h5>
                </div>
                <div class="card-body">
                    <div style="max-height: 350px; overflow-y: auto;">
                        @forelse($areas as $areaId => $area)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <span class="font-weight-bold">{{ $area->name }}</span>
                            <span class="badge badge-dark">{{ number_format($areaBreakdown[$areaId]) }}</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            لا توجد بيانات
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Logs -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list text-primary ml-2"></i>
                سجل عمليات الاستلام
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>اسم المستفيد</th>
                            <th class="text-center">الكوبون</th>
                            <th class="text-center">المنطقة</th>
                            <th class="text-center">تاريخ الاستلام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $recipient)
                        <tr>
                            <td class="font-weight-bold">{{ $recipient->person_name }}</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $recipient->project_name }}</span>
                            </td>
                            <td class="text-center">{{ $areas[$recipient->area_id]->name ?? 'غير محدد' }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($recipient->delivery_date)->format('Y-m-d h:i A') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                لا توجد سجلات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($recipients->hasPages())
        <div class="card-footer bg-white">
            {{ $recipients->links() }}
        </div>
        @endif
    </div>

</x-layout>
