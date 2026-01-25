@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Choose::class])
    @slot('url', route('dashboard.chooses.index'))
    @slot('name', trans('chooses.plural'))
    @slot('active', request()->routeIs('*chooses*'))
    @slot('icon', 'fas fa-cogs')
    @slot('tree', [
        [
            'name' => trans('chooses.actions.list'),
            'url' => route('dashboard.chooses.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Choose::class],
            'active' => request()->routeIs('*chooses.index')
            || request()->routeIs('*chooses.show'),
        ],
        [
            'name' => trans('chooses.actions.create'),
            'url' => route('dashboard.chooses.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Choose::class],
            'active' => request()->routeIs('*chooses.create'),
        ],
    ])
@endcomponent
