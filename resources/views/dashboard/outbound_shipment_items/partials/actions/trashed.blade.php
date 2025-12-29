@can('viewAnyTrash', \App\Models\OutboundShipmentItem::class)
    <a href="{{ route('dashboard.outbound_shipment_items.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('outbound_shipment_items.trashed')
    </a>
@endcan
