@can('viewAnyTrash', \App\Models\InternalPackage::class)
    <a href="{{ route('dashboard.internal_packages.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('internal_packages.trashed')
    </a>
@endcan
