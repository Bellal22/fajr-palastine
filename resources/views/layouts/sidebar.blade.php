@component('dashboard::components.sidebarItem')
    @slot('url', route('dashboard.home'))
    @slot('name', trans('dashboard.home'))
    @slot('icon', 'fas fa-tachometer-alt')
    @slot('active', request()->routeIs('dashboard.home'))
@endcomponent

 @include('dashboard.accounts.sidebar')
{{-- @include('dashboard.families.partials.actions.sidebar') --}}
@include('dashboard.cities.partials.actions.sidebar')
@include('dashboard.sub_cities.partials.actions.sidebar')
@include('dashboard.neighborhoods.partials.actions.sidebar')
@include('dashboard.people.partials.actions.sidebar')
{{-- @include('dashboard.people.partials.actions.sidebar') --}}
@include('dashboard.complaints.partials.actions.sidebar')
@include('dashboard.area_responsibles.partials.actions.sidebar')
@include('dashboard.blocks.partials.actions.sidebar')
@include('dashboard.suppliers.partials.actions.sidebar')
@include('dashboard.items.partials.actions.sidebar')
@include('dashboard.inbound_shipments.partials.actions.sidebar')
@include('dashboard.regions.partials.actions.sidebar')
@include('dashboard.locations.partials.actions.sidebar')
@include('dashboard.maps.partials.actions.sidebar')
{{-- The sidebar of generated crud will set here: Don't remove this line --}}
@include('dashboard.feedback.partials.actions.sidebar')
@include('dashboard.settings.sidebar')
