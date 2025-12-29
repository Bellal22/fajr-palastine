@if($outbound_shipment_item)
    @if(method_exists($outbound_shipment_item, 'trashed') && $outbound_shipment_item->trashed())
        <a href="{{ route('dashboard.outbound_shipment_items.trashed.show', $outbound_shipment_item) }}" class="text-decoration-none text-ellipsis">
            {{ $outbound_shipment_item->name }}
        </a>
    @else
        <a href="{{ route('dashboard.outbound_shipment_items.show', $outbound_shipment_item) }}" class="text-decoration-none text-ellipsis">
            {{ $outbound_shipment_item->name }}
        </a>
    @endif
@else
    ---
@endif