<x-layout :title="$city->name" :breadcrumbs="['dashboard.cities.show', $city]">
    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- City Icon Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="city-icon mb-3">
                    <i class="fas fa-map fa-5x text-primary"></i>
                </div>
                <h4 class="mb-1">{{ $city->name }}</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-map-marker-alt"></i> @lang('cities.singular')
                </p>
            </div>

            {{-- City Details Section --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary"></i>
                            @lang('cities.city_details')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-city"></i> @lang('cities.attributes.name')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $city->name }}
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-0">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-calendar"></i> @lang('cities.attributes.created_at')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <span data-toggle="tooltip"
                                      title="{{ $city->created_at->format('Y-m-d H:i') }}">
                                    {{ $city->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            {{-- Neighborhoods List --}}
            <div class="col-md-12 mt-4">
                <div class="card border-0 shadow-sm mt-4 overflow-hidden">
                    <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-map-marked-alt mr-1"></i>
                            @lang('neighborhoods.plural')
                            <span class="badge badge-dark badge-pill ml-2">{{ count_formatted($city->neighborhoods->count()) }}</span>
                        </h5>
                        @include('dashboard.neighborhoods.partials.actions.trashed')
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="100" class="py-2 px-4 border-bottom-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <x-check-all-delete
                                                        type="{{ \App\Models\Neighborhood::class }}"
                                                        :resource="trans('neighborhoods.plural')"></x-check-all-delete>

                                                <a href="{{ route('dashboard.neighborhoods.create', ['city_id' => $city->id]) }}" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                                                    <i class="fas fa-plus"></i> @lang('neighborhoods.actions.create')
                                                </a>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr class="text-dark font-weight-bold border-top shadow-none">
                                        <th style="width: 50px;" class="text-center border-top-0">
                                            <x-check-all></x-check-all>
                                        </th>
                                        <th style="width: 60px" class="text-center border-top-0">#</th>
                                        <th class="border-top-0"><i class="fas fa-map-signs text-info mr-1"></i> @lang('neighborhoods.attributes.name')</th>
                                        <th class="text-center border-top-0"><i class="fas fa-history text-info mr-1"></i> @lang('neighborhoods.attributes.created_at')</th>
                                        <th style="width: 140px" class="text-center border-top-0"><i class="fas fa-tools text-info mr-1"></i> @lang('neighborhoods.actions.options')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($city->neighborhoods as $neighborhood)
                                    <tr class="align-middle border-bottom">
                                        <td class="text-center">
                                            <x-check-all-item :model="$neighborhood"></x-check-all-item>
                                        </td>
                                        <td class="text-center text-muted font-weight-bold">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-dark h6 mb-0">{{ $neighborhood->name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center">
                                                @if($neighborhood->created_at)
                                                    <span class="text-dark small font-weight-bold">{{ $neighborhood->created_at->translatedFormat('Y-m-d') }}</span>
                                                    <span class="text-muted font-size-12">{{ $neighborhood->created_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded">
                                                @include('dashboard.neighborhoods.partials.actions.edit')
                                                @include('dashboard.neighborhoods.partials.actions.delete')
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="100" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-map-marked fa-3x text-muted mb-3 d-block"></i>
                                                <h5>@lang('neighborhoods.empty')</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @slot('footer')
            <div class="d-flex flex-wrap">
                <div class="mr-2">
                    @include('dashboard.cities.partials.actions.edit')
                </div>
                <div class="mr-2">
                    @include('dashboard.cities.partials.actions.delete')
                </div>
                <div class="mr-2">
                    @include('dashboard.cities.partials.actions.restore')
                </div>
                <div>
                    @include('dashboard.cities.partials.actions.forceDelete')
                </div>
            </div>
        @endslot
    @endcomponent
</x-layout>
