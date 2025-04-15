@if($supplier)
    @if(method_exists($supplier, 'trashed') && $supplier->trashed())
        <a href="{{ route('dashboard.suppliers.trashed.show', $supplier) }}" class="text-decoration-none text-ellipsis">
            {{ $supplier->name }}
        </a>
    @else
        <a href="{{ route('dashboard.suppliers.show', $supplier) }}" class="text-decoration-none text-ellipsis">
            {{ $supplier->name }}
        </a>
    @endif
@else
    ---
@endif