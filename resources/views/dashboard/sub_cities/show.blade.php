<x-layout :title="$sub_city->name" :breadcrumbs="['dashboard.sub_cities.show', $sub_city]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('sub_cities.attributes.name')</th>
                        <td>{{ $sub_city->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.sub_cities.partials.actions.edit')
                    @include('dashboard.sub_cities.partials.actions.delete')
                    @include('dashboard.sub_cities.partials.actions.restore')
                    @include('dashboard.sub_cities.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
