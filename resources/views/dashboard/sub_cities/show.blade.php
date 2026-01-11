<x-layout :title="trans('sub_cities.plural')" :breadcrumbs="['dashboard.sub_cities.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.sub_cities.partials.filter')
    </div>

    @component('dashboard::components.table-box')

        @slot('title')
            @lang('sub_cities.actions.list') ({{ count_formatted($sub_cities->total()) }})
        @endslot

        {{-- Table Header Actions --}}
        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center">
                    <x-check-all-delete
                            type="{{ \App\Models\SubCity::class }}"
                            :resource="trans('sub_cities.plural')">
                    </x-check-all-delete>

                    <div class="ml-auto d-flex gap-2">
                        @include('dashboard.sub_cities.partials.actions.trashed')
                        @include('dashboard.sub_cities.partials.actions.create')
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th style="width: 50px">
                <x-check-all></x-check-all>
            </th>
            <th>
                <i class="fas fa-map-marked-alt"></i> @lang('sub_cities.attributes.name')
            </th>
            <th class="d-none d-sm-table-cell">
                <i class="fas fa-calendar"></i> @lang('sub_cities.attributes.created_at')
            </th>
            <th class="text-center" style="width: 160px">@lang('sub_cities.actions.actions')</th>
        </tr>
        </thead>

        {{-- Table Body --}}
        <tbody>
        @forelse($sub_cities as $sub_city)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$sub_city"></x-check-all-item>
                </td>

                {{-- Sub City Name --}}
                <td>
                    <a href="{{ route('dashboard.sub_cities.show', $sub_city) }}"
                       class="text-decoration-none font-weight-bold text-dark">
                        {{ $sub_city->name }}
                    </a>
                </td>

                {{-- Created At --}}
                <td class="d-none d-sm-table-cell">
                    <span class="text-muted">{{ $sub_city->created_at->format('Y-m-d') }}</span>
                </td>

                {{-- Actions --}}
                <td>
                    <div class="btn-group" role="group">
                        @include('dashboard.sub_cities.partials.actions.show')
                        @include('dashboard.sub_cities.partials.actions.edit')
                        @include('dashboard.sub_cities.partials.actions.delete')
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <h5>@lang('sub_cities.empty')</h5>
                        <p class="mb-0">@lang('sub_cities.empty_hint')</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($sub_cities->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('sub_cities.pagination_info', [
                            'from' => $sub_cities->firstItem() ?? 0,
                            'to' => $sub_cities->lastItem() ?? 0,
                            'total' => $sub_cities->total()
                        ])
                    </div>
                    {{ $sub_cities->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
