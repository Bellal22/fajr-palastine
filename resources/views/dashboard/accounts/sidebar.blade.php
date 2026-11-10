@if(auth()->user()->hasPermission('manage.admins')
    || auth()->user()->hasPermission('manage.supervisors')
    || auth()->user()->hasPermission('manage.distributors')
    || auth()->user()->hasPermission('manage.customers')
    || auth()->user()->hasPermission('manage.researchers')
    || auth()->user()->hasPermission('manage.kitchen_monitors'))
    @component('dashboard::components.sidebarItem')
        @slot('url', '#')
        @slot('name', trans('users.plural'))
        @slot('active', request()->routeIs('*admins*') || request()->routeIs('*customers*') || request()->routeIs('*supervisors*') || request()->routeIs('*distributors*') || request()->routeIs('*researchers*') || request()->routeIs('*kitchen_monitors*'))
        @slot('icon', 'fas fa-users')
        @slot('tree', [
            [
                'name' => trans('admins.plural'),
                'url' => route('dashboard.admins.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Admin::class],
                'active' => request()->routeIs('*admins*'),
            ],
            [
                'name' => trans('supervisors.plural'),
                'url' => route('dashboard.supervisors.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Supervisor::class],
                'active' => request()->routeIs('*supervisors*'),
            ],
            [
                'name' => 'الموزعين',
                'url' => route('dashboard.distributors.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Distributor::class],
                'active' => request()->routeIs('*distributors.index*'),
            ],
            [
                'name' => trans('researchers.plural'),
                'url' => route('dashboard.researchers.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Researcher::class],
                'active' => request()->routeIs('*researchers*'),
            ],
            [
                'name' => 'متابعي المطبخ',
                'url' => route('dashboard.kitchen_monitors.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\KitchenMonitor::class],
                'active' => request()->routeIs('*kitchen_monitors*'),
            ],
        ])
    @endcomponent
@endif
