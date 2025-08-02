@if (auth()->user()?->isAdmin())
    @can('viewAnyTrash', \App\Models\Complaint::class)
        <a href="{{ route('dashboard.complaint.trashed') }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa fa-fw fa-trash"></i>
            @lang('complaint.trashed')
        </a>
    @endcan
@endif
