@component('dashboard::components.sidebarItem')
    @slot('url', route('dashboard.home'))
    @slot('name', trans('dashboard.home'))
    @slot('icon', 'fas fa-tachometer-alt')
    @slot('active', request()->routeIs('dashboard.home'))
@endcomponent

@component('dashboard::components.sidebarItem')
    @slot('url', route('dashboard.notifications.index'))
    @slot('name', 'الإشعارات')
    @slot('icon', 'far fa-bell')
    @slot('active', request()->routeIs('dashboard.notifications.*'))
    @if(auth()->user()->unreadNotifications->count() > 0)
        @slot('badge', auth()->user()->unreadNotifications->count())
        @slot('badge_class', 'badge-warning')
    @endif
@endcomponent

 @include('dashboard.accounts.sidebar')
{{-- @include('dashboard.families.partials.actions.sidebar') --}}
@include('dashboard.cities.partials.actions.sidebar')
@include('dashboard.neighborhoods.partials.actions.sidebar')
@include('dashboard.chooses.partials.actions.sidebar')
@include('dashboard.people.partials.actions.sidebar')
{{-- @include('dashboard.people.partials.actions.sidebar') --}}
@include('dashboard.complaints.partials.actions.sidebar')
@include('dashboard.area_responsibles.partials.actions.sidebar')
@include('dashboard.blocks.partials.actions.sidebar')
@include('dashboard.regions.partials.actions.sidebar')
@include('dashboard.locations.partials.actions.sidebar')
{{-- @include('dashboard.maps.partials.actions.sidebar') --}}
@include('dashboard.suppliers.partials.actions.sidebar')
@include('dashboard.inbound_shipments.partials.actions.sidebar')
@include('dashboard.items.partials.actions.sidebar')
@include('dashboard.ready_packages.partials.actions.sidebar')
@include('dashboard.internal_packages.partials.actions.sidebar')
{{-- @include('dashboard.package_contents.partials.actions.sidebar') --}}
@include('dashboard.sub_warehouses.partials.actions.sidebar')
@include('dashboard.coupon_types.partials.actions.sidebar')
@include('dashboard.projects.partials.actions.sidebar')
@include('dashboard.outbound_shipments.partials.actions.sidebar')
{{-- @include('dashboard.outbound_shipment_items.partials.actions.sidebar') --}}


@include('dashboard.game_winnings.partials.actions.sidebar')
@include('dashboard.need_requests.partials.actions.sidebar')

{{-- للأدمن فقط --}}
@if(auth()->check() && auth()->user()->isAdmin())
@component('dashboard::components.sidebarItem')
    @slot('url', route('dashboard.mobile_app.index'))
    @slot('name', 'تكليفات التوزيع')
    @slot('icon', 'fas fa-mobile-alt')
    @slot('active', request()->routeIs('dashboard.mobile_app.*'))
    @slot('tree', [
        [
            'name' => 'لوحة التحكم',
            'url' => route('dashboard.mobile_app.index'),
            'active' => request()->routeIs('dashboard.mobile_app.index'),
        ],
        [
            'name' => 'قائمة الموزعين',
            'url' => route('dashboard.mobile_app.users'),
            'active' => request()->routeIs('dashboard.mobile_app.users*'),
        ],
        [
            'name' => 'تكليف الموزعين',
            'url' => route('dashboard.mobile_app.nominate'),
            'active' => request()->routeIs('dashboard.mobile_app.nominate'),
        ],
        [
            'name' => 'تقارير الإنجاز',
            'url' => route('dashboard.mobile_app.report'),
            'active' => request()->routeIs('dashboard.mobile_app.report'),
        ],
    ])
@endcomponent
@endif

@include('dashboard.settings.sidebar')
