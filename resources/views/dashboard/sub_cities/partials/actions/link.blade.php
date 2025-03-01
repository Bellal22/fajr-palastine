@if($sub_city)
    @if(method_exists($sub_city, 'trashed') && $sub_city->trashed())
        <a href="{{ route('dashboard.sub_cities.trashed.show', $sub_city) }}" class="text-decoration-none text-ellipsis">
            {{ $sub_city->name }}
        </a>
    @else
        <a href="{{ route('dashboard.sub_cities.show', $sub_city) }}" class="text-decoration-none text-ellipsis">
            {{ $sub_city->name }}
        </a>
    @endif
@else
    ---
@endif