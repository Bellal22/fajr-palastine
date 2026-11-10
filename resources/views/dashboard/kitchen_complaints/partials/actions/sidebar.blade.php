@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\KitchenComplaint::class])
    @slot('url', route('dashboard.kitchen_complaints.index'))
    @slot('name', trans('kitchen_complaints.plural'))
    @slot('active', request()->routeIs('*kitchen_complaints*'))
    @slot('icon', 'fas fa-utensils')
    @slot('tree', [
        [
            'name' => trans('kitchen_complaints.actions.list'),
            'url' => route('dashboard.kitchen_complaints.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\KitchenComplaint::class],
            'active' => request()->routeIs('*kitchen_complaints.index')
            || request()->routeIs('*kitchen_complaints.show'),
        ],
    ])
@endcomponent
