@if($sub_warehouse)
    @if(method_exists($sub_warehouse, 'trashed') && $sub_warehouse->trashed())
        <a href="{{ route('dashboard.sub_warehouses.trashed.show', $sub_warehouse) }}" class="text-decoration-none text-ellipsis">
            {{ $sub_warehouse->name }}
        </a>
    @else
        <a href="{{ route('dashboard.sub_warehouses.show', $sub_warehouse) }}" class="text-decoration-none text-ellipsis">
            {{ $sub_warehouse->name }}
        </a>
    @endif
@else
    ---
@endif