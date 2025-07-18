<x-layout :title="trans('area_responsibles.trashed')" :breadcrumbs="['dashboard.area_responsibles.trashed']">
    @include('dashboard.area_responsibles.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('area_responsibles.actions.list') ({{ $area_responsibles->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\AreaResponsible::class }}"
                    :resource="trans('area_responsibles.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\AreaResponsible::class }}"
                    :resource="trans('area_responsibles.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('area_responsibles.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($area_responsibles as $area_responsible)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$area_responsible"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.area_responsibles.trashed.show', $area_responsible) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $area_responsible->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.area_responsibles.partials.actions.show')
                    @include('dashboard.area_responsibles.partials.actions.restore')
                    @include('dashboard.area_responsibles.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('area_responsibles.empty')</td>
            </tr>
        @endforelse

        @if($area_responsibles->hasPages())
            @slot('footer')
                {{ $area_responsibles->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
