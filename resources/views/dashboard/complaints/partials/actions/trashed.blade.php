@can('viewAnyTrash', \App\Models\Complaint::class)
    <a href="{{ route('dashboard.complaints.trashed') }}" class="btn btn-outline-danger btn-sm font-weight-bold bg-white shadow-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('complaints.trashed')
    </a>
@endcan
