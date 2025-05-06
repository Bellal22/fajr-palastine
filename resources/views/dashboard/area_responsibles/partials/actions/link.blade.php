@if($area_responsible)
    @if(method_exists($area_responsible, 'trashed') && $area_responsible->trashed())
        <a href="{{ route('dashboard.area_responsibles.trashed.show', $area_responsible) }}" class="text-decoration-none text-ellipsis">
            {{ $area_responsible->name }}
        </a>
    @else
        <a href="{{ route('dashboard.area_responsibles.show', $area_responsible) }}" class="text-decoration-none text-ellipsis">
            {{ $area_responsible->name }}
        </a>
    @endif
@else
    ---
@endif