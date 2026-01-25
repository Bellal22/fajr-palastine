<x-layout :title="$neighborhood->name" :breadcrumbs="['dashboard.neighborhoods.show', $neighborhood]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <div class="card-body text-center p-4 bg-light border-bottom">
                    <div class="mb-3">
                        <i class="fas fa-map-marked-alt fa-4x text-primary"></i>
                    </div>
                    <h4>{{ $neighborhood->name }}</h4>
                    @if($neighborhood->city)
                        <a href="{{ route('dashboard.cities.show', $neighborhood->city) }}" class="badge badge-info font-size-14">
                            <i class="fas fa-map-marker-alt"></i> {{ $neighborhood->city->name }}
                        </a>
                    @endif
                </div>

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200"><i class="fas fa-map-signs"></i> @lang('neighborhoods.attributes.name')</th>
                        <td>{{ $neighborhood->name }}</td>
                    </tr>
                    @if($neighborhood->city)
                    <tr>
                        <th><i class="fas fa-city"></i> @lang('cities.singular')</th>
                        <td>
                            <a href="{{ route('dashboard.cities.show', $neighborhood->city) }}" class="text-decoration-none">
                                {{ $neighborhood->city->name }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th><i class="fas fa-calendar"></i> @lang('neighborhoods.attributes.created_at')</th>
                        <td>{{ $neighborhood->created_at->diffForHumans() }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    <div class="d-flex justify-content-center">
                        @include('dashboard.neighborhoods.partials.actions.edit')
                        @include('dashboard.neighborhoods.partials.actions.delete')
                        @include('dashboard.neighborhoods.partials.actions.restore')
                        @include('dashboard.neighborhoods.partials.actions.forceDelete')
                    </div>
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
