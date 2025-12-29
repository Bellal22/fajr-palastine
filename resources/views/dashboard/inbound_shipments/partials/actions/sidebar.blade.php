@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\InboundShipment::class])
    @slot('url', route('dashboard.inbound_shipments.index'))
    @slot('name', trans('inbound_shipments.plural'))
    @slot('active', request()->routeIs('*inbound_shipments*'))
    @slot('icon', 'fas fa-truck-loading')
    @slot('tree', [
        [
            'name' => trans('inbound_shipments.actions.list'),
            'url' => route('dashboard.inbound_shipments.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\InboundShipment::class],
            'active' => request()->routeIs('*inbound_shipments.index')
            || request()->routeIs('*inbound_shipments.show'),
        ],
        [
            'name' => trans('inbound_shipments.actions.create'),
            'url' => route('dashboard.inbound_shipments.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\InboundShipment::class],
            'active' => request()->routeIs('*inbound_shipments.create'),
        ],
    ])
@endcomponent
