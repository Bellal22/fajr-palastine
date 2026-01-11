<x-layout :title="trans('complaints.plural')" :breadcrumbs="['dashboard.complaints.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.complaints.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-clipboard-list"></i> @lang('complaints.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($complaints->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            <x-check-all-delete
                                type="{{ \App\Models\Complaint::class }}"
                                :resource="trans('complaints.plural')">
                            </x-check-all-delete>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            @include('dashboard.complaints.partials.actions.create')
                        </div>

                        <div>
                            @include('dashboard.complaints.partials.actions.trashed')
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
            <th><i class="fas fa-id-card"></i> @lang('complaints.attributes.id_num')</th>
            <th><i class="fas fa-heading"></i> @lang('complaints.attributes.complaint_title')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-align-left"></i> @lang('complaints.attributes.complaint_text')</th>
            <th class="d-none d-md-table-cell"><i class="fas fa-calendar"></i> @lang('complaints.attributes.created_at')</th>
            <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> @lang('complaints.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($complaints as $complaint)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$complaint"></x-check-all-item>
                </td>

                {{-- ID Number --}}
                <td>
                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                       class="text-decoration-none font-weight-bold text-primary">
                        <i class="fas fa-id-card text-muted"></i>
                        {{ $complaint->id_num }}
                    </a>
                </td>

                {{-- Complaint Title --}}
                <td>
                    <div class="font-weight-bold">{{ Str::limit($complaint->complaint_title, 50) }}</div>
                    @if($complaint->response)
                        <small class="text-success">
                            <i class="fas fa-check-circle"></i> @lang('complaints.status.responded')
                        </small>
                    @endif
                </td>

                {{-- Complaint Text --}}
                <td class="d-none d-lg-table-cell">
                    <span class="text-muted">{{ Str::limit($complaint->complaint_text, 60) }}</span>
                </td>

                {{-- Created At --}}
                <td class="d-none d-md-table-cell">
                    <span class="text-muted">
                        <i class="fas fa-clock"></i>
                        {{ $complaint->created_at->diffForHumans() }}
                    </span>
                </td>

                {{-- Actions --}}
                <td class="text-center">
                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                    class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                        @lang('complaints.actions.show')
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                        <h5>@lang('complaints.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($complaints->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('complaints.pagination_info', [
                            'from' => $complaints->firstItem() ?? 0,
                            'to' => $complaints->lastItem() ?? 0,
                            'total' => $complaints->total()
                        ])
                    </div>
                    {{ $complaints->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
