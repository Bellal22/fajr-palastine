@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Neighborhood::class])
    @slot('url', route('dashboard.neighborhoods.index'))
    @slot('name', trans('neighborhoods.plural'))
    @slot('active', request()->routeIs('*neighborhoods*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('neighborhoods.actions.list'),
            'url' => route('dashboard.neighborhoods.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Neighborhood::class],
            'active' => request()->routeIs('*neighborhoods.index')
            || request()->routeIs('*neighborhoods.show'),
        ],
        [
            'name' => trans('neighborhoods.actions.create'),
            'url' => route('dashboard.neighborhoods.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Neighborhood::class],
            'active' => request()->routeIs('*neighborhoods.create'),
        ],
    ])
@endcomponent
