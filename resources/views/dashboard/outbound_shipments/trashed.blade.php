<x-layout :title="trans('outbound_shipments.trashed')" :breadcrumbs="['dashboard.outbound_shipments.trashed']">
    @include('dashboard.outbound_shipments.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('outbound_shipments.actions.list') ({{ $outbound_shipments->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\OutboundShipment::class }}"
                    :resource="trans('outbound_shipments.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\OutboundShipment::class }}"
                    :resource="trans('outbound_shipments.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('outbound_shipments.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($outbound_shipments as $outbound_shipment)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$outbound_shipment"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.outbound_shipments.trashed.show', $outbound_shipment) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $outbound_shipment->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.outbound_shipments.partials.actions.show')
                    @include('dashboard.outbound_shipments.partials.actions.restore')
                    @include('dashboard.outbound_shipments.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('outbound_shipments.empty')</td>
            </tr>
        @endforelse

        @if($outbound_shipments->hasPages())
            @slot('footer')
                {{ $outbound_shipments->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
