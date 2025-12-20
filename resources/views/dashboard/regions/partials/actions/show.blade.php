@if(method_exists($region, 'trashed') && $region->trashed())
    @can('view', $region)
        <a href="{{ route('dashboard.regions.trashed.show', $region) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $region)
        <a href="{{ route('dashboard.regions.show', $region) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif