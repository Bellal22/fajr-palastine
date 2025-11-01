@if($item)
    @if(method_exists($item, 'trashed') && $item->trashed())
        <a href="{{ route('dashboard.items.trashed.show', $item) }}" class="text-decoration-none text-ellipsis">
            {{ $item->name }}
        </a>
    @else
        <a href="{{ route('dashboard.items.show', $item) }}" class="text-decoration-none text-ellipsis">
            {{ $item->name }}
        </a>
    @endif
@else
    ---
@endif