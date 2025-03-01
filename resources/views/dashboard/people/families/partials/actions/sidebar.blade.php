@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Person::class])
    @slot('url', route('dashboard.people.index'))
    @slot('name', trans('people.plural'))
    @slot('active', request()->routeIs('*people*'))
    @slot('icon', 'fas fa-th')
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
    ])
@endcomponent
