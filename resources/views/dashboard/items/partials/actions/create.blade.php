@can('create', \App\Models\Item::class)
    <a href="{{ route('dashboard.items.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('items.actions.create')
    </a>
@endcan
