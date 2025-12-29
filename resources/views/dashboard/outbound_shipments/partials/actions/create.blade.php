@can('create', \App\Models\OutboundShipment::class)
    <a href="{{ route('dashboard.outbound_shipments.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('outbound_shipments.actions.create')
    </a>
@endcan
