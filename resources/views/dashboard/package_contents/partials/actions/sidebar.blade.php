@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\PackageContent::class])
    @slot('url', route('dashboard.package_contents.index'))
    @slot('name', trans('package_contents.plural'))
    @slot('active', request()->routeIs('*package_contents*'))
    @slot('icon', 'fas fa-layer-group')
    @slot('tree', [
        [
            'name' => trans('package_contents.actions.list'),
            'url' => route('dashboard.package_contents.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\PackageContent::class],
            'active' => request()->routeIs('*package_contents.index')
            || request()->routeIs('*package_contents.show'),
        ],
        [
            'name' => trans('package_contents.actions.create'),
            'url' => route('dashboard.package_contents.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\PackageContent::class],
            'active' => request()->routeIs('*package_contents.create'),
        ],
    ])
@endcomponent
