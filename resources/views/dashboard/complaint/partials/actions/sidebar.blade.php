@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Complaint::class])
    @slot('url', route('dashboard.complaint.index'))
    @slot('name', trans('complaint.plural'))
    @slot('active', request()->routeIs('*complaint*'))
    @slot('icon', 'fas fa-th')
    @slot('tree', [
        [
            'name' => trans('complaint.actions.list'),
            'url' => route('dashboard.complaint.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Complaint::class],
            'active' => request()->routeIs('*complaint.index')
            || request()->routeIs('*complaint.show'),
        ],
        [
            'name' => trans('complaint.actions.create'),
            'url' => route('dashboard.complaint.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\Complaint::class],
            'active' => request()->routeIs('*complaint.create'),
        ],
    ])
@endcomponent
