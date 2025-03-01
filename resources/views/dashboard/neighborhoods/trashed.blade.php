<x-layout :title="trans('neighborhoods.trashed')" :breadcrumbs="['dashboard.neighborhoods.trashed']">
    @include('dashboard.neighborhoods.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('neighborhoods.actions.list') ({{ $neighborhoods->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Neighborhood::class }}"
                    :resource="trans('neighborhoods.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Neighborhood::class }}"
                    :resource="trans('neighborhoods.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('neighborhoods.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($neighborhoods as $neighborhood)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$neighborhood"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.neighborhoods.trashed.show', $neighborhood) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $neighborhood->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.neighborhoods.partials.actions.show')
                    @include('dashboard.neighborhoods.partials.actions.restore')
                    @include('dashboard.neighborhoods.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('neighborhoods.empty')</td>
            </tr>
        @endforelse

        @if($neighborhoods->hasPages())
            @slot('footer')
                {{ $neighborhoods->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
