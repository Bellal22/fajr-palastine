@can('create', \App\Models\CouponType::class)
    <a href="{{ route('dashboard.coupon_types.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('coupon_types.actions.create')
    </a>
@endcan
