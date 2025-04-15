<x-layout :title="trans('suppliers.trashed')" :breadcrumbs="['dashboard.suppliers.trashed']">
    @include('dashboard.suppliers.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('suppliers.actions.list') ({{ $suppliers->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Supplier::class }}"
                    :resource="trans('suppliers.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Supplier::class }}"
                    :resource="trans('suppliers.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('suppliers.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$supplier"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.suppliers.trashed.show', $supplier) }}"
                       class="text-decoration-none text-ellipsis">

                        <img src="{{ $supplier->getFirstMediaUrl() }}"
                             alt="Image"
                             class="img-circle img-size-32 mr-2" style="height: 32px;">
                        {{ $supplier->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.suppliers.partials.actions.show')
                    @include('dashboard.suppliers.partials.actions.restore')
                    @include('dashboard.suppliers.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('suppliers.empty')</td>
            </tr>
        @endforelse

        @if($suppliers->hasPages())
            @slot('footer')
                {{ $suppliers->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
