@can('create', \App\Models\PackageContent::class)
    <a href="{{ route('dashboard.package_contents.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('package_contents.actions.create')
    </a>
@endcan
