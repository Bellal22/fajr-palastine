@if(method_exists($choose, 'trashed') && $choose->trashed())
    @can('view', $choose)
        <a href="{{ route('dashboard.chooses.trashed.show', $choose) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $choose)
        <a href="{{ route('dashboard.chooses.show', $choose) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif