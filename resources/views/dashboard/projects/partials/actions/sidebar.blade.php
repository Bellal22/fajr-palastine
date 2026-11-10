@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('manage.projects') || auth()->user()->hasPermission('manage.project_deliveries') || auth()->user()->hasPermission('manage.project_reports')))
@component('dashboard::components.sidebarItem')
    @slot('url', route('dashboard.projects.index'))
    @slot('name', trans('projects.plural'))
    @slot('active', (request()->routeIs('dashboard.projects.*') || request()->routeIs('dashboard.reports.projects')) && !request()->routeIs('dashboard.custom_projects.*') && !request()->routeIs('dashboard.rrm.*') && request('type') !== 'rrm' && !request()->filled('custom_project_id'))
    @slot('icon', 'fas fa-project-diagram')
    @slot('tree', [
        [
            'name' => trans('projects.actions.list'),
            'url' => route('dashboard.projects.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Project::class],
            'active' => request()->routeIs('dashboard.projects.index')
            || request()->routeIs('dashboard.projects.show')
            || request()->routeIs('dashboard.projects.edit'),
        ],
        [
            'name' => trans('projects.actions.create'),
            'url' => route('dashboard.projects.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Project::class],
            'active' => request()->routeIs('dashboard.projects.create'),
        ],
        ...((auth()->user()->isAdmin() || auth()->user()->hasPermission('manage.projects') || auth()->user()->hasPermission('manage.project_deliveries')) ? [
            [
                'name' => 'تسليم الطرود / الكوبونات',
                'url' => route('dashboard.projects.deliveries'),
                'active' => request()->routeIs('dashboard.projects.deliveries*') && request('type') !== 'rrm' && !request()->filled('custom_project_id'),
            ],
        ] : []),
        ...((auth()->user()->isAdmin() || auth()->user()->hasPermission('manage.projects') || auth()->user()->hasPermission('manage.project_reports')) ? [
            [
                'name' => 'تقارير الكوبونات',
                'url' => route('dashboard.reports.projects'),
                'active' => request()->routeIs('dashboard.reports.projects'),
            ],
        ] : []),
    ])
@endcomponent
@endif
