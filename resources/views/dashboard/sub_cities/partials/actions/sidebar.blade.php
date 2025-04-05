@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\SubCity::class])
    @slot('url', route('dashboard.sub_cities.index'))
    @slot('name', trans('sub_cities.plural'))
    @slot('active', request()->routeIs('*sub_cities*'))
    @slot('icon', 'fas fa-city')
    @slot('tree', [
        [
            'name' => trans('sub_cities.actions.list'),
            'url' => route('dashboard.sub_cities.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\SubCity::class],
            'active' => request()->routeIs('*sub_cities.index')
            || request()->routeIs('*sub_cities.show'),
        ],
        [
            'name' => trans('sub_cities.actions.create'),
            'url' => route('dashboard.sub_cities.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\SubCity::class],
            'active' => request()->routeIs('*sub_cities.create'),
        ],
    ])
@endcomponent
