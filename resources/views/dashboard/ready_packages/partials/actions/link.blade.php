@if($ready_package)
    @if(method_exists($ready_package, 'trashed') && $ready_package->trashed())
        <a href="{{ route('dashboard.ready_packages.trashed.show', $ready_package) }}" class="text-decoration-none text-ellipsis">
            {{ $ready_package->name }}
        </a>
    @else
        <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}" class="text-decoration-none text-ellipsis">
            {{ $ready_package->name }}
        </a>
    @endif
@else
    ---
@endif