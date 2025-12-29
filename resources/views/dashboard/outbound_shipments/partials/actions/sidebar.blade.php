@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\OutboundShipment::class])
    @slot('url', route('dashboard.outbound_shipments.index'))
    @slot('name', trans('outbound_shipments.plural'))
    @slot('active', request()->routeIs('*outbound_shipments*'))
    @slot('icon', 'fas fa-shipping-fast')
    @slot('tree', [
        [
            'name' => trans('outbound_shipments.actions.list'),
            'url' => route('dashboard.outbound_shipments.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\OutboundShipment::class],
            'active' => request()->routeIs('*outbound_shipments.index')
            || request()->routeIs('*outbound_shipments.show'),
        ],
        [
            'name' => trans('outbound_shipments.actions.create'),
            'url' => route('dashboard.outbound_shipments.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\OutboundShipment::class],
            'active' => request()->routeIs('*outbound_shipments.create'),
        ],
    ])
@endcomponent
