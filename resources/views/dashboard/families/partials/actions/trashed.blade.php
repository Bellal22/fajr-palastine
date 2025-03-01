@can('viewAnyTrash', \App\Models\Family::class)
    <a href="{{ route('dashboard.families.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('families.trashed')
    </a>
@endcan
