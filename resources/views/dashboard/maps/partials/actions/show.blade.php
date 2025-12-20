@if(method_exists($map, 'trashed') && $map->trashed())
    @can('view', $map)
        <a href="{{ route('dashboard.maps.trashed.show', $map) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $map)
        <a href="{{ route('dashboard.maps.show', $map) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif