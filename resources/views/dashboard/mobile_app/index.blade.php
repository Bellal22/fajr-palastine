<x-layout :title="'إدارة تطبيق الجوال'" :breadcrumbs="['dashboard.mobile_app.index']">
    <div class="row">
        <!-- قسم الإدارة -->
        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title', 'تكليفات التوزيع')
                <div class="text-center py-3">
                    <i class="fas fa-users-cog fa-4x text-primary mb-3"></i>
                    <p class="text-muted small">إدارة حسابات الموزعين الميدانيين وصلاحيات وصولهم للتطبيق.</p>
                    <a href="{{ route('dashboard.mobile_app.users') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-list"></i> قائمة الموزعين
                    </a>
                </div>
            @endcomponent
        </div>

        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title', 'إحصائيات التوزيع')
                <div class="py-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">الموزعين الفعالين:</span>
                        <span class="badge badge-primary">{{ number_format($stats['active_distributors_count']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">الموزعين المكلفين:</span>
                        <span class="badge badge-warning">{{ number_format($stats['assigned_distributors_count']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">إجمالي الترشيحات:</span>
                        <span class="badge badge-secondary">{{ number_format($stats['total_nominations']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">تم الاستلام:</span>
                        <span class="badge badge-success">{{ number_format($stats['total_received']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">تسليمات اليوم:</span>
                        <span class="badge badge-info">{{ number_format($stats['today_deliveries']) }}</span>
                    </div>
                    <a href="{{ route('dashboard.mobile_app.report') }}" class="btn btn-outline-success btn-sm btn-block">
                        <i class="fas fa-chart-pie"></i> تفاصيل الإحصائيات
                    </a>
                </div>
            @endcomponent
        </div>

        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title', 'تكليفات الميدان')
                <div class="text-center py-3">
                    <i class="fas fa-clipboard-list fa-4x text-info mb-3"></i>
                    <p class="text-muted small">تكليف الموزعين بأسماء المستفيدين وتحديد نقاط التوزيع.</p>
                    <a href="{{ route('dashboard.mobile_app.nominate') }}" class="btn btn-info btn-block">
                        <i class="fas fa-user-check"></i> إدارة التكليفات
                    </a>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            @component('dashboard::components.table-box')
                @slot('title')
                    <i class="fas fa-chart-line"></i> تقارير ومتابعة المزامنة
                @endslot

                <div class="p-4 text-center">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h4 class="fw-bold mb-3">مركز التقارير</h4>
                            <p class="text-muted small">متابعة إنجاز الموزعين ودفعات التسليم اليومية (Batches).</p>
                            <a href="{{ route('dashboard.mobile_app.report') }}" class="btn btn-outline-primary px-5">
                                <i class="fas fa-file-invoice"></i> عرض التقارير التفصيلية
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-3">أفضل الموزعين</h4>
                            <div class="text-left px-5">
                                @forelse($topDistributors as $top)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small font-weight-bold">{{ $loop->iteration }}. {{ $top->name }}</span>
                                        <span class="badge badge-light-success text-success">{{ $top->deliveries }} استلام</span>
                                    </div>
                                @empty
                                    <p class="text-muted small text-center">لا يوجد بيانات تسليم حتى الآن.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</x-layout>
