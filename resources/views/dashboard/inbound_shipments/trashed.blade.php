<x-layout :title="trans('inbound_shipments.trashed')" :breadcrumbs="['dashboard.inbound_shipments.trashed']">
    @include('dashboard.inbound_shipments.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('inbound_shipments.actions.list') ({{ $inbound_shipments->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\InboundShipment::class }}"
                    :resource="trans('inbound_shipments.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\InboundShipment::class }}"
                    :resource="trans('inbound_shipments.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('inbound_shipments.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($inbound_shipments as $inbound_shipment)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$inbound_shipment"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.inbound_shipments.trashed.show', $inbound_shipment) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $inbound_shipment->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.inbound_shipments.partials.actions.show')
                    @include('dashboard.inbound_shipments.partials.actions.restore')
                    @include('dashboard.inbound_shipments.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('inbound_shipments.empty')</td>
            </tr>
        @endforelse

        @if($inbound_shipments->hasPages())
            @slot('footer')
                {{ $inbound_shipments->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
