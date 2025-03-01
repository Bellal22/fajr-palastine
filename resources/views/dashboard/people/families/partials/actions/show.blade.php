@if(method_exists($person, 'trashed') && $person->trashed())
    @can('view', $person)
        <a href="{{ route('dashboard.people.trashed.show', $person) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $person)
        <a href="{{ route('dashboard.people.show', $person) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif