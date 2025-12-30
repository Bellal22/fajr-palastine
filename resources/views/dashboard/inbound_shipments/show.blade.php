<x-layout :title="'إرسالية وارد #' . $inbound_shipment->shipment_number" :breadcrumbs="['dashboard.inbound_shipments.show', $inbound_shipment]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الإرسالية')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('inbound_shipments.attributes.shipment_number')</th>
                        <td><strong>{{ $inbound_shipment->shipment_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.supplier_id')</th>
                        <td>{{ $inbound_shipment->supplier?->name ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.inbound_type')</th>
                        <td>
                            @if($inbound_shipment->inbound_type === 'ready_package')
                                <span class="badge badge-success">طرد جاهز</span>
                            @else
                                <span class="badge badge-info">صنف مفرد</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('inbound_shipments.attributes.created_at')</th>
                        <td>{{ $inbound_shipment->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    @if($inbound_shipment->notes)
                    <tr>
                        <th>@lang('inbound_shipments.attributes.notes')</th>
                        <td>{{ $inbound_shipment->notes }}</td>
                    </tr>
                    @endif
                    </tbody>
                </table>

                @slot('footer')
                    <a href="{{ route('dashboard.inbound_shipments.exportPdf', $inbound_shipment) }}"
                       class="btn btn-outline-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    @include('dashboard.inbound_shipments.partials.actions.edit')
                    @include('dashboard.inbound_shipments.partials.actions.delete')
                    @include('dashboard.inbound_shipments.partials.actions.restore')
                    @include('dashboard.inbound_shipments.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    <!-- بيان الأصناف / الطرود -->
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @if($inbound_shipment->inbound_type === 'single_item')
                    @slot('title', 'بيان الأصناف')

                    @if($inbound_shipment->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 25%;">@lang('inbound_shipments.attributes.item_name')</th>
                                        <th style="width: 30%;">@lang('inbound_shipments.attributes.description')</th>
                                        <th style="width: 12%;">@lang('inbound_shipments.attributes.quantity')</th>
                                        <th style="width: 13%;">وزن الوحدة (كجم)</th>
                                        <th style="width: 15%;">الوزن الإجمالي (كجم)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = 0;
                                        $totalWeight = 0;
                                    @endphp
                                    @foreach($inbound_shipment->items as $index => $item)
                                        @php
                                            $itemTotalWeight = $item->quantity * ($item->weight ?? 0);
                                            $totalQuantity += $item->quantity;
                                            $totalWeight += $itemTotalWeight;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $item->name }}</strong></td>
                                            <td>{{ $item->description ?? '-' }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
                                            <td class="text-center"><strong>{{ number_format($itemTotalWeight, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">الإجمالي</th>
                                        <th class="text-center">{{ $totalQuantity }}</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center"><strong>{{ number_format($totalWeight, 2) }}</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> لا توجد أصناف في هذه الإرسالية
                        </div>
                    @endif

                @elseif($inbound_shipment->inbound_type === 'ready_package')
                    @slot('title', 'بيان الطرود الجاهزة')

                    @if($inbound_shipment->readyPackages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 20%;">@lang('inbound_shipments.attributes.package_name')</th>
                                        <th style="width: 35%;">@lang('inbound_shipments.attributes.description')</th>
                                        <th style="width: 12%;">@lang('inbound_shipments.attributes.quantity')</th>
                                        <th style="width: 13%;">وزن الوحدة (كجم)</th>
                                        <th style="width: 15%;">الوزن الإجمالي (كجم)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = 0;
                                        $totalWeight = 0;
                                    @endphp
                                    @foreach($inbound_shipment->readyPackages as $index => $package)
                                        @php
                                            $packageTotalWeight = $package->quantity * ($package->weight ?? 0);
                                            $totalQuantity += $package->quantity;
                                            $totalWeight += $packageTotalWeight;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $package->name }}</strong></td>
                                            <td>{{ $package->description ?? '-' }}</td>
                                            <td class="text-center">{{ $package->quantity }}</td>
                                            <td class="text-center">{{ $package->weight ? number_format($package->weight, 2) : '-' }}</td>
                                            <td class="text-center"><strong>{{ number_format($packageTotalWeight, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">الإجمالي</th>
                                        <th class="text-center">{{ $totalQuantity }}</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center"><strong>{{ number_format($totalWeight, 2) }}</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> لا توجد طرود في هذه الإرسالية
                        </div>
                    @endif
                @endif
            @endcomponent
        </div>
    </div>
</x-layout>
