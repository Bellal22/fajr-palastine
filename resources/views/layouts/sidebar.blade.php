@if(!auth()->user()?->isGeneralSearchOnly())
<li class="nav-item">
    <a href="{{ route('dashboard.home') }}" class="nav-link{{ request()->routeIs('dashboard.home') ? ' active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>{{ trans('dashboard.home') }}</p>
    </a>
</li>

@if(auth()->check() && auth()->user()->hasPermission('manage.statistics'))
<li class="nav-item">
    <a href="{{ route('dashboard.statistics') }}" class="nav-link{{ request()->routeIs('dashboard.statistics') ? ' active' : '' }}">
        <i class="nav-icon fas fa-chart-pie"></i>
        <p>لوحة الإحصائيات</p>
    </a>
</li>
@endif

<li class="nav-item">
    <a href="{{ route('dashboard.notifications.index') }}" class="nav-link{{ request()->routeIs('dashboard.notifications.*') ? ' active' : '' }}">
        <i class="nav-icon far fa-bell"></i>
        <p>الإشعارات
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <span class="right badge badge-warning">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </p>
    </a>
</li>
@endif

@if(!auth()->user()?->isGeneralSearchOnly())
 @include('dashboard.accounts.sidebar')
 @include('dashboard.cities.partials.actions.sidebar')
 @include('dashboard.neighborhoods.partials.actions.sidebar')
 @include('dashboard.chooses.partials.actions.sidebar')
@endif
 @include('dashboard.people.partials.actions.sidebar')
@if(!auth()->user()?->isGeneralSearchOnly())
 @include('dashboard.complaints.partials.actions.sidebar')
 @include('dashboard.kitchen_cards.partials.actions.sidebar')
 @include('dashboard.kitchen_complaints.partials.actions.sidebar')
 @include('dashboard.area_responsibles.partials.actions.sidebar')
 @include('dashboard.blocks.partials.actions.sidebar')
 @include('dashboard.regions.partials.actions.sidebar')
 @include('dashboard.locations.partials.actions.sidebar')
 @include('dashboard.suppliers.partials.actions.sidebar')
 @include('dashboard.inbound_shipments.partials.actions.sidebar')
 @include('dashboard.items.partials.actions.sidebar')
 @include('dashboard.ready_packages.partials.actions.sidebar')
 @include('dashboard.internal_packages.partials.actions.sidebar')
 @include('dashboard.sub_warehouses.partials.actions.sidebar')
 @include('dashboard.coupon_types.partials.actions.sidebar')
 @include('dashboard.projects.partials.actions.sidebar')
 @php
    \App\Models\CustomProject::ensureTableExists();
    $sidebarUser = auth()->user();
    $canManageAllCustomProjects = auth()->check() && (
        $sidebarUser->isAdmin()
        || $sidebarUser->hasPermission('manage.admins')
        || $sidebarUser->hasPermission('manage.custom_projects')
        || $sidebarUser->hasPermission('manage.projects')
    );
    $sidebarCustomProjects = \App\Models\CustomProject::where('is_active', true)
        ->orderByRaw("CASE WHEN slug = 'rrm' THEN 0 ELSE 1 END")
        ->orderBy('id', 'asc')
        ->get()
        ->filter(function ($cp) use ($sidebarUser, $canManageAllCustomProjects) {
            return $canManageAllCustomProjects || ($sidebarUser && $cp->userCanAccess($sidebarUser));
        });
    $isCustomProjectsActive = request()->routeIs('dashboard.custom_projects.*')
        || request()->routeIs('dashboard.rrm.*')
        || (request()->routeIs('dashboard.projects.deliveries') && (request('type') === 'rrm' || request()->filled('custom_project_id')));
    $currentCustomProjectId = (int) request('custom_project_id', 0);
@endphp
 @if($canManageAllCustomProjects || $sidebarCustomProjects->isNotEmpty())
