<x-layout :title="$area_responsible->name" :breadcrumbs="['dashboard.area_responsibles.show', $area_responsible]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('area_responsibles.attributes.name')</th>
                        <td>{{ $area_responsible->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.area_responsibles.partials.actions.edit')
                    @include('dashboard.area_responsibles.partials.actions.delete')
                    @include('dashboard.area_responsibles.partials.actions.restore')
                    @include('dashboard.area_responsibles.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
