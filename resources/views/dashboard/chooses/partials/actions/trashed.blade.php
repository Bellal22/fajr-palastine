@can('viewAnyTrash', \App\Models\Choose::class)
    <a href="{{ route('dashboard.chooses.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('chooses.trashed')
    </a>
@endcan
