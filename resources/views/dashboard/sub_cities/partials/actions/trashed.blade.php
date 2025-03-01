@can('viewAnyTrash', \App\Models\SubCity::class)
    <a href="{{ route('dashboard.sub_cities.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('sub_cities.trashed')
    </a>
@endcan