<li class="nav-item has-treeview {{ $isCustomProjectsActive ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isCustomProjectsActive ? 'active' : '' }}">
        <i class="nav-icon fas fa-layer-group"></i>
        <p>
            المشاريع المخصصة
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @if($canManageAllCustomProjects)
        <li class="nav-item">
            <a href="{{ route('dashboard.custom_projects.index') }}" class="nav-link {{ request()->routeIs('dashboard.custom_projects.index') ? 'active' : '' }}">
                <i class="fas fa-cogs nav-icon"></i>
                <p>إدارة المشاريع المخصصة</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.custom_projects.create') }}" class="nav-link {{ request()->routeIs('dashboard.custom_projects.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle nav-icon"></i>
                <p>إضافة مشروع مخصص جديد</p>
            </a>
        </li>
        @endif

        @foreach($sidebarCustomProjects as $cp)
            @php
                $isEditingThisCp = request()->routeIs('dashboard.custom_projects.edit') && optional(request()->route('custom_project'))->id == $cp->id;
                $isThisCpActive = ($currentCustomProjectId === (int) $cp->id)
                    || $isEditingThisCp
                    || ($cp->slug === 'rrm' && $currentCustomProjectId === 0 && (request()->routeIs('dashboard.rrm.*') || (request()->routeIs('dashboard.projects.deliveries') && request('type') === 'rrm')));
            @endphp
            <li class="nav-item has-treeview {{ $isThisCpActive ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ $isThisCpActive ? 'active' : '' }}">
                    <i class="nav-icon {{ $cp->icon ?: 'fas fa-first-aid' }}"></i>
                    <p>
                        {{ $cp->name }}
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="padding-right: 12px;">
                    <li class="nav-item">
                        <a href="{{ route('dashboard.rrm.index', ['custom_project_id' => $cp->id]) }}"
                           class="nav-link {{ (request()->routeIs('dashboard.rrm.index') || request()->routeIs('dashboard.rrm.show')) && $isThisCpActive ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>كشوفات وتوزيعات ({{ $cp->code_prefix }})</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.rrm.deliver', ['custom_project_id' => $cp->id]) }}"
                           class="nav-link {{ ((request()->routeIs('dashboard.rrm.deliver') || (request()->routeIs('dashboard.projects.deliveries') && (request('type') === 'rrm' || request()->filled('custom_project_id')))) && $isThisCpActive) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>تسليم كشف ({{ $cp->code_prefix }}) جديد</p>
                        </a>
                    </li>
                    @if($canManageAllCustomProjects)
                    <li class="nav-item">
                        <a href="{{ route('dashboard.custom_projects.edit', $cp->id) }}"
                           class="nav-link {{ $isEditingThisCp ? 'active' : '' }}">
                            <i class="fas fa-sliders-h nav-icon" style="font-size: 0.85rem;"></i>
                            <p>تخصيص طريقة التعامل</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
        @endforeach
    </ul>
</li>
@endif
 @include('dashboard.outbound_shipments.partials.actions.sidebar')
 @include('dashboard.game_winnings.partials.actions.sidebar')
 @include('dashboard.need_requests.partials.actions.sidebar')
@endif


@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('manage.admins') || auth()->user()->hasPermission('manage.audit')))
<li class="nav-item">
    <a href="{{ route('admin.audit.index') }}" class="nav-link{{ request()->routeIs('admin.audit.index') ? ' active' : '' }}">
        <i class="nav-icon fas fa-shield-alt"></i>
        <p>التدقيق الذكي</p>
    </a>
</li>
@endif

@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('manage.admins') || auth()->user()->hasPermission('manage.mobile_app') || auth()->user()->hasPermission('manage.outbound_shipments')))
<li class="nav-item {{ request()->routeIs('dashboard.mobile_app.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('dashboard.mobile_app.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-mobile-alt"></i>
        <p>
            تكليفات التوزيع
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('dashboard.mobile_app.index') }}" class="nav-link {{ request()->routeIs('dashboard.mobile_app.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>لوحة التحكم</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.mobile_app.users') }}" class="nav-link {{ request()->routeIs('dashboard.mobile_app.users*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>قائمة الموزعين</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.mobile_app.nominate') }}" class="nav-link {{ request()->routeIs('dashboard.mobile_app.nominate') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>تكليف الموزعين</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.mobile_app.report') }}" class="nav-link {{ request()->routeIs('dashboard.mobile_app.report') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>تقارير الإنجاز</p>
            </a>
        </li>
    </ul>
</li>
@endif

@if(!auth()->user()?->isGeneralSearchOnly())
@include('dashboard.settings.sidebar')
@endif
