<x-layout :title="$location->name" :breadcrumbs="['dashboard.locations.show', $location]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('locations.attributes.name')</th>
                        <td>{{ $location->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.locations.partials.actions.edit')
                    @include('dashboard.locations.partials.actions.delete')
                    @include('dashboard.locations.partials.actions.restore')
                    @include('dashboard.locations.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
