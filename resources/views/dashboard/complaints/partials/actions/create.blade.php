@can('create', \App\Models\Complaint::class)
    <a href="{{ route('dashboard.complaints.create') }}" class="btn btn-outline-success btn-sm font-weight-bold bg-white shadow-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('complaints.actions.create')
    </a>
@endcan
