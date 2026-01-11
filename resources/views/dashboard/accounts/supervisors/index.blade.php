<x-layout :title="trans('supervisors.plural')" :breadcrumbs="['dashboard.supervisors.index']">
    @include('dashboard.accounts.supervisors.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('supervisors.actions.list') ({{ count_formatted($supervisors->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\Supervisor::class }}"
                        :resource="trans('supervisors.plural')">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.accounts.supervisors.partials.actions.create')
                        @include('dashboard.accounts.supervisors.partials.actions.trashed')
                    </div>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>@lang('supervisors.attributes.name')</th>
            <th class="d-none d-md-table-cell">@lang('supervisors.attributes.email')</th>
            <th>@lang('supervisors.attributes.phone')</th>
            <th>@lang('supervisors.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($supervisors as $supervisor)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$supervisor"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.supervisors.show', $supervisor) }}"
                       class="text-decoration-none text-ellipsis">
                        <span class="index-flag">
                            @include('dashboard.accounts.supervisors.partials.flags.svg')
                        </span>
                        <img src="{{ $supervisor->getAvatar() }}"
                             alt="{{ $supervisor->name }}"
                             class="img-circle img-size-32 mr-2">
                        <strong class="text-dark">{{ $supervisor->name }}</strong>
                    </a>
                </td>
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-envelope text-primary"></i>
                    <span class="text-muted">{{ $supervisor->email }}</span>
                </td>
                <td>
                    @if($supervisor->phone)
                        <i class="fas fa-phone text-success"></i>
                        <span class="text-muted">{{ $supervisor->phone }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <i class="fas fa-calendar-alt text-info"></i>
                    <span class="text-muted">{{ $supervisor->created_at->format('Y-m-d') }}</span>
                </td>
                <td style="width: 160px">
                    @include('dashboard.accounts.supervisors.partials.actions.impersonate')
                    @include('dashboard.accounts.supervisors.partials.actions.edit')
                    @include('dashboard.accounts.supervisors.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('supervisors.empty')</td>
            </tr>
        @endforelse

        @if($supervisors->hasPages())
            @slot('footer')
                {{ $supervisors->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
