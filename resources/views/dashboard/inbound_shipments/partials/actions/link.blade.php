@if($inbound_shipment)
    @if(method_exists($inbound_shipment, 'trashed') && $inbound_shipment->trashed())
        <a href="{{ route('dashboard.inbound_shipments.trashed.show', $inbound_shipment) }}" class="text-decoration-none text-ellipsis">
            {{ $inbound_shipment->name }}
        </a>
    @else
        <a href="{{ route('dashboard.inbound_shipments.show', $inbound_shipment) }}" class="text-decoration-none text-ellipsis">
            {{ $inbound_shipment->name }}
        </a>
    @endif
@else
    ---
@endif