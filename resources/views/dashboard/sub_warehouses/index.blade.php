<x-layout :title="trans('sub_warehouses.plural')" :breadcrumbs="['dashboard.sub_warehouses.index']">
    @include('dashboard.sub_warehouses.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('sub_warehouses.actions.list') ({{ $sub_warehouses->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\SubWarehouse::class }}"
                        :resource="trans('sub_warehouses.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.sub_warehouses.partials.actions.create')
                    @include('dashboard.sub_warehouses.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('sub_warehouses.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sub_warehouses as $sub_warehouse)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$sub_warehouse"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.sub_warehouses.show', $sub_warehouse) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $sub_warehouse->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.sub_warehouses.partials.actions.show')
                    @include('dashboard.sub_warehouses.partials.actions.edit')
                    @include('dashboard.sub_warehouses.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('sub_warehouses.empty')</td>
            </tr>
        @endforelse

        @if($sub_warehouses->hasPages())
            @slot('footer')
                {{ $sub_warehouses->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
