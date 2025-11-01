<x-layout :title="$inbound_shipment->name" :breadcrumbs="['dashboard.inbound_shipments.show', $inbound_shipment]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('inbound_shipments.attributes.name')</th>
                        <td>{{ $inbound_shipment->name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.description')</th>
                        <td>{{ $inbound_shipment->description }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.supplier_id')</th>
                        <td>{{ $inbound_shipment->supplier_id }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.type')</th>
                        <td><x-boolean :value="$inbound_shipment->type" /></td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.weight')</th>
                        <td>{{ $inbound_shipment->weight }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.quantity')</th>
                        <td>{{ $inbound_shipment->quantity }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.created_at')</th>
                        <td>{{ optional($inbound_shipment->created_at)->toDayDateTimeString() }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.updated_at')</th>
                        <td>{{ optional($inbound_shipment->updated_at)->toDayDateTimeString() }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.inbound_shipments.partials.actions.edit')
                    @include('dashboard.inbound_shipments.partials.actions.delete')
                    @include('dashboard.inbound_shipments.partials.actions.restore')
                    @include('dashboard.inbound_shipments.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
