@can('viewAnyTrash', \App\Models\AreaResponsible::class)
    <a href="{{ route('dashboard.area_responsibles.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('area_responsibles.trashed')
    </a>
@endcan
