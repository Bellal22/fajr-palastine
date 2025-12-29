@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Project::class])
    @slot('url', route('dashboard.projects.index'))
    @slot('name', trans('projects.plural'))
    @slot('active', request()->routeIs('*projects*'))
    @slot('icon', 'fas fa-project-diagram')
    @slot('tree', [
        [
            'name' => trans('projects.actions.list'),
            'url' => route('dashboard.projects.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Project::class],
            'active' => request()->routeIs('*projects.index')
            || request()->routeIs('*projects.show'),
        ],
        [
            'name' => trans('projects.actions.create'),
            'url' => route('dashboard.projects.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Project::class],
            'active' => request()->routeIs('*projects.create'),
        ],
    ])
@endcomponent
