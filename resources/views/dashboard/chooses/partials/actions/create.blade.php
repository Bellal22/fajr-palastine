@can('create', \App\Models\Choose::class)
    <a href="{{ route('dashboard.chooses.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('chooses.actions.create')
    </a>
@endcan
