@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\ReadyPackage::class])
    @slot('url', route('dashboard.ready_packages.index'))
    @slot('name', trans('ready_packages.plural'))
    @slot('active', request()->routeIs('*ready_packages*'))
    @slot('icon', 'fas fa-box')
    @slot('tree', [
        [
            'name' => trans('ready_packages.actions.list'),
            'url' => route('dashboard.ready_packages.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\ReadyPackage::class],
            'active' => request()->routeIs('*ready_packages.index')
            || request()->routeIs('*ready_packages.show'),
        ],
        [
            'name' => trans('ready_packages.actions.create'),
            'url' => route('dashboard.ready_packages.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\ReadyPackage::class],
            'active' => request()->routeIs('*ready_packages.create'),
        ],
    ])
@endcomponent
