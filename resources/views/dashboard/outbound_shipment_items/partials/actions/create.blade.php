@can('create', \App\Models\OutboundShipmentItem::class)
    <a href="{{ route('dashboard.outbound_shipment_items.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('outbound_shipment_items.actions.create')
    </a>
@endcan
