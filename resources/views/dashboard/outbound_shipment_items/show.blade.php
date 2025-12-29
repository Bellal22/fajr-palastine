<x-layout :title="$outbound_shipment_item->name" :breadcrumbs="['dashboard.outbound_shipment_items.show', $outbound_shipment_item]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('outbound_shipment_items.attributes.name')</th>
                        <td>{{ $outbound_shipment_item->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.outbound_shipment_items.partials.actions.edit')
                    @include('dashboard.outbound_shipment_items.partials.actions.delete')
                    @include('dashboard.outbound_shipment_items.partials.actions.restore')
                    @include('dashboard.outbound_shipment_items.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
