@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Supplier::class])
    @slot('url', route('dashboard.suppliers.index'))
    @slot('name', trans('suppliers.plural'))
    @slot('active', request()->routeIs('*suppliers*'))
    @slot('icon', 'fas fa-truck')
    @slot('tree', [
        [
            'name' => trans('suppliers.actions.list'),
            'url' => route('dashboard.suppliers.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Supplier::class],
            'active' => request()->routeIs('*suppliers.index')
            || request()->routeIs('*suppliers.show'),
        ],
        [
            'name' => trans('suppliers.actions.create'),
            'url' => route('dashboard.suppliers.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Supplier::class],
            'active' => request()->routeIs('*suppliers.create'),
        ],
    ])
@endcomponent
