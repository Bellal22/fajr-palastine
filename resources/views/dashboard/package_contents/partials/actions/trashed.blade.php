@can('viewAnyTrash', \App\Models\PackageContent::class)
    <a href="{{ route('dashboard.package_contents.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('package_contents.trashed')
    </a>
@endcan
