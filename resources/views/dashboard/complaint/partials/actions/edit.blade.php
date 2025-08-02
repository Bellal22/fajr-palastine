@if (auth()->user()?->isAdmin())
    @can('update', $complaint)
        <a href="{{ route('dashboard.complaint.edit', $complaint) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa fa-fw fa-edit"></i>
        </a>
    @endcan
@endif
