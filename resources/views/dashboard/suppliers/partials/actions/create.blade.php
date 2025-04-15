@can('create', \App\Models\Supplier::class)
    <a href="{{ route('dashboard.suppliers.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('suppliers.actions.create')
    </a>
@endcan
