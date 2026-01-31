<x-layout :title="trans('need_requests.trashed')" :breadcrumbs="['dashboard.need_requests.trashed']">
    @include('dashboard.need_requests.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('need_requests.actions.list') ({{ $need_requests->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\NeedRequest::class }}"
                    :resource="trans('need_requests.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\NeedRequest::class }}"
                    :resource="trans('need_requests.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('need_requests.attributes.name')</th>
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
                    <a href="{{ route('dashboard.need_requests.trashed.show', $need_request) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $need_request->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.need_requests.partials.actions.show')
                    @include('dashboard.need_requests.partials.actions.restore')
                    @include('dashboard.need_requests.partials.actions.forceDelete')
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
</x-layout>
