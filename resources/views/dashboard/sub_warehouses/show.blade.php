<x-layout :title="$sub_warehouse->name" :breadcrumbs="['dashboard.sub_warehouses.show', $sub_warehouse]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('sub_warehouses.attributes.name')</th>
                        <td>{{ $sub_warehouse->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.sub_warehouses.partials.actions.edit')
                    @include('dashboard.sub_warehouses.partials.actions.delete')
                    @include('dashboard.sub_warehouses.partials.actions.restore')
                    @include('dashboard.sub_warehouses.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
