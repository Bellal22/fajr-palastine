@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\AreaResponsible::class])
    @slot('url', route('dashboard.area_responsibles.index'))
    @slot('name', trans('area_responsibles.plural'))
    @slot('active', request()->routeIs('*area_responsibles*'))
    @slot('icon', 'fas fa-map-marked-alt')
    @slot('tree', [
        [
            'name' => trans('area_responsibles.actions.list'),
            'url' => route('dashboard.area_responsibles.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\AreaResponsible::class],
            'active' => request()->routeIs('*area_responsibles.index')
            || request()->routeIs('*area_responsibles.show'),
        ],
        [
            'name' => trans('area_responsibles.actions.create'),
            'url' => route('dashboard.area_responsibles.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\AreaResponsible::class],
            'active' => request()->routeIs('*area_responsibles.create'),
        ],
    ])
@endcomponent
