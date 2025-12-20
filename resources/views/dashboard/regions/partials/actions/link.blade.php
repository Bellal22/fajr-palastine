@if($region)
    @if(method_exists($region, 'trashed') && $region->trashed())
        <a href="{{ route('dashboard.regions.trashed.show', $region) }}" class="text-decoration-none text-ellipsis">
            {{ $region->name }}
        </a>
    @else
        <a href="{{ route('dashboard.regions.show', $region) }}" class="text-decoration-none text-ellipsis">
            {{ $region->name }}
        </a>
    @endif
@else
    ---
@endif