<x-layout :title="trans('researchers.trashed')" :breadcrumbs="['dashboard.researchers.trashed']">
    @include('dashboard.accounts.researchers.partials.filter')

    @component('dashboard::components.table-box')

        @slot('title')
            @lang('researchers.actions.list') ({{ count_formatted($researchers->total()) }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <x-check-all-force-delete
                        type="{{ \App\Models\Researcher::class }}"
                        :resource="trans('researchers.plural')"></x-check-all-force-delete>
                <x-check-all-restore
                        type="{{ \App\Models\Researcher::class }}"
                        :resource="trans('researchers.plural')"></x-check-all-restore>
            </th>
        </tr>
        <tr>
            <th>
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
                <td>
                    <x-check-all-item :model="$researcher"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.researchers.trashed.show', $researcher) }}"
                       class="text-decoration-none text-ellipsis">
                            <span class="index-flag">
                            @include('dashboard.accounts.researchers.partials.flags.svg')
                            </span>
                        <img src="{{ $researcher->getAvatar() }}"
                             alt="Product 1"
                             class="img-circle img-size-32 mr-2">
                        {{ $researcher->name }}
                    </a>
                </td>

                <td class="d-none d-md-table-cell">
                    {{ $researcher->email }}
                </td>
                <td>
                    @include('dashboard.accounts.researchers.partials.flags.phone')
                </td>
                <td>{{ $researcher->created_at->format('Y-m-d') }}</td>

                <td style="width: 160px">
                    @include('dashboard.accounts.researchers.partials.actions.show')
                    @include('dashboard.accounts.researchers.partials.actions.restore')
                    @include('dashboard.accounts.researchers.partials.actions.forceDelete')
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
