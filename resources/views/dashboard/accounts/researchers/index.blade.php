<x-layout :title="trans('researchers.plural')" :breadcrumbs="['dashboard.researchers.index']">
    @include('dashboard.accounts.researchers.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('researchers.actions.list') ({{ count_formatted($researchers->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\Researcher::class }}"
                        :resource="trans('researchers.plural')">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.accounts.researchers.partials.actions.create')
                        @include('dashboard.accounts.researchers.partials.actions.trashed')
                    </div>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>@lang('researchers.attributes.name')</th>
            <th class="d-none d-md-table-cell">@lang('researchers.attributes.email')</th>
            <th>@lang('researchers.attributes.phone')</th>
            <th>@lang('researchers.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($researchers as $researcher)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$researcher"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.researchers.show', $researcher) }}"
                       class="text-decoration-none text-ellipsis">
                        <span class="index-flag">
                            @include('dashboard.accounts.researchers.partials.flags.svg')
                        </span>
                        <img src="{{ $researcher->getAvatar() }}"
                             alt="{{ $researcher->name }}"
                             class="img-circle img-size-32 mr-2">
                        <strong class="text-dark">{{ $researcher->name }}</strong>
                    </a>
                </td>
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-envelope text-primary"></i>
                    <span class="text-muted">{{ $researcher->email }}</span>
                </td>
                <td>
                    @if($researcher->phone)
                        <i class="fas fa-phone text-success"></i>
                        <span class="text-muted">{{ $researcher->phone }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <i class="fas fa-calendar-alt text-info"></i>
                    <span class="text-muted">{{ $researcher->created_at->format('Y-m-d') }}</span>
                </td>
                <td style="width: 160px">
                    @include('dashboard.accounts.researchers.partials.actions.impersonate')
                    @include('dashboard.accounts.researchers.partials.actions.edit')
                    @include('dashboard.accounts.researchers.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('researchers.empty')</td>
            </tr>
        @endforelse

        @if($researchers->hasPages())
            @slot('footer')
                {{ $researchers->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
