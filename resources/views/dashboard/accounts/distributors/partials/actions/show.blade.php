@can('view', $distributor)
    <a href="{{ route('dashboard.distributors.show', $distributor) }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-eye"></i>
    </a>
@endcan
