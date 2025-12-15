@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Item::class])
    @slot('url', route('dashboard.items.index'))
    @slot('name', trans('items.plural'))
    @slot('active', request()->routeIs('*items*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('items.actions.list'),
            'url' => route('dashboard.items.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Item::class],
            'active' => request()->routeIs('*items.index')
            || request()->routeIs('*items.show'),
        ],
        [
            'name' => trans('items.actions.create'),
            'url' => route('dashboard.items.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Item::class],
            'active' => request()->routeIs('*items.create'),
        ],
    ])
@endcomponent
