@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\SubWarehouse::class])
    @slot('url', route('dashboard.sub_warehouses.index'))
    @slot('name', trans('sub_warehouses.plural'))
    @slot('active', request()->routeIs('*sub_warehouses*'))
    @slot('icon', 'fas fa-warehouse')
    @slot('tree', [
        [
            'name' => trans('sub_warehouses.actions.list'),
            'url' => route('dashboard.sub_warehouses.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\SubWarehouse::class],
            'active' => request()->routeIs('*sub_warehouses.index')
            || request()->routeIs('*sub_warehouses.show'),
        ],
        [
            'name' => trans('sub_warehouses.actions.create'),
            'url' => route('dashboard.sub_warehouses.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\SubWarehouse::class],
            'active' => request()->routeIs('*sub_warehouses.create'),
        ],
    ])
@endcomponent
