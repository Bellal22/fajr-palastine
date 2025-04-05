@if(method_exists($complaint, 'trashed') && $complaint->trashed())
    @can('view', $complaint)
        <a href="{{ route('dashboard.complaints.trashed.show', $complaint) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $complaint)
        <a href="{{ route('dashboard.complaints.show', $complaint) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif