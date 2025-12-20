@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Map::class])
    @slot('url', route('dashboard.maps.index'))
    @slot('name', trans('maps.plural'))
    @slot('active', request()->routeIs('*maps*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('maps.actions.list'),
            'url' => route('dashboard.maps.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Map::class],
            'active' => request()->routeIs('*maps.index')
            || request()->routeIs('*maps.show'),
        ],
        [
            'name' => trans('maps.actions.create'),
            'url' => route('dashboard.maps.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Map::class],
            'active' => request()->routeIs('*maps.create'),
        ],
    ])
@endcomponent
