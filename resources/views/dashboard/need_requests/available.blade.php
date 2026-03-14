<x-layout :title="'طلبات الاحتياج المتاحة'" :breadcrumbs="['dashboard.need_requests.index']">
    @push('styles')
    <style>
        /* Metronic-like Card & Design System */
        :root {
            --kt-primary: #007bff;
            --kt-primary-light: rgba(0, 123, 255, 0.1);
            --kt-primary-active: #0056b3;
            --kt-gray-100: #f5f8fa;
            --kt-gray-200: #eff2f5;
            --kt-gray-300: #e4e6ef;
            --kt-gray-600: #7e8299;
            --kt-gray-800: #3f4254;
            --kt-success: #50cd89;
            --kt-card-box-shadow: 0 0 20px 0 rgba(76, 87, 125, 0.02);
        }

        .card-metronic {
            border: 0;
            box-shadow: 0 0 20px 0 rgba(76, 87, 125, 0.02);
            background-color: #ffffff;
            border-radius: 0.65rem;
            transition: transform 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-metronic:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px 0 rgba(76, 87, 125, 0.05);
        }

        .card-metronic .card-header {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            flex-wrap: wrap;
            min-height: 70px;
            padding: 0 2.25rem;
            color: var(--kt-gray-800);
            background-color: transparent;
            border-bottom: 1px solid var(--kt-gray-200);
        }

        .card-metronic .card-header .card-title {
            display: flex;
            align-items: center;
            margin: 0.5rem;
            margin-right: 0;
        }

        .card-metronic .card-header .card-title h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--kt-gray-800);
            margin: 0;
        }

        .card-metronic .card-body {
            padding: 2rem 2.25rem;
            flex-grow: 1;
        }

        .card-metronic .card-footer {
            padding: 1.5rem 2.25rem;
            background-color: transparent;
            border-top: 1px solid var(--kt-gray-200);
        }

        /* Metronic Progress Bar */
        .progress-metronic {
            display: flex;
            height: 1rem;
            overflow: hidden;
            font-size: 0.75rem;
            background-color: var(--kt-gray-200);
            border-radius: 0.475rem;
        }

        .progress-metronic .progress-bar {
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #ffffff;
            text-align: center;
            white-space: nowrap;
            background-color: var(--kt-primary);
            transition: width 0.6s ease;
        }

        /* Metronic Badges */
        .badge-light-primary {
            color: var(--kt-primary);
            background-color: var(--kt-primary-light);
        }

        /* List Items */
        .stat-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
        }

        .stat-list li {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--kt-gray-200);
        }

        .stat-list li:last-child {
            border-bottom: 0;
        }

        .stat-list li i {
            font-size: 1.25rem;
            width: 35px;
            color: var(--kt-primary);
        }

        .stat-list li .stat-label {
            flex-grow: 1;
            color: var(--kt-gray-600);
            font-weight: 500;
        }

        .stat-list li .stat-value {
            color: var(--kt-gray-800);
            font-weight: 700;
        }

        /* Primary Button */
        .btn-kt-primary {
            color: #ffffff;
            background-color: var(--kt-primary);
            border: 0;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.475rem;
            transition: color 0.15s ease, background-color 0.15s ease;
            width: 100%;
            text-align: center;
        }

        .btn-kt-primary:hover:not(.disabled):not(:disabled) {
            background-color: var(--kt-primary-active);
            color: #ffffff;
        }

        .btn-kt-light {
            color: var(--kt-gray-800);
            background-color: var(--kt-gray-100);
            font-weight: 600;
            border-radius: 0.475rem;
            padding: 0.75rem 1.5rem;
            border: 0;
            width: 100%;
            text-align: center;
        }

        .btn-kt-light:hover {
            background-color: var(--kt-gray-200);
        }
    </style>
    @endpush

    <div class="d-flex flex-wrap flex-stack mb-6">
        <div class="d-flex flex-column">
            <h1 class="font-weight-bold text-dark mb-1">فرص طلبات الاحتياج المتاحة</h1>
            <div class="text-muted fs-6 fw-bold">هنا تظهر الكوبونات التي تم تفعيلها ويمكنك ترشيح هويات لها.</div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        @forelse($projects as $project)
            @php
                $setting = $project->needRequestProject;
                $deadline = $setting->deadline;
                $limit = $setting->allowed_id_count;
                
                // Find pending request for this supervisor
                $pendingRequest = $project->needRequests->first();
                $currentCount = $pendingRequest ? $pendingRequest->items()->count() : 0;
                $percent = $limit > 0 ? min(100, ($currentCount / $limit) * 100) : 0;
                
                $isExpired = $deadline && $deadline->isPast();
            @endphp

            <div class="col-md-6 col-xl-4 mb-4">
                <div class="card card-metronic">
                    <div class="card-header border-0 pt-5">
                        <div class="card-title">
                            <div class="d-flex flex-column">
                                <h3 class="text-dark fw-bolder fs-3">{{ $project->name }}</h3>
                                <span class="text-muted fw-bold fs-7 mt-1">{{ $project->project_type ?? 'كوبون احتياج' }}</span>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            @if($deadline)
                                <span class="badge {{ $isExpired ? 'badge-light-danger' : 'badge-light-primary' }} fw-bolder px-4 py-3">
                                    <i class="far fa-clock mr-1"></i>
                                    @if($isExpired) منتهي @else {{ $deadline->diffForHumans() }} @endif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <ul class="stat-list">
                            <li>
                                <i class="fas fa-users"></i>
                                <span class="stat-label">العدد الأقصى المسموح</span>
                                <span class="stat-value">{{ $limit ?: 'غير محدد' }}</span>
                            </li>
                            <li>
                                <i class="fas fa-check-double"></i>
                                <span class="stat-label">العدد الذي تم ترشيحه</span>
                                <span class="stat-value text-primary">{{ $currentCount }}</span>
                            </li>
                        </ul>

                        <div class="d-flex justify-content-between fw-bolder fs-6 text-gray-800 mb-2">
                            <span>نسبة الإنجاز</span>
                            <span>{{ round($percent) }}%</span>
                        </div>
                        <div class="progress-metronic w-100 mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="card-footer pt-0">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dashboard.need_requests.bulk_create', ['project_id' => $project->id]) }}" class="btn btn-kt-light">
                                <i class="fas fa-edit mr-2"></i> تعديل تفعيل الطلب
                            </a>
                        @else
                            @if($isExpired)
                                <button class="btn btn-kt-light disabled" disabled>
                                    <i class="fas fa-lock mr-2"></i> انتهى الوقت
                                </button>
                            @elseif($limit > 0 && $currentCount >= $limit)
                                <button class="btn btn-kt-light disabled" disabled>
                                    <i class="fas fa-check-circle mr-2"></i> تم اكتمال العدد
                                </button>
                            @else
                                <a href="{{ route('dashboard.need_requests.create', ['project_id' => $project->id]) }}" class="btn btn-kt-primary">
                                    <i class="fas fa-plus-circle mr-2"></i> 
                                    {{ $currentCount > 0 ? 'إضافة هويات إضافية' : 'بدء الترشيح الآن' }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-metronic p-10 text-center">
                    <div class="card-body py-15">
                        <i class="fas fa-clipboard-list fa-5x text-gray-300 mb-5"></i>
                        <h3 class="text-gray-800 fw-bolder mb-3">لايوجد فرص متاحة حالياً</h3>
                        <p class="text-gray-600 fw-bold fs-6">سيتم إشعارك فور تفعيل أي مشاريع أو كوبونات جديدة لطلبات الاحتياج.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</x-layout>
