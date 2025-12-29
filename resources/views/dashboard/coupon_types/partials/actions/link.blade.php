@if($coupon_type)
    @if(method_exists($coupon_type, 'trashed') && $coupon_type->trashed())
        <a href="{{ route('dashboard.coupon_types.trashed.show', $coupon_type) }}" class="text-decoration-none text-ellipsis">
            {{ $coupon_type->name }}
        </a>
    @else
        <a href="{{ route('dashboard.coupon_types.show', $coupon_type) }}" class="text-decoration-none text-ellipsis">
            {{ $coupon_type->name }}
        </a>
    @endif
@else
    ---
@endif