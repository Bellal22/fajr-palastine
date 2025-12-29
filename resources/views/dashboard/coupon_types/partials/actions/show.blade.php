@if(method_exists($coupon_type, 'trashed') && $coupon_type->trashed())
    @can('view', $coupon_type)
        <a href="{{ route('dashboard.coupon_types.trashed.show', $coupon_type) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $coupon_type)
        <a href="{{ route('dashboard.coupon_types.show', $coupon_type) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif