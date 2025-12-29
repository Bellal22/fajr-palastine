@can('viewAnyTrash', \App\Models\ReadyPackage::class)
    <a href="{{ route('dashboard.ready_packages.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('ready_packages.trashed')
    </a>
@endcan
