<x-layout :title="'متابعي المطبخ المحذوفين'" :breadcrumbs="['dashboard.kitchen_monitors.trashed']">
    @include('dashboard.accounts.kitchen_monitors.partials.filter')

    @component('dashboard::components.table-box')

        @slot('title')
            قائمة متابعي المطبخ المحذوفين ({{ count_formatted($kitchenMonitors->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <x-check-all-force-delete
                        type="{{ \App\Models\KitchenMonitor::class }}"
                        :resource="'متابعي المطبخ'"></x-check-all-force-delete>
                <x-check-all-restore
                        type="{{ \App\Models\KitchenMonitor::class }}"
                        :resource="'متابعي المطبخ'"></x-check-all-restore>
            </th>
        </tr>
        <tr>
            <th>
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
                <td>
                    <x-check-all-item :model="$kitchenMonitor"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.kitchen_monitors.trashed.show', $kitchenMonitor) }}"
                       class="text-decoration-none text-ellipsis">
                        <img src="{{ $kitchenMonitor->getAvatar() }}"
                             alt="{{ $kitchenMonitor->name }}"
                             class="img-circle img-size-32 mr-2">
                        {{ $kitchenMonitor->name }}
                    </a>
                </td>

                <td class="d-none d-md-table-cell">
                    {{ $kitchenMonitor->email }}
                </td>
                <td>
                    @if($kitchenMonitor->phone)
                        {{ $kitchenMonitor->phone }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $kitchenMonitor->created_at->format('Y-m-d') }}</td>

                <td style="width: 160px">
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.show')
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.restore')
                    @include('dashboard.accounts.kitchen_monitors.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">لا يوجد حسابات محذوفة حالياً.</td>
            </tr>
        @endforelse

        @if($kitchenMonitors->hasPages())
            @slot('footer')
                {{ $kitchenMonitors->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
