@can('create', \App\Models\InternalPackage::class)
    <a href="{{ route('dashboard.internal_packages.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('internal_packages.actions.create')
    </a>
@endcan
