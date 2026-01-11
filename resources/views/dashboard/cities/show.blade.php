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
