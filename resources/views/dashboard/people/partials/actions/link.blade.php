@if($person)
    @if(method_exists($person, 'trashed') && $person->trashed())
        <a href="{{ route('dashboard.people.trashed.show', $person) }}" class="text-decoration-none text-ellipsis">
            {{ $person->name }}
        </a>
    @else
        <a href="{{ route('dashboard.people.show', $person) }}" class="text-decoration-none text-ellipsis">
            {{ $person->name }}
        </a>
    @endif
@else
    ---
@endif