@can('create', \App\Models\Person::class)
    <a href="{{ route('dashboard.people.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('people.actions.create')
    </a>
@endcan
