@component('dashboard::components.sidebarItem')
    @slot('name', trans('need_requests.plural'))
    @slot('icon', 'fas fa-hand-holding-heart')
    @slot('active', request()->routeIs('*need_requests*'))
    @slot('tree', [
        [
            'name' => trans('need_requests.actions.my_requests'),
            'url' => route('dashboard.need_requests.my'),
            'active' => request()->routeIs('*need_requests.my'),
            'can' => ['ability' => 'viewAnyOwn', 'model' => \App\Models\NeedRequest::class],
        ],
        [
            'name' => trans('need_requests.actions.create'),
            'url' => route('dashboard.need_requests.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\NeedRequest::class],
            'active' => request()->routeIs('*need_requests.create'),
        ],
        [
            'name' => trans('need_requests.actions.list'),
            'url' => route('dashboard.need_requests.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\NeedRequest::class],
            'active' => request()->routeIs('*need_requests.index') || request()->routeIs('*need_requests.show'),
        ],
        [
            'name' => trans('need_requests.actions.supervisor_settings'),
            'url' => route('dashboard.need_requests.settings'),
            'can' => ['ability' => 'manageSettings', 'model' => \App\Models\NeedRequest::class],
            'active' => request()->routeIs('*need_requests.settings'),
        ],
        [
            'name' => trans('need_requests.actions.project_settings'),
            'url' => route('dashboard.need_requests.settings.projects'),
            'can' => ['ability' => 'manageSettings', 'model' => \App\Models\NeedRequest::class],
            'active' => request()->routeIs('*need_requests.settings.projects'),
        ],
    ])
@endcomponent
