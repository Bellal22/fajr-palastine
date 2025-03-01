@if($family)
    @if(method_exists($family, 'trashed') && $family->trashed())
        <a href="{{ route('dashboard.families.trashed.show', $family) }}" class="text-decoration-none text-ellipsis">
            {{ $family->name }}
        </a>
    @else
        <a href="{{ route('dashboard.families.show', $family) }}" class="text-decoration-none text-ellipsis">
            {{ $family->name }}
        </a>
    @endif
@else
    ---
@endif