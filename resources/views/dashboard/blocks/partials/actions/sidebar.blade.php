@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Block::class])
    @slot('url', route('dashboard.blocks.index'))
    @slot('name', trans('blocks.plural'))
    @slot('active', request()->routeIs('*blocks*'))
    @slot('icon', 'fas fa-user-cog')
    @slot('tree', [
        [
            'name' => trans('blocks.actions.list'),
            'url' => route('dashboard.blocks.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Block::class],
            'active' => request()->routeIs('*blocks.index')
            || request()->routeIs('*blocks.show'),
        ],
        [
            'name' => trans('blocks.actions.create'),
            'url' => route('dashboard.blocks.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Block::class],
            'active' => request()->routeIs('*blocks.create'),
        ],
    ])
@endcomponent
