@canBeImpersonated($admin)
<a href="{{ route('impersonate', $admin) }}"
   title="@lang('users.impersonate.go')"
   class="btn btn-outline-success btn-sm">
    <i class="nav-icon fas fa-tachometer-alt"></i>
</a>
@endCanBeImpersonated
