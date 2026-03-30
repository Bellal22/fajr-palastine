@can('viewAnyTrash', \App\Models\Distributor::class)
    <a href="{{ route('dashboard.distributors.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa-trash"></i>
        @lang('distributors.actions.trashed')
    </a>
@endcan
