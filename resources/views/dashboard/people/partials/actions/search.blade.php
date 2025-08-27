@if (!request()->routeIs('dashboard.people.search'))
    <a href="{{ route('dashboard.people.search') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-fw fas fa-search"></i>
        @lang('people.actions.search')
    </a>
@endif
