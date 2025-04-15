<x-layout :title="trans('suppliers.plural')" :breadcrumbs="['dashboard.suppliers.index']">
    @include('dashboard.suppliers.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('suppliers.actions.list') ({{ $suppliers->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Supplier::class }}"
                        :resource="trans('suppliers.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.suppliers.partials.actions.create')
                    @include('dashboard.suppliers.partials.actions.trashed')
                </div>
            </div>
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
                    <a href="{{ route('dashboard.suppliers.show', $supplier) }}"
                       class="text-decoration-none text-ellipsis">

                        <img src="{{ $supplier->getFirstMediaUrl() }}"
                             alt="Image"
                             class="img-circle img-size-32 mr-2" style="height: 32px;">
                        {{ $supplier->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.suppliers.partials.actions.show')
                    @include('dashboard.suppliers.partials.actions.edit')
                    @include('dashboard.suppliers.partials.actions.delete')
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
