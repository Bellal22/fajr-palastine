<x-layout :title="trans('inbound_shipments.show.title', ['number' => $inbound_shipment->shipment_number])" :breadcrumbs="['dashboard.inbound_shipments.index', 'dashboard.inbound_shipments.show']">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex p-4 shadow"
                             style="background-color: {{ $inbound_shipment->inbound_type === 'ready_package' ? '#28a745' : '#17a2b8' }}20;">
                            <i class="fas fa-truck-loading fa-4x"
                               style="color: {{ $inbound_shipment->inbound_type === 'ready_package' ? '#28a745' : '#17a2b8' }};"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $inbound_shipment->shipment_number }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-truck-loading"></i> @lang('inbound_shipments.singular')
                    </p>
                    <div class="mb-3">
                        @if($inbound_shipment->inbound_type === 'ready_package')
                            <span class="badge badge-success badge-pill px-3 py-2">
                                <i class="fas fa-box"></i>
                                @lang('inbound_shipments.types.ready_package')
                            </span>
                        @else
                            <span class="badge badge-info badge-pill px-3 py-2">
                                <i class="fas fa-box-open"></i>
                                @lang('inbound_shipments.types.single_item')
                            </span>
                        @endif
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('inbound_shipments.attributes.created_at'):</span>
                            <br>
                            <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                {{ $inbound_shipment->created_at->format('Y-m-d H:i') }}
                            </small>
                        </p>
                        @if($inbound_shipment->updated_at && $inbound_shipment->updated_at != $inbound_shipment->created_at)
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('inbound_shipments.attributes.updated_at'):</span>
                                <br>
                                <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                    {{ $inbound_shipment->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light p-3">
                    {{-- Export PDF Button --}}
                    <a href="{{ route('dashboard.inbound_shipments.exportPdf', $inbound_shipment) }}"
                       class="btn btn-danger btn-block mb-2" target="_blank">
                        <i class="fas fa-file-pdf"></i> @lang('inbound_shipments.actions.export_pdf')
                    </a>

                    {{-- Action Buttons Group --}}
                    <div class="btn-group w-100 mb-2" role="group">
                        @can('update', $inbound_shipment)
                            <a href="{{ route('dashboard.inbound_shipments.edit', $inbound_shipment) }}"
                               class="btn btn-primary"
                               title="@lang('inbound_shipments.actions.edit')">
                                <i class="fas fa-edit"></i> @lang('inbound_shipments.actions.edit')
                            </a>
                        @endcan

                        @can('delete', $inbound_shipment)
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="deleteInboundShipment({{ $inbound_shipment->id }})"
                                    title="@lang('inbound_shipments.actions.delete')">
                                <i class="fas fa-trash"></i> @lang('inbound_shipments.actions.delete')
                            </button>

                            <form id="delete-form-{{ $inbound_shipment->id }}"
                                  action="{{ route('dashboard.inbound_shipments.destroy', $inbound_shipment) }}"
                                  method="POST"
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-12">

            {{-- Shipment Info Card --}}
            <div class="card border-primary mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('inbound_shipments.sections.shipment_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-barcode text-primary"></i>
                                    @lang('inbound_shipments.attributes.shipment_number')
                                </th>
                                <td>
                                    <strong class="text-primary">{{ $inbound_shipment->shipment_number }}</strong>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-truck text-success"></i>
                                    @lang('inbound_shipments.attributes.supplier_id')
                                </th>
                                <td>
                                    @if($inbound_shipment->supplier)
                                        <a href="{{ route('dashboard.suppliers.show', $inbound_shipment->supplier) }}"
                                           class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $inbound_shipment->supplier->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('inbound_shipments.messages.no_supplier')
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-layer-group text-info"></i>
                                    @lang('inbound_shipments.attributes.inbound_type')
                                </th>
                                <td>
                                    @if($inbound_shipment->inbound_type === 'ready_package')
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-box"></i>
                                            @lang('inbound_shipments.types.ready_package')
                                        </span>
                                    @else
                                        <span class="badge badge-info badge-pill">
                                            <i class="fas fa-box-open"></i>
                                            @lang('inbound_shipments.types.single_item')
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            @if($inbound_shipment->notes)
                                <tr>
                                    <th>
                                        <i class="fas fa-sticky-note text-warning"></i>
                                        @lang('inbound_shipments.attributes.notes')
                                    </th>
                                    <td>{{ $inbound_shipment->notes }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card border-info mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> @lang('inbound_shipments.sections.statistics')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @php
                            $totalQuantity = 0;
                            $totalWeight = 0;
                            $itemsCount = 0;

                            if($inbound_shipment->inbound_type === 'ready_package') {
                                $itemsCount = $inbound_shipment->readyPackages()->count();
                                foreach($inbound_shipment->readyPackages as $package) {
                                    $totalQuantity += $package->quantity;
                                    $totalWeight += $package->quantity * ($package->weight ?? 0);
                                }
                            } else {
                                $itemsCount = $inbound_shipment->items()->count();
                                foreach($inbound_shipment->items as $item) {
                                    $totalQuantity += $item->quantity;
                                    $totalWeight += $item->quantity * ($item->weight ?? 0);
                                }
                            }
                        @endphp

                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-cubes fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">@lang('inbound_shipments.statistics.items_count')</h6>
                                <h4 class="mb-0 text-primary">{{ $itemsCount }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-sort-numeric-up fa-2x text-success mb-2"></i>
                                <h6 class="mb-1">@lang('inbound_shipments.statistics.total_quantity')</h6>
                                <h4 class="mb-0 text-success">{{ $totalQuantity }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-weight fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">@lang('inbound_shipments.statistics.total_weight')</h6>
                                <h4 class="mb-0 text-warning">{{ number_format($totalWeight, 2) }} @lang('inbound_shipments.units.kg')</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Items/Packages Details --}}
    <div class="row">
        <div class="col-12">
            @if($inbound_shipment->inbound_type === 'single_item')
                <div class="card border-success mb-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-box"></i> @lang('inbound_shipments.sections.single_items')
                            <span class="badge badge-light">{{ $inbound_shipment->items->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($inbound_shipment->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">#</th>
                                            <th style="width: 25%;">
                                                <i class="fas fa-tag"></i> @lang('inbound_shipments.attributes.item_name')
                                            </th>
                                            <th style="width: 30%;">
                                                <i class="fas fa-align-right"></i> @lang('inbound_shipments.attributes.description')
                                            </th>
                                            <th style="width: 12%;" class="text-center">
                                                <i class="fas fa-sort-numeric-up"></i> @lang('inbound_shipments.attributes.quantity')
                                            </th>
                                            <th style="width: 13%;" class="text-center">
                                                <i class="fas fa-weight"></i> @lang('inbound_shipments.table.unit_weight')
                                            </th>
                                            <th style="width: 15%;" class="text-center">
                                                <i class="fas fa-weight-hanging"></i> @lang('inbound_shipments.table.total_weight')
                                            </th>
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
                                                <td class="text-center">
                                                    <span class="badge badge-primary badge-pill">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-center">{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($itemTotalWeight, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <th colspan="3" class="text-{{ app()->isLocale('ar') ? 'left' : 'right' }}">
                                                @lang('inbound_shipments.table.total')
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-success badge-pill">{{ $totalQuantity }}</span>
                                            </th>
                                            <th class="text-center">-</th>
                                            <th class="text-center text-success">{{ number_format($totalWeight, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0 m-3">
                                <i class="fas fa-info-circle"></i> @lang('inbound_shipments.messages.no_items')
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($inbound_shipment->inbound_type === 'ready_package')
                <div class="card border-info mb-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-truck-loading"></i> @lang('inbound_shipments.sections.ready_packages')
                            <span class="badge badge-light">{{ $inbound_shipment->readyPackages->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($inbound_shipment->readyPackages->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">#</th>
                                            <th style="width: 20%;">
                                                <i class="fas fa-boxes"></i> @lang('inbound_shipments.attributes.package_name')
                                            </th>
                                            <th style="width: 35%;">
                                                <i class="fas fa-align-right"></i> @lang('inbound_shipments.attributes.description')
                                            </th>
                                            <th style="width: 12%;" class="text-center">
                                                <i class="fas fa-sort-numeric-up"></i> @lang('inbound_shipments.attributes.quantity')
                                            </th>
                                            <th style="width: 13%;" class="text-center">
                                                <i class="fas fa-weight"></i> @lang('inbound_shipments.table.unit_weight')
                                            </th>
                                            <th style="width: 15%;" class="text-center">
                                                <i class="fas fa-weight-hanging"></i> @lang('inbound_shipments.table.total_weight')
                                            </th>
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
                                                <td class="text-center">
                                                    <span class="badge badge-primary badge-pill">{{ $package->quantity }}</span>
                                                </td>
                                                <td class="text-center">{{ $package->weight ? number_format($package->weight, 2) : '-' }}</td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($packageTotalWeight, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <th colspan="3" class="text-{{ app()->isLocale('ar') ? 'left' : 'right' }}">
                                                @lang('inbound_shipments.table.total')
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-success badge-pill">{{ $totalQuantity }}</span>
                                            </th>
                                            <th class="text-center">-</th>
                                            <th class="text-center text-success">{{ number_format($totalWeight, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0 m-3">
                                <i class="fas fa-info-circle"></i> @lang('inbound_shipments.messages.no_packages')
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function deleteInboundShipment(id) {
            if (confirm('@lang('inbound_shipments.dialogs.delete')')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    @endpush

</x-layout>
