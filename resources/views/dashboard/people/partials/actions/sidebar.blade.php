@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Person::class])
    @slot('url', route('dashboard.people.index'))
    @slot('name', trans('people.plural'))
    @slot('active', request()->routeIs('*people*'))
    @slot('icon', 'fas fa-users')
    @slot('tree', [
        [
            'name' => trans('people.actions.list'),
            'url' => route('dashboard.people.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
            'active' => request()->routeIs('*people.index')
                || request()->routeIs('*people.show'),
        ],
        [
            'name' => trans('people.actions.create'),
            'url' => route('dashboard.people.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Person::class],
            'active' => request()->routeIs('*people.create'),
        ],
        [
            'name' => trans('people.actions.view'),
            'url' => route('dashboard.people.view'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
            'active' => request()->routeIs('*people.view'),
            'icon' => 'fa-list',
        ],
        [
            'name' => trans('people.actions.general_search'),
            'url' => route('dashboard.people.general_search'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
            'active' => request()->routeIs('*people.general_search'),
            'icon' => 'fa-id-card',
        ],
        [
            'name' => trans('people.actions.search'),
            'url' => route('dashboard.people.search'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
            'active' => request()->routeIs('*people.search'),
            'icon' => 'fa-search',
        ],
    ])
@endcomponent
