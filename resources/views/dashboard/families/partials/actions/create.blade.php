@can('create', \App\Models\Family::class)
    <a href="{{ route('dashboard.families.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('families.actions.create')
    </a>
@endcan
