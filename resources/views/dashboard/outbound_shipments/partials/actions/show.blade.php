@if(method_exists($outbound_shipment, 'trashed') && $outbound_shipment->trashed())
    @can('view', $outbound_shipment)
        <a href="{{ route('dashboard.outbound_shipments.trashed.show', $outbound_shipment) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $outbound_shipment)
        <a href="{{ route('dashboard.outbound_shipments.show', $outbound_shipment) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif