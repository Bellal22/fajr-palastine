<x-layout :title="trans('need_requests.plural')" :breadcrumbs="['dashboard.need_requests.index']">
    @include('dashboard.need_requests.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('need_requests.actions.list') ({{ $need_requests->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\NeedRequest::class }}"
                        :resource="trans('need_requests.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    <div class="d-flex">
                        @include('dashboard.need_requests.partials.actions.create')
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dashboard.need_requests.bulk_create') }}" class="btn btn-info ml-2">
                                <i class="fas fa-layer-group"></i> تفعيل طلبات الاحتياج
                            </a>
                        @endif
                    </div>
                    @include('dashboard.need_requests.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>@lang('need_requests.attributes.supervisor_id')</th>
            <th>@lang('need_requests.attributes.status')</th>
            <th>@lang('need_requests.count')</th>
            <th>@lang('need_requests.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($need_requests as $need_request)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$need_request"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.need_requests.show', $need_request) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $need_request->project->name ?? '---' }}
                    </a>
                </td>
                <td>{{ $need_request->supervisor->name ?? '---' }}</td>
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
                    @include('dashboard.need_requests.partials.actions.edit')
                    @include('dashboard.need_requests.partials.actions.delete')
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
