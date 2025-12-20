<x-layout :title="trans('locations.trashed')" :breadcrumbs="['dashboard.locations.trashed']">
    @include('dashboard.locations.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('locations.actions.list') ({{ $locations->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Location::class }}"
                    :resource="trans('locations.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Location::class }}"
                    :resource="trans('locations.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('locations.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($locations as $location)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$location"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.locations.trashed.show', $location) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $location->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.locations.partials.actions.show')
                    @include('dashboard.locations.partials.actions.restore')
                    @include('dashboard.locations.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('locations.empty')</td>
            </tr>
        @endforelse

        @if($locations->hasPages())
            @slot('footer')
                {{ $locations->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
