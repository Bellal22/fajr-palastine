@can('viewAnyTrash', \App\Models\CouponType::class)
    <a href="{{ route('dashboard.coupon_types.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('coupon_types.trashed')
    </a>
@endcan
