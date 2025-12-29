<x-layout :title="$inbound_shipment->shipment_number" :breadcrumbs="['dashboard.inbound_shipments.show', $inbound_shipment]">
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('inbound_shipments.attributes.shipment_number')</th>
                        <td>{{ $inbound_shipment->shipment_number }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.supplier_id')</th>
                        <td>{{ $inbound_shipment->supplier?->name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.inbound_type')</th>
                        <td>@lang('inbound_shipments.types.' . $inbound_shipment->inbound_type)</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.notes')</th>
                        <td>{{ $inbound_shipment->notes }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.created_at')</th>
                        <td>{{ optional($inbound_shipment->created_at)->toDayDateTimeString() }}</td>
                    </tr>
                    </tbody>
                </table>

                @if($inbound_shipment->isSingleItem())
                    <h4 class="p-3">@lang('inbound_shipments.sections.single_items')</h4>
                    <table class="table table-striped table-middle">
                        <thead>
                            <tr>
                                <th>@lang('inbound_shipments.attributes.item_name')</th>
                                <th>@lang('inbound_shipments.attributes.description')</th>
                                <th>@lang('inbound_shipments.attributes.quantity')</th>
                                <th>@lang('inbound_shipments.attributes.weight_kg')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inbound_shipment->items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->weight }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($inbound_shipment->isReadyPackage())
                    <h4 class="p-3">@lang('inbound_shipments.sections.ready_packages')</h4>
                    <table class="table table-striped table-middle">
                        <thead>
                            <tr>
                                <th>@lang('inbound_shipments.attributes.package_name')</th>
                                <th>@lang('inbound_shipments.attributes.description')</th>
                                <th>@lang('inbound_shipments.attributes.quantity')</th>
                                <th>@lang('inbound_shipments.attributes.weight_kg')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inbound_shipment->readyPackages as $package)
                                <tr>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->description }}</td>
                                    <td>{{ $package->quantity }}</td>
                                    <td>{{ $package->weight }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @slot('footer')
                    <a href="{{ route('dashboard.inbound_shipments.exportPdf', $inbound_shipment) }}" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> @lang('inbound_shipments.buttons.export_pdf')
                    </a>
                    @include('dashboard.inbound_shipments.partials.actions.edit')
                    @include('dashboard.inbound_shipments.partials.actions.delete')
                    @include('dashboard.inbound_shipments.partials.actions.restore')
                    @include('dashboard.inbound_shipments.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
