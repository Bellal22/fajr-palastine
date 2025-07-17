@can('create', \App\Models\Block::class)
    <a href="{{ route('dashboard.blocks.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('blocks.actions.create')
    </a>
@endcan
