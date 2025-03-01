@if($neighborhood)
    @if(method_exists($neighborhood, 'trashed') && $neighborhood->trashed())
        <a href="{{ route('dashboard.neighborhoods.trashed.show', $neighborhood) }}" class="text-decoration-none text-ellipsis">
            {{ $neighborhood->name }}
        </a>
    @else
        <a href="{{ route('dashboard.neighborhoods.show', $neighborhood) }}" class="text-decoration-none text-ellipsis">
            {{ $neighborhood->name }}
        </a>
    @endif
@else
    ---
@endif