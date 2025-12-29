@if(method_exists($outbound_shipment_item, 'trashed') && $outbound_shipment_item->trashed())
    @can('view', $outbound_shipment_item)
        <a href="{{ route('dashboard.outbound_shipment_items.trashed.show', $outbound_shipment_item) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $outbound_shipment_item)
        <a href="{{ route('dashboard.outbound_shipment_items.show', $outbound_shipment_item) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif