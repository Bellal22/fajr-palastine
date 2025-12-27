@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Location::class])
    @slot('url', route('dashboard.locations.index'))
    @slot('name', trans('locations.plural'))
    @slot('active', request()->routeIs('*locations*'))
    @slot('icon', 'fas fa-map-pin')
    @slot('tree', [
        [
            'name' => trans('locations.actions.list'),
            'url' => route('dashboard.locations.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Location::class],
            'active' => request()->routeIs('*locations.index')
            || request()->routeIs('*locations.show'),
        ],
        [
            'name' => trans('locations.actions.create'),
            'url' => route('dashboard.locations.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Location::class],
            'active' => request()->routeIs('*locations.create'),
        ],
    ])
@endcomponent
