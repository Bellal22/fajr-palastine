@if(method_exists($area_responsible, 'trashed') && $area_responsible->trashed())
    @can('view', $area_responsible)
        <a href="{{ route('dashboard.area_responsibles.trashed.show', $area_responsible) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $area_responsible)
        <a href="{{ route('dashboard.area_responsibles.show', $area_responsible) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif