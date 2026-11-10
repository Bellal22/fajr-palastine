@if(method_exists($researcher, 'trashed') && $researcher->trashed())
    @can('view', $researcher)
        <a href="{{ route('dashboard.researchers.trashed.show', $researcher) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $researcher)
        <a href="{{ route('dashboard.researchers.show', $researcher) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
