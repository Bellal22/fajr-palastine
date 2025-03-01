@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Family::class])
    @slot('url', route('dashboard.families.index'))
    @slot('name', trans('families.plural'))
    @slot('active', request()->routeIs('*families*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('families.actions.list'),
            'url' => route('dashboard.families.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Family::class],
            'active' => request()->routeIs('*families.index')
            || request()->routeIs('*families.show'),
        ],
        [
            'name' => trans('families.actions.create'),
            'url' => route('dashboard.families.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Family::class],
            'active' => request()->routeIs('*families.create'),
        ],
    ])
@endcomponent
