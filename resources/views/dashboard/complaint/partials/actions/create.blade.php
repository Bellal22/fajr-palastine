
@if (auth()->user()?->isAdmin())@can('create', \App\Models\Complaint::class)
        <a href="{{ route('dashboard.complaint.create') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa fa-fw fa-plus"></i>
            @lang('complaint.actions.create')
        </a>
    @endcan
@endif
