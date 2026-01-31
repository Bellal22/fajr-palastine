@can('create', \App\Models\NeedRequest::class)
    <a href="{{ route('dashboard.need_requests.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('need_requests.actions.create')
    </a>
@endcan
