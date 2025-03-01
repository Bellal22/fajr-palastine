@if(method_exists($neighborhood, 'trashed') && $neighborhood->trashed())
    @can('view', $neighborhood)
        <a href="{{ route('dashboard.neighborhoods.trashed.show', $neighborhood) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $neighborhood)
        <a href="{{ route('dashboard.neighborhoods.show', $neighborhood) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif