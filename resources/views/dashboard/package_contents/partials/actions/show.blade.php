@if(method_exists($package_content, 'trashed') && $package_content->trashed())
    @can('view', $package_content)
        <a href="{{ route('dashboard.package_contents.trashed.show', $package_content) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $package_content)
        <a href="{{ route('dashboard.package_contents.show', $package_content) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif