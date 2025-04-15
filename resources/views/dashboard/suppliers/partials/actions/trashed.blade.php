@can('viewAnyTrash', \App\Models\Supplier::class)
    <a href="{{ route('dashboard.suppliers.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('suppliers.trashed')
    </a>
@endcan
