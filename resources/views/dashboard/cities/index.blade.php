<x-layout :title="trans('cities.plural')" :breadcrumbs="['dashboard.cities.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.cities.partials.filter')
    </div>

    @component('dashboard::components.table-box')

        @slot('title')
            <i class="fas fa-city text-primary mr-1"></i> @lang('cities.actions.list')
            <span class="badge badge-primary badge-pill ml-1">{{ count_formatted($cities->total()) }}</span>
        @endslot

        {{-- Table Header Actions --}}
        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center">
                    <x-check-all-delete
                            type="{{ \App\Models\City::class }}"
                            :resource="trans('cities.plural')">
                    </x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.cities.partials.actions.trashed')
                        @include('dashboard.cities.partials.actions.create')
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
                <i class="fas fa-map-marker-alt"></i> @lang('cities.attributes.name')
            </th>
            <th class="d-none d-sm-table-cell">
                <i class="fas fa-calendar"></i> @lang('cities.attributes.created_at')
            </th>
            <th class="text-center">
                <i class="fas fa-map-marked"></i> @lang('neighborhoods.plural')
            </th>
            <th class="text-center" style="width: 160px">
                <i class="fas fa-cog"></i> @lang('cities.actions.actions')
            </th>
        </tr>
        </thead>

        {{-- Table Body --}}
        <tbody>
        @forelse($cities as $city)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$city"></x-check-all-item>
                </td>

                {{-- City Name --}}
                <td>
                    <a href="{{ route('dashboard.cities.show', $city) }}"
                       class="text-decoration-none font-weight-bold text-dark">
                        <i class="fas fa-map-marker-alt text-info"></i>
                        {{ $city->name }}
                    </a>
                </td>

                {{-- Created At --}}
                <td class="d-none d-sm-table-cell">
                    <span class="text-muted" data-toggle="tooltip"
                          title="{{ $city->created_at->format('Y-m-d H:i') }}">
                        <i class="fas fa-calendar-alt text-info"></i>
                        {{ $city->created_at->diffForHumans() }}
                    </span>
                </td>

                {{-- Neighborhood Count --}}
                <td class="text-center">
                    <span class="badge badge-warning badge-pill">
                        <i class="fas fa-map-marked"></i> {{ $city->neighborhoods_count }}
                    </span>
                </td>

                {{-- Actions --}}
                <td>
                    <div class="btn-group" role="group">
                        @include('dashboard.cities.partials.actions.show')
                        @include('dashboard.cities.partials.actions.edit')
                        @include('dashboard.cities.partials.actions.delete')
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-city fa-3x mb-3 d-block"></i>
                        <h5>@lang('cities.empty')</h5>
                        <p class="mb-0">@lang('cities.empty_hint')</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($cities->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('cities.pagination_info', [
                            'from' => $cities->firstItem() ?? 0,
                            'to' => $cities->lastItem() ?? 0,
                            'total' => $cities->total()
                        ])
                    </div>
                    {{ $cities->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
