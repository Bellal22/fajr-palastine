@can('view', $person)
    <a href="{{ route('dashboard.people.family.list', $person) }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa fa-fw fa-address-book"></i>
    </a>
@endcan
