@if($researcher)
    @if(method_exists($researcher, 'trashed') && $researcher->trashed())
        <a href="{{ route('dashboard.researchers.trashed.show', $researcher) }}" class="text-decoration-none text-ellipsis">
            {{ $researcher->name }}
        </a>
    @else
        <a href="{{ route('dashboard.researchers.show', $researcher) }}" class="text-decoration-none text-ellipsis">
            {{ $researcher->name }}
        </a>
    @endif
@else
    ---
@endif
