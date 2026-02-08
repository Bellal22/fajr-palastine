<x-layout title="المشاريع" :breadcrumbs="['dashboard.projects.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.projects.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-project-diagram"></i> قائمة المشاريع
            <span class="badge badge-primary badge-pill">{{ number_format($projects->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    {{-- Left Side Actions --}}
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            <x-check-all-delete
                                type="{{ \App\Models\Project::class }}"
                                resource="المشاريع">
                            </x-check-all-delete>
                        </div>
                    </div>

                    {{-- Right Side Actions --}}
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            @include('dashboard.projects.partials.actions.create')
                        </div>

                        <div class="mr-2">
                            @include('dashboard.projects.partials.actions.trashed')
                        </div>

                        <div>
                            <a href="{{ route('dashboard.reports.projects') }}"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar"></i>
                                التقارير
                            </a>
                        </div>
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th style="width: 50px">
                <x-check-all></x-check-all>
            </th>
            <th><i class="fas fa-tag"></i> اسم المشروع</th>
            <th class="d-none d-md-table-cell"><i class="fas fa-calendar-alt"></i> تاريخ البدء</th>
            <th class="d-none d-md-table-cell"><i class="fas fa-calendar-check"></i> تاريخ الانتهاء</th>
            <th class="d-none d-lg-table-cell text-center"><i class="fas fa-users"></i> المستفيدين</th>
            <th class="text-center"><i class="fas fa-user-check"></i> المستلمين</th>
            <th class="text-center"><i class="fas fa-cubes"></i> الكميات</th>
            <th><i class="fas fa-info-circle"></i> الحالة</th>
            <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> الإجراءات</th>
        </tr>
        </thead>

        <tbody>
        @forelse($projects as $project)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$project"></x-check-all-item>
                </td>

                {{-- Project Name --}}
                <td>
                    <a href="{{ route('dashboard.projects.show', $project) }}"
                       class="text-decoration-none font-weight-bold text-primary">
                        <i class="fas fa-project-diagram text-muted"></i>
                        {{ $project->name }}
                    </a>
                    <small class="text-muted d-md-none d-block mt-1">
                        {{ Str::limit($project->description, 30) ?? '-' }}
                    </small>
                </td>

                {{-- Start Date --}}
                <td class="d-none d-md-table-cell">
                    @if($project->start_date)
                        <span class="text-muted">
                            <i class="fas fa-calendar-alt text-info"></i>
                            {{ $project->start_date->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                {{-- End Date --}}
                <td class="d-none d-md-table-cell">
                    @if($project->end_date)
                        <span class="text-muted">
                            <i class="fas fa-calendar-check text-success"></i>
                            {{ $project->end_date->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                {{-- Total Beneficiaries --}}
                <td class="d-none d-lg-table-cell text-center">
                    <span class="badge badge-info badge-pill">
                        <i class="fas fa-users"></i>
                        {{ number_format($project->beneficiaries_count ?? 0) }}
                    </span>
                </td>

                {{-- Received Count --}}
                <td class="text-center">
                    @php
                        $receivedCount = $project->received_count ?? 0;
                        $totalCount = $project->beneficiaries_count ?? 0;
                        $percentage = $totalCount > 0 ? round(($receivedCount / $totalCount) * 100) : 0;
                    @endphp

                    <span class="badge badge-success badge-pill"
                          title="نسبة الإنجاز: {{ $percentage }}%">
                        <i class="fas fa-check-circle"></i>
                        {{ number_format($receivedCount) }}
                    </span>

                    @if($totalCount > 0)
                        <small class="d-block text-muted mt-1">
                            {{ $percentage }}%
                        </small>
                    @endif
                </td>

                {{-- Total Quantity --}}
                <td class="text-center">
                    @php
                        $totalQuantity = $project->total_quantity ?? 0;
                    @endphp

                    @if($totalQuantity > 0)
                        <span class="badge badge-dark badge-pill">
                            <i class="fas fa-cubes"></i>
                            {{ number_format($totalQuantity) }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @if($project->status === 'active')
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> نشط
                        </span>
                    @elseif($project->status === 'completed')
                        <span class="badge badge-primary">
                            <i class="fas fa-flag-checkered"></i> مكتمل
                        </span>
                    @elseif($project->status === 'suspended')
                        <span class="badge badge-warning">
                            <i class="fas fa-pause-circle"></i> معلق
                        </span>
                    @endif
                </td>

                {{-- Actions Dropdown --}}
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton{{ $project->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="dropdownMenuButton{{ $project->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.projects.show', $project) }}"
                               class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="mr-2">عرض التفاصيل</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- التقرير التفصيلي --}}
                            <a href="{{ route('dashboard.reports.projects.show', $project) }}"
                               class="dropdown-item">
                                <i class="fas fa-chart-pie text-info"></i>
                                <span class="mr-2">التقرير التفصيلي</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- تعديل المشروع --}}
                            <a href="{{ route('dashboard.projects.edit', $project) }}"
                               class="dropdown-item">
                                <i class="fas fa-edit text-warning"></i>
                                <span class="mr-2">تعديل</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- حذف المشروع --}}
                            <form action="{{ route('dashboard.projects.destroy', $project) }}"
                                  method="POST"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="dropdown-item"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="mr-2">حذف</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-project-diagram fa-3x mb-3 d-block"></i>
                        <h5>لا توجد مشاريع</h5>
                        <p class="mb-0">لا توجد مشاريع مسجلة حالياً</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($projects->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        عرض {{ $projects->firstItem() ?? 0 }} إلى {{ $projects->lastItem() ?? 0 }} من أصل {{ number_format($projects->total()) }}
                    </div>
                    {{ $projects->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
