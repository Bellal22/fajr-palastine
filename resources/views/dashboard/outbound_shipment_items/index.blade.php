<x-layout :title="trans('outbound_shipment_items.plural')" :breadcrumbs="['dashboard.outbound_shipment_items.index']">
    @include('dashboard.outbound_shipment_items.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('outbound_shipment_items.actions.list') ({{ $outbound_shipment_items->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\OutboundShipmentItem::class }}"
                        :resource="trans('outbound_shipment_items.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.outbound_shipment_items.partials.actions.create')
                    @include('dashboard.outbound_shipment_items.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('outbound_shipment_items.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($outbound_shipment_items as $outbound_shipment_item)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$outbound_shipment_item"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.outbound_shipment_items.show', $outbound_shipment_item) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $outbound_shipment_item->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.outbound_shipment_items.partials.actions.show')
                    @include('dashboard.outbound_shipment_items.partials.actions.edit')
                    @include('dashboard.outbound_shipment_items.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('outbound_shipment_items.empty')</td>
            </tr>
        @endforelse

        @if($outbound_shipment_items->hasPages())
            @slot('footer')
                {{ $outbound_shipment_items->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
