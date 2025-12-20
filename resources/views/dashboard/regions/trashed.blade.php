<x-layout :title="trans('regions.trashed')" :breadcrumbs="['dashboard.regions.trashed']">
    @include('dashboard.regions.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('regions.actions.list') ({{ $regions->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Region::class }}"
                    :resource="trans('regions.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Region::class }}"
                    :resource="trans('regions.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('regions.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($regions as $region)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$region"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.regions.trashed.show', $region) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $region->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.regions.partials.actions.show')
                    @include('dashboard.regions.partials.actions.restore')
                    @include('dashboard.regions.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('regions.empty')</td>
            </tr>
        @endforelse

        @if($regions->hasPages())
            @slot('footer')
                {{ $regions->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
