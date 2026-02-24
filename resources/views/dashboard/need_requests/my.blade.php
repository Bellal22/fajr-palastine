<x-layout :title="trans('need_requests.actions.my_requests')" :breadcrumbs="['dashboard.need_requests.index']">
    @component('dashboard::components.table-box')
        @slot('title')
            @lang('need_requests.actions.my_requests') ({{ $need_requests->total() }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex justify-content-end">
                    @include('dashboard.need_requests.partials.actions.create')
                </div>
            </th>
        </tr>
        <tr>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>@lang('need_requests.attributes.status')</th>
            <th>@lang('need_requests.count')</th>
            <th>@lang('need_requests.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($need_requests as $need_request)
            <tr>
                <td>
                    <a href="{{ route('dashboard.need_requests.show', $need_request) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $need_request->project->name ?? '---' }}
                    </a>
                </td>
                <td>
                    @php
                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                        $color = $statusColors[$need_request->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">
                        {{ trans('need_requests.statuses.'.$need_request->status) }}
                    </span>
                </td>
                <td>{{ $need_request->items_count }}</td>
                <td>{{ $need_request->created_at->format('Y-m-d') }}</td>

                <td style="width: 160px">
                    @include('dashboard.need_requests.partials.actions.show')
                    @if($need_request->isPending())
                        @include('dashboard.need_requests.partials.actions.edit')
                        @include('dashboard.need_requests.partials.actions.delete')
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('need_requests.empty')</td>
            </tr>
        @endforelse

        @if($need_requests->hasPages())
            @slot('footer')
                {{ $need_requests->links() }}
            @endslot
        @endif
    @endcomponent
    @include('dashboard.need_requests.partials.skipped_modal')
</x-layout>
