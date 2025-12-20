@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Region::class])
    @slot('url', route('dashboard.regions.index'))
    @slot('name', trans('regions.plural'))
    @slot('active', request()->routeIs('*regions*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('regions.actions.list'),
            'url' => route('dashboard.regions.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Region::class],
            'active' => request()->routeIs('*regions.index')
            || request()->routeIs('*regions.show'),
        ],
        [
            'name' => trans('regions.actions.create'),
            'url' => route('dashboard.regions.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Region::class],
            'active' => request()->routeIs('*regions.create'),
        ],
    ])
@endcomponent
