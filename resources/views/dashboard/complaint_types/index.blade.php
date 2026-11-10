<x-layout title="أنواع الشكاوى" :breadcrumbs="['dashboard.complaint_types.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.complaint_types.partials.filter')
    </div>

    @component('dashboard::components.table-box')

        @slot('title')
            <i class="fas fa-list-alt text-primary mr-1"></i> قائمة أنواع الشكاوى المعرّفة
            <span class="badge badge-primary badge-pill ml-1">{{ count_formatted($types->total()) }}</span>
        @endslot

        {{-- Table Header Actions --}}
        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center">
                    <x-check-all-delete
                            type="{{ \App\Models\ComplaintType::class }}"
                            resource="أنواع الشكاوى">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        <div></div>
                        <a href="{{ route('dashboard.complaint_types.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus"></i> إضافة نوع جديد
                        </a>
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th style="width: 50px">
                <x-check-all></x-check-all>
            </th>
            <th>
                <i class="fas fa-heading"></i> اسم نوع الشكوى
            </th>
            <th class="text-center">
                <i class="fas fa-toggle-on"></i> الحالة
            </th>
            <th class="text-center">
                <i class="fas fa-history"></i> فترة الانتظار (Cooldown)
            </th>
            <th>
                <i class="fas fa-comment-dots"></i> الرد المخصص عند الإيقاف
            </th>
            <th class="d-none d-sm-table-cell">
                <i class="fas fa-calendar"></i> تاريخ الإضافة
            </th>
            <th class="text-center" style="width: 160px">
                <i class="fas fa-cog"></i> الإجراءات
            </th>
        </tr>
        </thead>

        {{-- Table Body --}}
        <tbody>
        @forelse($types as $type)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$type"></x-check-all-item>
                </td>

                {{-- Name --}}
                <td>
                    <a href="{{ route('dashboard.complaint_types.edit', $type) }}"
                       class="text-decoration-none font-weight-bold text-dark">
                        <i class="fas fa-tag text-info mr-1"></i>
                        {{ $type->name }}
                    </a>
                </td>

                {{-- Status --}}
                <td class="text-center">
                    @if($type->is_active)
                        <span class="badge badge-success badge-pill px-3 py-1">
                            <i class="fas fa-check-circle"></i> مفعّل
                        </span>
                    @else
                        <span class="badge badge-danger badge-pill px-3 py-1">
                            <i class="fas fa-ban"></i> مطفي
                        </span>
                    @endif
                </td>

                {{-- Cooldown --}}
                <td class="text-center">
                    <span class="badge badge-info badge-pill px-3 py-1">
                        <i class="fas fa-clock"></i> {{ $type->cooldown_days }} يوم
                    </span>
                </td>

                {{-- Inactive Response --}}
                <td>
                    @if(!$type->is_active)
                        <span class="text-muted small" data-toggle="tooltip" title="{{ $type->inactive_response }}">
                            <i class="fas fa-quote-right text-warning"></i>
                            {{ Str::limit($type->inactive_response ?: 'لا يوجد رد مخصص', 40) }}
                        </span>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>

                {{-- Created At --}}
                <td class="d-none d-sm-table-cell">
                    <span class="text-muted" data-toggle="tooltip"
                          title="{{ $type->created_at->format('Y-m-d H:i') }}">
                        <i class="fas fa-calendar-alt text-info"></i>
                        {{ $type->created_at->diffForHumans() }}
                    </span>
                </td>

                {{-- Actions --}}
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="{{ route('dashboard.complaint_types.edit', $type) }}" class="btn btn-outline-primary btn-sm" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('dashboard.complaint_types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من إزالة هذا النوع؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-list-alt fa-3x mb-3 d-block text-secondary"></i>
                        <h5>لا توجد أنواع شكاوى معرّفة حالياً</h5>
                        <p class="small text-muted mb-0">يمكنك إضافة أنواع شكاوى جديدة وتحديد فترة انتظار الفرد والرد المخصص عند إيقاف أي نوع.</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($types->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        عرض النتائج من {{ $types->firstItem() ?? 0 }} إلى {{ $types->lastItem() ?? 0 }} من أصل {{ $types->total() }}
                    </div>
                    {{ $types->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
