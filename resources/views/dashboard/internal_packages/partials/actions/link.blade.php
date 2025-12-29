@if($internal_package)
    @if(method_exists($internal_package, 'trashed') && $internal_package->trashed())
        <a href="{{ route('dashboard.internal_packages.trashed.show', $internal_package) }}" class="text-decoration-none text-ellipsis">
            {{ $internal_package->name }}
        </a>
    @else
        <a href="{{ route('dashboard.internal_packages.show', $internal_package) }}" class="text-decoration-none text-ellipsis">
            {{ $internal_package->name }}
        </a>
    @endif
@else
    ---
@endif