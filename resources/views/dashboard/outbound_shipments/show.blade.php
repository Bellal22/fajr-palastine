<x-layout :title="'إرسالية صادر #' . $outbound_shipment->shipment_number" :breadcrumbs="['dashboard.outbound_shipments.show', $outbound_shipment]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الإرسالية')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.shipment_number')</th>
                        <td>
                            <strong class="text-primary">
                                <i class="fas fa-barcode"></i> {{ $outbound_shipment->shipment_number }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('outbound_shipments.attributes.project_id')</th>
                        <td>
                            @if($outbound_shipment->project)
                                <a href="{{ route('dashboard.projects.show', $outbound_shipment->project) }}" class="text-decoration-none">
                                    <i class="fas fa-project-diagram text-info"></i> {{ $outbound_shipment->project->name }}
                                </a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('outbound_shipments.attributes.sub_warehouse_id')</th>
                        <td>
                            @if($outbound_shipment->subWarehouse)
                                <div>
                                    <a href="{{ route('dashboard.sub_warehouses.show', $outbound_shipment->subWarehouse) }}" class="text-decoration-none">
                                        <i class="fas fa-warehouse text-success"></i> <strong>{{ $outbound_shipment->subWarehouse->name }}</strong>
                                    </a>
                                </div>
                                @if($outbound_shipment->subWarehouse->address || $outbound_shipment->subWarehouse->phone)
                                    <small class="text-muted">
                                        @if($outbound_shipment->subWarehouse->address)
                                            <i class="fas fa-map-marker-alt"></i> {{ $outbound_shipment->subWarehouse->address }}
                                        @endif
                                        @if($outbound_shipment->subWarehouse->phone)
                                            <br><i class="fas fa-phone"></i> {{ $outbound_shipment->subWarehouse->phone }}
                                        @endif
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('outbound_shipments.attributes.driver_name')</th>
                        <td>
                            @if($outbound_shipment->driver_name)
                                <i class="fas fa-user-tie text-secondary"></i> {{ $outbound_shipment->driver_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('outbound_shipments.attributes.notes')</th>
                        <td>{{ $outbound_shipment->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء</th>
                        <td>
                            <i class="fas fa-calendar text-info"></i> {{ $outbound_shipment->created_at->format('Y-m-d h:i A') }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.outbound_shipments.partials.actions.edit')
                    @include('dashboard.outbound_shipments.partials.actions.delete')
                    @include('dashboard.outbound_shipments.partials.actions.restore')
                    @include('dashboard.outbound_shipments.partials.actions.forceDelete')

                    <a href="{{ route('dashboard.outbound_shipments.exportPdf', $outbound_shipment) }}"
                       class="btn btn-outline-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                @endslot
            @endcomponent
        </div>
    </div>

    <!-- بيان الصادر -->
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('title', 'بيان الصادر')

                @if($outbound_shipment->items && $outbound_shipment->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;">النوع</th>
                                    <th style="width: 40%;">اسم الطرد</th>
                                    <th style="width: 15%;" class="text-center">الكمية</th>
                                    <th style="width: 15%;" class="text-center">الوزن (كجم)</th>
                                    <th style="width: 10%;" class="text-center">الوزن الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQuantity = 0;
                                    $totalWeight = 0;
                                @endphp
                                @foreach($outbound_shipment->items as $index => $item)
                                    @php
                                        $itemWeight = $item->weight ?? 0;
                                        $itemTotalWeight = $item->quantity * $itemWeight;
                                        $totalQuantity += $item->quantity;
                                        $totalWeight += $itemTotalWeight;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            @if($item->shippable_type === 'App\Models\ReadyPackage')
                                                <span class="badge badge-success"><i class="fas fa-box-open"></i> طرد جاهز</span>
                                            @elseif($item->shippable_type === 'App\Models\InternalPackage')
                                                <span class="badge badge-info"><i class="fas fa-box"></i> طرد داخلي</span>
                                            @else
                                                <span class="badge badge-secondary">{{ class_basename($item->shippable_type) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->shippable->name ?? '-' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($itemWeight, 2) }}</td>
                                        <td class="text-center">
                                            <strong>{{ number_format($itemTotalWeight, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="text-right">الإجمالي</th>
                                    <th class="text-center">{{ $totalQuantity }}</th>
                                    <th></th>
                                    <th class="text-center">
                                        <strong class="text-success">{{ number_format($totalWeight, 2) }} كجم</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> لا توجد بنود في هذه الإرسالية
                    </div>
                @endif
            @endcomponent
        </div>
    </div>
</x-layout>
