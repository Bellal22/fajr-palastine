@can('viewAnyTrash', \App\Models\OutboundShipment::class)
    <a href="{{ route('dashboard.outbound_shipments.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('outbound_shipments.trashed')
    </a>
@endcan
