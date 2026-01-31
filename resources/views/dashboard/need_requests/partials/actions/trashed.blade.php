@can('viewAnyTrash', \App\Models\NeedRequest::class)
    <a href="{{ route('dashboard.need_requests.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('need_requests.trashed')
    </a>
@endcan
