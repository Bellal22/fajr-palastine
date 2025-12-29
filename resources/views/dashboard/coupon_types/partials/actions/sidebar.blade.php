@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\CouponType::class])
    @slot('url', route('dashboard.coupon_types.index'))
    @slot('name', trans('coupon_types.plural'))
    @slot('active', request()->routeIs('*coupon_types*'))
    @slot('icon', 'fas fa-tags')
    @slot('tree', [
        [
            'name' => trans('coupon_types.actions.list'),
            'url' => route('dashboard.coupon_types.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\CouponType::class],
            'active' => request()->routeIs('*coupon_types.index')
            || request()->routeIs('*coupon_types.show'),
        ],
        [
            'name' => trans('coupon_types.actions.create'),
            'url' => route('dashboard.coupon_types.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\CouponType::class],
            'active' => request()->routeIs('*coupon_types.create'),
        ],
    ])
@endcomponent
