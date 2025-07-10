@can('viewAnyTrash', \App\Models\Block::class)
    <a href="{{ route('dashboard.blocks.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('blocks.trashed')
    </a>
@endcan
