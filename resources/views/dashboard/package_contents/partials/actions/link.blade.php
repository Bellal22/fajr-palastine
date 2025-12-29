@if($package_content)
    @if(method_exists($package_content, 'trashed') && $package_content->trashed())
        <a href="{{ route('dashboard.package_contents.trashed.show', $package_content) }}" class="text-decoration-none text-ellipsis">
            {{ $package_content->name }}
        </a>
    @else
        <a href="{{ route('dashboard.package_contents.show', $package_content) }}" class="text-decoration-none text-ellipsis">
            {{ $package_content->name }}
        </a>
    @endif
@else
    ---
@endif