<x-layout :title="trans('maps.trashed')" :breadcrumbs="['dashboard.maps.trashed']">
    @include('dashboard.maps.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('maps.actions.list') ({{ $maps->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Map::class }}"
                    :resource="trans('maps.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Map::class }}"
                    :resource="trans('maps.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('maps.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($maps as $map)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$map"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.maps.trashed.show', $map) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $map->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.maps.partials.actions.show')
                    @include('dashboard.maps.partials.actions.restore')
                    @include('dashboard.maps.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('maps.empty')</td>
            </tr>
        @endforelse

        @if($maps->hasPages())
            @slot('footer')
                {{ $maps->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
