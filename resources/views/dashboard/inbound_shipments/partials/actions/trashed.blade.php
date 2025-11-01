@can('viewAnyTrash', \App\Models\InboundShipment::class)
    <a href="{{ route('dashboard.inbound_shipments.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('inbound_shipments.trashed')
    </a>
@endcan
