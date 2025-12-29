<x-layout :title="'إرسالية صادر #' . $outbound_shipment->shipment_number" :breadcrumbs="['dashboard.outbound_shipments.show', $outbound_shipment]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الإرسالية')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.shipment_number')</th>
                        <td>{{ $outbound_shipment->shipment_number }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.project_id')</th>
                        <td>{{ $outbound_shipment->project->name ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.sub_warehouse_id')</th>
                        <td>
                            {{ $outbound_shipment->subWarehouse->name ?? '-' }}
                            @if($outbound_shipment->subWarehouse)
                                <br>
                                <small class="text-muted">
                                    {{ $outbound_shipment->subWarehouse->address }}
                                    @if($outbound_shipment->subWarehouse->phone)
                                        | {{ $outbound_shipment->subWarehouse->phone }}
                                    @endif
                                </small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.driver_name')</th>
                        <td>{{ $outbound_shipment->driver_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('outbound_shipments.attributes.notes')</th>
                        <td>{{ $outbound_shipment->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th width="200">تاريخ الإنشاء</th>
                        <td>{{ $outbound_shipment->created_at->format('Y-m-d h:i A') }}</td>
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
                
                @if($outbound_shipment->items->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>النوع</th>
                                <th>اسم الطرد</th>
                                <th>الكمية</th>
                                <th>الوزن (كجم)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outbound_shipment->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($item->shippable_type === 'App\Models\ReadyPackage')
                                            <span class="badge badge-success">طرد جاهز</span>
                                        @else
                                            <span class="badge badge-info">طرد داخلي</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->shippable->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->weight ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="3">الإجمالي</th>
                                <th>{{ $outbound_shipment->items->sum('quantity') }}</th>
                                <th>{{ $outbound_shipment->items->sum('weight') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="alert alert-info">
                        لا توجد بنود في هذه الإرسالية
                    </div>
                @endif
            @endcomponent
        </div>
    </div>
</x-layout>
