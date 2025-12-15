@can('create', \App\Models\InboundShipment::class)
    <a href="{{ route('dashboard.inbound_shipments.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('inbound_shipments.actions.create')
    </a>
@endcan
