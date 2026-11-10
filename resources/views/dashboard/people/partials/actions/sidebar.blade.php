@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\Person::class])
    @slot('url', route('dashboard.people.index'))
    @slot('name', trans('people.plural'))
    @slot('active', request()->routeIs('*people*'))
    @slot('icon', 'fas fa-users')
    @slot('tree', [
        ...(!auth()->user()?->isGeneralSearchOnly() ? [
            [
                'name' => trans('people.actions.list'),
                'url' => route('dashboard.people.index'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.index')
                    || request()->routeIs('*people.show'),
            ],
            [
                'name' => trans('people.actions.create'),
                'url' => route('dashboard.people.create'),
                'can' => ['ability' => 'create', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.create'),
            ],
            [
                'name' => trans('people.actions.view'),
                'url' => route('dashboard.people.view'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.view'),
                'icon' => 'fa-list',
            ],
        ] : []),
        [
            'name' => trans('people.actions.general_search'),
            'url' => route('dashboard.people.general_search'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class], // This will be allowed because we will modify PersonPolicy viewAny, wait no, let's just make it visible
            'active' => request()->routeIs('*people.general_search'),
            'icon' => 'fa-id-card',
        ],
        ...(!auth()->user()?->isGeneralSearchOnly() ? [
            [
                'name' => 'إصدار إفادة كوبونات',
                'url' => route('dashboard.people.bulk_history'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.bulk_history'),
                'icon' => 'fa-file-excel',
            ],
            [
                'name' => 'فحص أرقام الهويات',
                'url' => route('dashboard.people.verify_ids'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.verify_ids'),
                'icon' => 'fa-check-double',
            ],
            [
                'name' => trans('people.actions.search'),
                'url' => route('dashboard.people.search'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.search'),
                'icon' => 'fa-search',
            ],
        ] : []),
        ...((auth()->user()?->isAdmin() || auth()->user()?->hasPermission('manage.people')) ? [
            [
                'name' => 'البيانات المحدثة',
                'url' => route('dashboard.people.updated'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.updated'),
                'icon' => 'fa-sync-alt',
            ],
            [
                'name' => 'الأشخاص غير المزامنين',
                'url' => route('dashboard.people.unsynced'),
                'can' => ['ability' => 'viewAny', 'model' => \App\Models\Person::class],
                'active' => request()->routeIs('*people.unsynced'),
                'icon' => 'fa-clock',
            ],
        ] : []),
        ...((auth()->user()?->isAdmin() || auth()->user()?->hasPermission('manage.incomplete_people') || auth()->user()?->hasPermission('manage.people')) ? [
            [
                'name' => 'البيانات غير المكتملة',
                'url' => route('dashboard.incomplete_people.index'),
                'active' => request()->routeIs('*incomplete_people.index'),
                'icon' => 'fa-trash-alt',
            ],
        ] : []),
        ...((auth()->user()?->isAdmin() || auth()->user()?->hasPermission('manage.employees') || auth()->user()?->hasPermission('manage.people')) ? [
            [
                'name' => 'إدارة الموظفين',
                'url' => route('dashboard.employees.index'),
                'active' => request()->routeIs('*employees*'),
                'icon' => 'fa-user-tie',
            ],
        ] : []),
    ])
@endcomponent
