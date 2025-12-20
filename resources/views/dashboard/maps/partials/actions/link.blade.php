@if($map)
    @if(method_exists($map, 'trashed') && $map->trashed())
        <a href="{{ route('dashboard.maps.trashed.show', $map) }}" class="text-decoration-none text-ellipsis">
            {{ $map->name }}
        </a>
    @else
        <a href="{{ route('dashboard.maps.show', $map) }}" class="text-decoration-none text-ellipsis">
            {{ $map->name }}
        </a>
    @endif
@else
    ---
@endif