@can('create', \App\Models\AreaResponsible::class)
    <a href="{{ route('dashboard.area_responsibles.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('area_responsibles.actions.create')
    </a>
@endcan
