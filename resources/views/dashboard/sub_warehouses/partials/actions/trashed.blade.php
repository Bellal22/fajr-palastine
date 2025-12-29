@can('viewAnyTrash', \App\Models\SubWarehouse::class)
    <a href="{{ route('dashboard.sub_warehouses.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('sub_warehouses.trashed')
    </a>
@endcan
