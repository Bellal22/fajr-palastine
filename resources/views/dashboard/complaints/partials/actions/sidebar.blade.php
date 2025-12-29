@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Complaint::class])
    @slot('url', route('dashboard.complaints.index'))
    @slot('name', trans('complaints.plural'))
    @slot('active', request()->routeIs('*complaints*'))
    @slot('icon', 'fas fa-bullhorn')
    @slot('tree', [
        [
            'name' => trans('complaints.actions.list'),
            'url' => route('dashboard.complaints.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Complaint::class],
            'active' => request()->routeIs('*complaints.index')
            || request()->routeIs('*complaints.show'),
        ],
        [
            'name' => trans('complaints.actions.create'),
            'url' => route('dashboard.complaints.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Complaint::class],
            'active' => request()->routeIs('*complaints.create'),
        ],
    ])
@endcomponent
