<x-layout :title="'طرد جاهز: ' . $ready_package->name" :breadcrumbs="['dashboard.ready_packages.show', $ready_package]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الطرد الجاهز')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('ready_packages.attributes.name')</th>
                        <td><strong>{{ $ready_package->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('ready_packages.attributes.description')</th>
                        <td>{{ $ready_package->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('ready_packages.attributes.inbound_shipment_id')</th>
                        <td>
                            @if($ready_package->inboundShipment)
                                <a href="{{ route('dashboard.inbound_shipments.show', $ready_package->inboundShipment) }}"
                                   class="badge badge-info">
                                    {{ $ready_package->inboundShipment->shipment_number }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('ready_packages.attributes.quantity')</th>
                        <td><span class="badge badge-primary">{{ $ready_package->quantity }}</span></td>
                    </tr>
                    <tr>
                        <th>@lang('ready_packages.attributes.weight')</th>
                        <td>{{ $ready_package->weight ? number_format($ready_package->weight, 2) . ' كجم' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>الوزن الإجمالي</th>
                        <td>
                            <strong>{{ number_format($ready_package->quantity * ($ready_package->weight ?? 0), 2) }} كجم</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('ready_packages.attributes.created_at')</th>
                        <td>{{ $ready_package->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.ready_packages.partials.actions.edit')
                    @include('dashboard.ready_packages.partials.actions.delete')
                    @include('dashboard.ready_packages.partials.actions.restore')
                    @include('dashboard.ready_packages.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    @if($ready_package->items && $ready_package->items->count() > 0)
    <!-- محتويات الطرد -->
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('title', 'محتويات الطرد')

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 35%;">اسم الصنف</th>
                                <th style="width: 40%;">الوصف</th>
                                <th style="width: 10%;">الكمية</th>
                                <th style="width: 10%;">الوزن (كجم)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ready_package->items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
    @endif
</x-layout>
