<x-layout :title="trans('neighborhoods.plural')" :breadcrumbs="['dashboard.neighborhoods.index']">
    @include('dashboard.neighborhoods.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-map-marked-alt text-primary mr-1"></i> @lang('neighborhoods.actions.list')
            <span class="badge badge-primary badge-pill ml-1">{{ count_formatted($neighborhoods->total()) }}</span>
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <div class="d-flex align-items-center flex-wrap">
                    <x-check-all-delete
                            type="{{ \App\Models\Neighborhood::class }}"
                            :resource="trans('neighborhoods.plural')"></x-check-all-delete>
                </div>
                <div class="d-flex align-items-center flex-wrap">
                    @include('dashboard.neighborhoods.partials.actions.trashed')
                    @include('dashboard.neighborhoods.partials.actions.create')
                </div>
            </div>
          </th>
        </tr>
        <tr class="bg-light">
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th><i class="fas fa-map-signs"></i> @lang('neighborhoods.attributes.name')</th>
            <th><i class="fas fa-city"></i> @lang('cities.singular')</th>
            <th class="d-none d-sm-table-cell"><i class="fas fa-calendar"></i> @lang('neighborhoods.attributes.created_at')</th>
            <th style="width: 160px" class="text-center"><i class="fas fa-cog"></i> @lang('neighborhoods.actions.actions')</th>
        </tr>
        </thead>
        <tbody>
        @forelse($neighborhoods as $neighborhood)
            <tr class="align-middle">
                <td class="text-center">
                  <x-check-all-item :model="$neighborhood"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.neighborhoods.show', $neighborhood) }}"
                       class="text-decoration-none font-weight-bold text-dark">
                        {{ $neighborhood->name }}
                    </a>
                </td>
                <td>
                    @if($neighborhood->city)
                        <a href="{{ route('dashboard.cities.show', $neighborhood->city) }}" class="badge badge-info text-white">
                            <i class="fas fa-map-marker-alt"></i> {{ $neighborhood->city->name }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="text-muted" data-toggle="tooltip" title="{{ $neighborhood->created_at }}">
                        {{ $neighborhood->created_at->diffForHumans() }}
                    </span>
                </td>

                <td class="text-center">
                    <div class="btn-group">
                        @include('dashboard.neighborhoods.partials.actions.show')
                        @include('dashboard.neighborhoods.partials.actions.edit')
                        @include('dashboard.neighborhoods.partials.actions.delete')
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-map-marked-alt fa-3x mb-3 d-block"></i>
                        <h5>@lang('neighborhoods.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        
        @if($neighborhoods->hasPages())
            @slot('footer')
                {{ $neighborhoods->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
