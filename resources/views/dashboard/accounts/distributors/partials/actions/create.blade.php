@can('create', \App\Models\Distributor::class)
    <a href="{{ route('dashboard.distributors.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa-plus"></i>
        @lang('distributors.actions.create')
    </a>
@endcan
