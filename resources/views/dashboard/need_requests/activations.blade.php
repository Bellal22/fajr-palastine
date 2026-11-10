<x-layout :title="'سجل تفعيلات طلبات الاحتياج'" :breadcrumbs="['dashboard.need_requests.index']">
    @push('styles')
    <style>
        .bg-light-soft { background-color: #f8f9fa; }
        .h-6px { height: 6px; }
    </style>
    @endpush

    <div class="mb-5 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="font-weight-bold">تاريخ تفعيلات طلبات الاحتياج</h1>
            <p class="text-muted">هنا يظهر سجل كامل بكل جولات التفعيل التي تمت (نشطة ومؤرشفة).</p>
        </div>
        <a href="{{ route('dashboard.need_requests.bulk_create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> تفعيل طلب جديد
        </a>
    </div>

    <div class="row">
        @forelse($activations as $activation)
            @php
                $isExpired = $activation->deadline && $activation->deadline->isPast();
                $isEnabled = $activation->is_enabled;
                $deadline = $activation->deadline;
                
                // Real-time counting for this activation
                $itemsCount = $activation->project ? \App\Models\NeedRequestItem::whereHas('needRequest', function($q) use ($activation) {
                    $q->where('project_id', $activation->project_id)->where('created_at', '>=', $activation->created_at);
                })->count() : 0;
                
                $limit = $activation->allowed_id_count;
                $percent = $limit > 0 ? min(100, ($itemsCount / $limit) * 100) : 0;
            @endphp
            <div class="col-xl-6 mb-5">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div style="height: 5px; width: 100%; background-color: {{ !$isEnabled ? '#7e8299' : ($isExpired ? '#f1416c' : '#50cd89') }}"></div>
                    
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-5">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px mr-4" style="width: 50px; height: 50px; background: #f1faff; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <i class="fas fa-layer-group text-primary fs-2"></i>
                                </div>
                                <div>
                                    <h3 class="text-dark fw-bolder fs-4 mb-1">
                                        {{ $activation->project->name ?? '---' }}
                                        <span class="badge {{ !$isEnabled ? 'badge-secondary' : ($isExpired ? 'badge-light-danger text-danger' : 'badge-light-success text-success') }} ms-2 px-3 py-1" style="font-size: 0.7rem">
                                            @if(!$isEnabled) مخفي @elseif($isExpired) منتهي @else نشط @endif
                                        </span>
                                    </h3>
                                    <span class="text-muted small">
                                        <i class="far fa-clock mr-1"></i> تفعيل التاريخ: {{ $activation->created_at->format('Y-m-d H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-light" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="z-index: 1050;">
                                    <a class="dropdown-item" href="{{ route('dashboard.need_requests.bulk_create', ['project_id' => $activation->project_id]) }}">
                                        <i class="fas fa-edit mr-2 text-warning"></i> تعديل/تمديد
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('dashboard.need_requests.activations.destroy', $activation->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل نهائياً من الأرشيف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash-alt mr-2"></i> حذف من السجل
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-6" style="margin-bottom: 20px;">
                            <div class="col-4">
                                <div class="border border-dashed border-gray-300 rounded text-center py-3 px-2" style="border: 1px dashed #ccc; border-radius: 8px;">
                                    <span class="fs-6 fw-bolder text-gray-800 d-block">{{ $limit ?: '∞' }}</span>
                                    <span class="fs-8 text-muted uppercase" style="font-size: 0.7rem;">المطلوب</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border border-dashed border-gray-300 rounded text-center py-3 px-2" style="border: 1px dashed #ccc; border-radius: 8px;">
                                    <span class="fs-6 fw-bolder text-primary d-block">{{ $itemsCount }}</span>
                                    <span class="fs-8 text-muted uppercase" style="font-size: 0.7rem;">المرشح</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border border-dashed border-gray-300 rounded text-center py-3 px-2" style="border: 1px dashed #ccc; border-radius: 8px;">
                                    <span class="fs-6 fw-bolder text-success d-block">{{ round($percent) }}%</span>
                                    <span class="fs-8 text-muted uppercase" style="font-size: 0.7rem;">الإنجاز</span>
                                </div>
                            </div>
                        </div>

                        <div class="progress w-100 mb-6" style="background-color: #f1faff; height: 8px; border-radius: 4px; margin-bottom: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%; height: 100%;"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center bg-light p-4 rounded" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                             <div class="d-flex flex-column">
                                <span class="text-gray-400 small mb-1" style="font-size: 0.75rem; color: #777;">المشرفون المختصون:</span>
                                <div class="d-flex flex-wrap">
                                    @forelse($activation->supervisors as $sup)
                                        <span class="badge badge-light-info text-info mr-2 mb-1" style="background: #e1f5fe; color: #0288d1; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">{{ $sup->name }}</span>
                                    @empty
                                        <span class="text-muted italic small">متاح لجميع المشرفين</span>
                                    @endforelse
                                </div>
                             </div>
                             @if($deadline)
                                <div class="text-end">
                                    <span class="badge {{ $isExpired ? 'badge-danger' : 'badge-primary' }} p-2" style="padding: 8px !important;">
                                        <i class="far fa-calendar-times mr-1"></i>
                                        {{ $deadline->format('Y-m-d') }}
                                    </span>
                                </div>
                             @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-20">
                <i class="fas fa-history fa-4x text-gray-300 mb-5"></i>
                <h3 class="text-muted">لا يوجد سجل تفعيلات حتى الآن</h3>
            </div>
        @endforelse
    </div>
</x-layout>
