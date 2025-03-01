<x-layout :title="trans('sub_cities.trashed')" :breadcrumbs="['dashboard.sub_cities.trashed']">
    @include('dashboard.sub_cities.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('sub_cities.actions.list') ({{ $sub_cities->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\SubCity::class }}"
                    :resource="trans('sub_cities.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\SubCity::class }}"
                    :resource="trans('sub_cities.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('sub_cities.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sub_cities as $sub_city)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$sub_city"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.sub_cities.trashed.show', $sub_city) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $sub_city->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.sub_cities.partials.actions.show')
                    @include('dashboard.sub_cities.partials.actions.restore')
                    @include('dashboard.sub_cities.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('sub_cities.empty')</td>
            </tr>
        @endforelse

        @if($sub_cities->hasPages())
            @slot('footer')
                {{ $sub_cities->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
