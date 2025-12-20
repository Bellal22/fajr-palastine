<x-layout :title="trans('dashboard.home')" :breadcrumbs="['dashboard.home']">

    @push('styles')
        <style>
            .hover-shadow {
                transition: all 0.3s ease-in-out;
            }
            .hover-shadow:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
            }
            .progress {
                border-radius: 10px;
                overflow: hidden;
            }
        </style>
    @endpush

    {{-- الرسم البياني أولاً (عمودين: مسجلين + مزامنين) --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        إحصائيات التسجيلات والمزامنة الشهرية {{ now()->year }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 450px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات الحالات الاجتماعية - دائرتين --}}
    <div class="row mb-5 g-4">
        {{-- الدائرة الأولى: المعتمدين --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        الحالات الاجتماعية - المعتمدين
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 350px;">
                        <canvas id="approvedSocialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- الدائرة الثانية: غير المعتمدين --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        الحالات الاجتماعية - غير المعتمدين
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 350px;">
                        <canvas id="unapprovedSocialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الكروت الإحصائية --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalCount) }}</h4>
                            <small>إجمالي رب الأسرة</small>
                        </div>
                        <i class="fas fa-user-friends fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ number_format($approvedCount) }}</h4>
                            <small>رب الأسرة معتمد</small>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ number_format($unapprovedCount) }}</h4>
                            <small>رب الأسرة غير معتمد</small>
                        </div>
                        <i class="fas fa-user-times fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                {{ $totalCount > 0 ? round(($approvedCount / $totalCount) * 100, 1) : 0 }}%
                            </h4>
                            <small>نسبة المعتمد</small>
                        </div>
                        <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات مسؤولي المناطق (AdminLTE Style) --}}
    <div class="row">
        @forelse($areaStats as $stat)
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-gradient-primary elevation-1">
                        {{-- نفترض أن $stat فيه responsible_id --}}
                        <a href="{{ route('dashboard.regions.showByResponsible', $stat['responsible_id']) }}"
                        class="text-white d-inline-block"
                        title="عرض المنطقة">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fw-semibold">{{ $stat['name'] }}</span>
                        <span class="info-box-number text-primary fw-bold">
                            {{ number_format($stat['total']) }}
                        </span>

                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ number_format($stat['approved']) }} معتمد
                                </small>
                                <div class="progress progress-xs mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $stat['approved_percent'] }}%"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-info">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    {{ number_format($stat['synced']) }} مزامن
                                </small>
                                <div class="progress progress-xs mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info"
                                        style="width: {{ $stat['synced_percent'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    <h5>لا توجد بيانات</h5>
                    <p>لم يتم تعيين مسؤولي مناطق بعد</p>
                </div>
            </div>
        @endforelse
    </div>


    <div class="row mb-4 g-4">
        {{-- دائرة المعتمدين --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        توزيع جنس أرباب الأسر - المعتمدين
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 350px;">
                        <canvas id="genderApprovedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- دائرة غير المعتمدين --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        توزيع جنس أرباب الأسر - غير المعتمدين
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 350px;">
                        <canvas id="genderUnapprovedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($pregnantNursingStats['pregnant_approved']) }}</h3>
                    <p>حوامل (معتمدات)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby-carriage"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($pregnantNursingStats['pregnant_unapproved']) }}</h3>
                    <p>حوامل (غير معتمدات)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby-carriage"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($pregnantNursingStats['nursing_approved']) }}</h3>
                    <p>مرضعات (معتمدات)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($pregnantNursingStats['nursing_unapproved']) }}</h3>
                    <p>مرضعات (غير معتمدات)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($childrenUnder1Count) }}</h3>
                    <p>أطفال عمر سنة وأقل (أب وطفل معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($childrenUnder3Count) }}</h3>
                    <p>أطفال أقل من ثلاث سنوات (أب وطفل معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-child"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات مرضى السرطان --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($cancerPatientsStats['cancer_approved']) }}</h3>
                    <p>مرضى السرطان (معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ribbon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($cancerPatientsStats['cancer_unapproved']) }}</h3>
                    <p>مرضى السرطان (غير معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ribbon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات المصابين (إصابات حرب) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($injuredPatientsStats['injured_approved']) }}</h3>
                    <p>المصابين - إصابات حرب (معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($injuredPatientsStats['injured_unapproved']) }}</h3>
                    <p>المصابين - إصابات حرب (غير معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات مرضى الكلى (غسيل كلوي) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($kidneyPatientsStats['kidney_approved']) }}</h3>
                    <p>مرضى الكلى - غسيل كلوي (معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-procedures"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($kidneyPatientsStats['kidney_unapproved']) }}</h3>
                    <p>مرضى الكلى - غسيل كلوي (غير معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-procedures"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات التبول اللاإرادي / المسنين --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($incontinenceStats['incontinence_approved']) }}</h3>
                    <p>تبول لاإرادي / مسنين (معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($incontinenceStats['incontinence_unapproved']) }}</h3>
                    <p>تبول لاإرادي / مسنين (غير معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات الحالات الخاصة (الإعاقات) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($disabilityPatientsStats['disability_approved']) }}</h3>
                    <p>حالات خاصة - إعاقات (معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wheelchair"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($disabilityPatientsStats['disability_unapproved']) }}</h3>
                    <p>حالات خاصة - إعاقات (غير معتمدين)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wheelchair"></i>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1) الرسم الشهري
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($registeredData)),
                    datasets: [
                        {
                            label: 'المسجلين الجدد',
                            data: @json(array_values($registeredData)),
                            backgroundColor: '#007bff',
                            borderColor: '#0056b3',
                            borderWidth: 2,
                            borderRadius: 0,
                            borderSkipped: false
                        },
                        {
                            label: 'المزامنين',
                            data: @json(array_values($syncedData)),
                            backgroundColor: '#28a745',
                            borderColor: '#1e7e34',
                            borderWidth: 2,
                            borderRadius: 0,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.08)' },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2) الحالات الاجتماعية (معتمد / غير معتمد)
            const socialLabels      = @json(array_keys($approvedSocialData));
            const approvedData      = @json(array_values($approvedSocialData));
            const unapprovedData    = @json(array_values($unapprovedSocialData));

            const commonColors = [
                '#007bff', // primary
                '#28a745', // success
                '#17a2b8', // info
                '#ffc107', // warning
                '#dc3545', // danger
            ];

            // دائرة المعتمدين
            const approvedSocialCtx = document.getElementById('approvedSocialChart').getContext('2d');
            new Chart(approvedSocialCtx, {
                type: 'doughnut',
                data: {
                    labels: socialLabels,
                    datasets: [{
                        data: approvedData,
                        backgroundColor: commonColors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 13 },
                                color: '#343a40'
                            }
                        }
                    }
                }
            });

            // دائرة غير المعتمدين
            const unapprovedSocialCtx = document.getElementById('unapprovedSocialChart').getContext('2d');
            new Chart(unapprovedSocialCtx, {
                type: 'doughnut',
                data: {
                    labels: socialLabels,
                    datasets: [{
                        data: unapprovedData,
                        backgroundColor: commonColors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 13 },
                                color: '#343a40'
                            }
                        }
                    }
                }
            });

            // 3) دوائر الجندر لأرباب الأسر
            const genderLabelsApproved   = @json($genderLabelsApproved);   // ['ذكر','أنثى','غير محدد']
            const genderDataApproved     = @json($genderDataApproved);
            const genderLabelsUnapproved = @json($genderLabelsUnapproved);
            const genderDataUnapproved   = @json($genderDataUnapproved);

            const commonGenderColors = [
                '#007bff', // ذكر - primary
                '#ffc107', // أنثى - warning
                '#6c757d', // غير محدد - secondary
            ];

            // دائرة المعتمدين
            const genderApprovedCtx = document.getElementById('genderApprovedChart').getContext('2d');
            new Chart(genderApprovedCtx, {
                type: 'doughnut',
                data: {
                    labels: genderLabelsApproved,
                    datasets: [{
                        data: genderDataApproved,
                        backgroundColor: commonGenderColors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 13 },
                                color: '#343a40'
                            }
                        }
                    }
                }
            });

            // دائرة غير المعتمدين
            const genderUnapprovedCtx = document.getElementById('genderUnapprovedChart').getContext('2d');
            new Chart(genderUnapprovedCtx, {
                type: 'doughnut',
                data: {
                    labels: genderLabelsUnapproved,
                    datasets: [{
                        data: genderDataUnapproved,
                        backgroundColor: commonGenderColors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 13 },
                                color: '#343a40'
                            }
                        }
                    }
                }
            });
        });

    </script>
@endpush
</x-layout>
