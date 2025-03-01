@if(method_exists($family, 'trashed') && $family->trashed())
    @can('view', $family)
        <a href="{{ route('dashboard.families.trashed.show', $family) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $family)
        <a href="{{ route('dashboard.families.show', $family) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif