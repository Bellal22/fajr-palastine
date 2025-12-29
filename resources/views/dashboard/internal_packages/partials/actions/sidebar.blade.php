@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\InternalPackage::class])
    @slot('url', route('dashboard.internal_packages.index'))
    @slot('name', trans('internal_packages.plural'))
    @slot('active', request()->routeIs('*internal_packages*'))
    @slot('icon', 'fas fa-box-open')
    @slot('tree', [
        [
            'name' => trans('internal_packages.actions.list'),
            'url' => route('dashboard.internal_packages.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\InternalPackage::class],
            'active' => request()->routeIs('*internal_packages.index')
            || request()->routeIs('*internal_packages.show'),
        ],
        [
            'name' => trans('internal_packages.actions.create'),
            'url' => route('dashboard.internal_packages.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\InternalPackage::class],
            'active' => request()->routeIs('*internal_packages.create'),
        ],
    ])
@endcomponent
