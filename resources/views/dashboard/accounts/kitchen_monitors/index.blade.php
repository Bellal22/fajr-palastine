<x-layout :title="'متابعي المطبخ'" :breadcrumbs="['dashboard.kitchen_monitors.index']">
    @include('dashboard.accounts.kitchen_monitors.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            قائمة متابعي المطبخ ({{ count_formatted($kitchenMonitors->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\KitchenMonitor::class }}"
                        :resource="'متابعي المطبخ'">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.accounts.kitchen_monitors.partials.actions.create')
                        @include('dashboard.accounts.kitchen_monitors.partials.actions.trashed')
                    </div>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>الاسم</th>
            <th class="d-none d-md-table-cell">البريد الإلكتروني</th>
            <th>الهاتف</th>
            <th>تاريخ الإضافة</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($kitchenMonitors as $kitchenMonitor)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$kitchenMonitor"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.kitchen_monitors.show', $kitchenMonitor) }}"
                       class="text-decoration-none text-ellipsis">
                        <img src="{{ $kitchenMonitor->getAvatar() }}"
                             alt="{{ $kitchenMonitor->name }}"
                             class="img-circle img-size-32 mr-2">
                        <strong class="text-dark">{{ $kitchenMonitor->name }}</strong>
                    </a>
                </td>
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-envelope text-primary"></i>
                    <span class="text-muted">{{ $kitchenMonitor->email }}</span>
                </td>
                <td>
                    @if($kitchenMonitor->phone)
                        <i class="fas fa-phone text-success"></i>
                        <span class="text-muted">{{ $kitchenMonitor->phone }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <i class="fas fa-calendar-alt text-info"></i>
                    <span class="text-muted">{{ $kitchenMonitor->created_at->format('Y-m-d') }}</span>
                </td>
                <td style="width: 160px">
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.impersonate')
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.edit')
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">لا يوجد حسابات حالياً.</td>
            </tr>
        @endforelse

        @if($kitchenMonitors->hasPages())
            @slot('footer')
                {{ $kitchenMonitors->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
