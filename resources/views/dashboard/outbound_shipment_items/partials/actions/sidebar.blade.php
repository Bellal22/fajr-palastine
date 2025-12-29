@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\OutboundShipmentItem::class])
    @slot('url', route('dashboard.outbound_shipment_items.index'))
    @slot('name', trans('outbound_shipment_items.plural'))
    @slot('active', request()->routeIs('*outbound_shipment_items*'))
    @slot('icon', 'fas fa-list-ul')
    @slot('tree', [
        [
            'name' => trans('outbound_shipment_items.actions.list'),
            'url' => route('dashboard.outbound_shipment_items.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\OutboundShipmentItem::class],
            'active' => request()->routeIs('*outbound_shipment_items.index')
            || request()->routeIs('*outbound_shipment_items.show'),
        ],
        [
            'name' => trans('outbound_shipment_items.actions.create'),
            'url' => route('dashboard.outbound_shipment_items.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\OutboundShipmentItem::class],
            'active' => request()->routeIs('*outbound_shipment_items.create'),
        ],
    ])
@endcomponent
