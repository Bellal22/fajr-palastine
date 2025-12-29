@if($outbound_shipment)
    @if(method_exists($outbound_shipment, 'trashed') && $outbound_shipment->trashed())
        <a href="{{ route('dashboard.outbound_shipments.trashed.show', $outbound_shipment) }}" class="text-decoration-none text-ellipsis">
            {{ $outbound_shipment->name }}
        </a>
    @else
        <a href="{{ route('dashboard.outbound_shipments.show', $outbound_shipment) }}" class="text-decoration-none text-ellipsis">
            {{ $outbound_shipment->name }}
        </a>
    @endif
@else
    ---
@endif