@if($location)
    @if(method_exists($location, 'trashed') && $location->trashed())
        <a href="{{ route('dashboard.locations.trashed.show', $location) }}" class="text-decoration-none text-ellipsis">
            {{ $location->name }}
        </a>
    @else
        <a href="{{ route('dashboard.locations.show', $location) }}" class="text-decoration-none text-ellipsis">
            {{ $location->name }}
        </a>
    @endif
@else
    ---
@endif