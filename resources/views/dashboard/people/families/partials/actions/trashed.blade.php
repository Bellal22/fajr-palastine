@can('viewAnyTrash', \App\Models\Person::class)
    <a href="{{ route('dashboard.people.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('people.trashed')
    </a>
@endcan
