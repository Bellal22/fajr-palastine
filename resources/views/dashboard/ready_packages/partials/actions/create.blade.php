@can('create', \App\Models\ReadyPackage::class)
    <a href="{{ route('dashboard.ready_packages.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('ready_packages.actions.create')
    </a>
@endcan
