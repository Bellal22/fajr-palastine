@can('beImpersonated', $distributor)
    <a href="{{ route('impersonate', $distributor) }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa-user-secret"></i>
    </a>
@endcan
