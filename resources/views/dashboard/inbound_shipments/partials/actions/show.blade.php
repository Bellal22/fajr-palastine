@if(method_exists($inbound_shipment, 'trashed') && $inbound_shipment->trashed())
    @can('view', $inbound_shipment)
        <a href="{{ route('dashboard.inbound_shipments.trashed.show', $inbound_shipment) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $inbound_shipment)
        <a href="{{ route('dashboard.inbound_shipments.show', $inbound_shipment) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif