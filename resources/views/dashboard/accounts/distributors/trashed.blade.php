<x-layout :title="trans('distributors.trashed')" :breadcrumbs="['dashboard.distributors.trashed']">
    @component('dashboard::components.table-box')
        @slot('title')
            @lang('distributors.trashed') ({{ count_formatted($distributors->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\Distributor::class }}"
                        :resource="trans('distributors.plural')">
                    </x-check-all-delete>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>@lang('distributors.attributes.name')</th>
            <th class="d-none d-md-table-cell">@lang('distributors.attributes.email')</th>
            <th>@lang('distributors.attributes.phone')</th>
            <th>@lang('distributors.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($distributors as $distributor)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$distributor"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.distributors.show', $distributor) }}"
                       class="text-decoration-none text-ellipsis">
                        <img src="{{ $distributor->getAvatar() }}"
                             alt="{{ $distributor->name }}"
                             class="img-circle img-size-32 mr-2">
                        <strong class="text-dark">{{ $distributor->name }}</strong>
                    </a>
                </td>
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-envelope text-primary"></i>
                    <span class="text-muted">{{ $distributor->email }}</span>
                </td>
                <td>
                    @if($distributor->phone)
                        <i class="fas fa-phone text-success"></i>
                        <span class="text-muted">{{ $distributor->phone }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <i class="fas fa-calendar-alt text-info"></i>
                    <span class="text-muted">{{ $distributor->created_at->format('Y-m-d') }}</span>
                </td>
                <td style="width: 160px">
                    @include('dashboard.accounts.distributors.partials.actions.show')
                    @include('dashboard.accounts.distributors.partials.actions.restore')
                    @include('dashboard.accounts.distributors.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('distributors.empty')</td>
            </tr>
        @endforelse

        @if($distributors->hasPages())
            @slot('footer')
                {{ $distributors->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
