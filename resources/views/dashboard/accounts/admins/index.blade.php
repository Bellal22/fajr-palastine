<x-layout :title="trans('admins.plural')" :breadcrumbs="['dashboard.admins.index']">
    @include('dashboard.accounts.admins.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('admins.actions.list') ({{ count_formatted($admins->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\Admin::class }}"
                        :resource="trans('admins.plural')">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.accounts.admins.partials.actions.create')
                        @include('dashboard.accounts.admins.partials.actions.trashed')
                    </div>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>@lang('admins.attributes.name')</th>
            <th class="d-none d-md-table-cell">@lang('admins.attributes.email')</th>
            <th>@lang('admins.attributes.phone')</th>
            <th>@lang('admins.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($admins as $admin)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$admin"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.admins.show', $admin) }}"
                       class="text-decoration-none text-ellipsis">
                        <span class="index-flag">
                            @include('dashboard.accounts.admins.partials.flags.svg')
                        </span>
                        <img src="{{ $admin->getAvatar() }}"
                             alt="{{ $admin->name }}"
                             class="img-circle img-size-32 mr-2">
                        <strong class="text-dark">{{ $admin->name }}</strong>
                    </a>
                </td>
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-envelope text-primary"></i>
                    <span class="text-muted">{{ $admin->email }}</span>
                </td>
                <td>
                    @if($admin->phone)
                        <i class="fas fa-phone text-success"></i>
                        <span class="text-muted">{{ $admin->phone }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <i class="fas fa-calendar-alt text-info"></i>
                    <span class="text-muted">{{ $admin->created_at->format('Y-m-d') }}</span>
                </td>
                <td style="width: 160px">
                    @include('dashboard.accounts.admins.partials.actions.show')
                    @include('dashboard.accounts.admins.partials.actions.edit')
                    @include('dashboard.accounts.admins.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('admins.empty')</td>
            </tr>
        @endforelse

        @if($admins->hasPages())
            @slot('footer')
                {{ $admins->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
