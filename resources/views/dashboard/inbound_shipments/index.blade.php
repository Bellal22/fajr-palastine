<x-layout :title="trans('inbound_shipments.plural')" :breadcrumbs="['dashboard.inbound_shipments.index']">
    @include('dashboard.inbound_shipments.partials.filter')

    @push('styles')
        <style>
            /* Table fixed layout */
            .table-box table {
                table-layout: fixed;
                width: 100%;
            }

            /* Prevent dropdown from affecting table layout */
            .table-box .dropdown {
                position: static !important;
            }

            .table-box .dropdown-menu {
                position: absolute !important;
            }

            /* Text overflow handling */
            .table-box td {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .table-box td a {
                display: inline-block;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        </style>
    @endpush

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-truck-loading"></i> @lang('inbound_shipments.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($inbound_shipments->total()) }}</span>
        @endslot

        <thead>
            {{-- Actions Row --}}
            <tr>
                <th colspan="100">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        {{-- Left Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                                <x-check-all-delete
                                    type="{{ \App\Models\InboundShipment::class }}"
                                    :resource="trans('inbound_shipments.plural')">
                                </x-check-all-delete>
                            </div>
                        </div>

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                                @include('dashboard.inbound_shipments.partials.actions.create')
                            </div>
                            <div>
                                @include('dashboard.inbound_shipments.partials.actions.trashed')
                            </div>
                        </div>
                    </div>
                </th>
            </tr>

            {{-- Table Column Headers --}}
            <tr class="bg-light">
                <th style="width: 40px">
                    <x-check-all></x-check-all>
                </th>
                <th style="width: 15%">
                    <i class="fas fa-barcode"></i> @lang('inbound_shipments.attributes.shipment_number')
                </th>
                <th style="width: 20%">
                    <i class="fas fa-truck"></i> @lang('inbound_shipments.attributes.supplier_id')
                </th>
                <th style="width: 15%">
                    <i class="fas fa-layer-group"></i> @lang('inbound_shipments.attributes.inbound_type')
                </th>
                <th style="width: 20%">
                    <i class="fas fa-boxes"></i> @lang('inbound_shipments.attributes.items_count')
                </th>
                <th style="width: 15%">
                    <i class="fas fa-calendar"></i> @lang('inbound_shipments.attributes.created_at')
                </th>
                <th class="text-center" style="width: 80px">
                    <i class="fas fa-cog"></i> @lang('inbound_shipments.actions.actions')
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($inbound_shipments as $inbound_shipment)
                <tr class="align-middle">
                    {{-- Checkbox --}}
                    <td>
                        <x-check-all-item :model="$inbound_shipment"></x-check-all-item>
                    </td>

                    {{-- Shipment Number --}}
                    <td title="{{ $inbound_shipment->shipment_number }}">
                        <a href="{{ route('dashboard.inbound_shipments.show', $inbound_shipment) }}"
                           class="text-decoration-none font-weight-bold text-primary">
                            <i class="fas fa-barcode"></i>
                            {{ $inbound_shipment->shipment_number }}
                        </a>
                    </td>

                    {{-- Supplier --}}
                    <td title="{{ optional($inbound_shipment->supplier)->name }}">
                        @if($inbound_shipment->supplier)
                            <a href="{{ route('dashboard.suppliers.show', $inbound_shipment->supplier) }}"
                               class="text-decoration-none">
                                <i class="fas fa-truck text-primary"></i>
                                {{ $inbound_shipment->supplier->name }}
                            </a>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-minus-circle"></i>
                            </span>
                        @endif
                    </td>

                    {{-- Inbound Type --}}
                    <td>
                        @if($inbound_shipment->inbound_type === 'ready_package')
                            <span class="badge badge-success badge-pill">
                                <i class="fas fa-boxes"></i>
                                @lang('inbound_shipments.types.ready_package')
                            </span>
                        @else
                            <span class="badge badge-info badge-pill">
                                <i class="fas fa-box"></i>
                                @lang('inbound_shipments.types.single_item')
                            </span>
                        @endif
                    </td>

                    {{-- Items Count --}}
                    <td>
                        @php
                            $itemsCount = 0;
                            if($inbound_shipment->inbound_type === 'ready_package') {
                                $itemsCount = $inbound_shipment->readyPackages()->count();
                            } else {
                                $itemsCount = $inbound_shipment->items()->count();
                            }
                        @endphp
                        <span class="badge badge-secondary badge-pill">
                            @if($inbound_shipment->inbound_type === 'ready_package')
                                <i class="fas fa-boxes"></i>
                                {{ $itemsCount }} @lang('inbound_shipments.labels.packages')
                            @else
                                <i class="fas fa-box"></i>
                                {{ $itemsCount }} @lang('inbound_shipments.labels.items')
                            @endif
                        </span>
                    </td>

                    {{-- Created At --}}
                    <td>
                        <span class="text-muted">
                            <i class="fas fa-calendar"></i>
                            {{ $inbound_shipment->created_at->format('Y-m-d') }}
                        </span>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i>
                            {{ $inbound_shipment->created_at->diffForHumans() }}
                        </small>
                    </td>

                    {{-- Actions Dropdown --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="dropdownMenuButton{{ $inbound_shipment->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton{{ $inbound_shipment->id }}">

                                {{-- عرض التفاصيل --}}
                                <a href="{{ route('dashboard.inbound_shipments.show', $inbound_shipment) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-eye text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('inbound_shipments.actions.show')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- تعديل --}}
                                <a href="{{ route('dashboard.inbound_shipments.edit', $inbound_shipment) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-edit text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('inbound_shipments.actions.edit')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- حذف --}}
                                <form action="{{ route('dashboard.inbound_shipments.destroy', $inbound_shipment) }}"
                                      method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('@lang('inbound_shipments.dialogs.delete')')">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('inbound_shipments.actions.delete')</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-truck-loading fa-3x mb-3 d-block"></i>
                            <h5>@lang('inbound_shipments.empty')</h5>
                            <p class="mb-0">@lang('inbound_shipments.empty_hint')</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($inbound_shipments->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        @lang('inbound_shipments.pagination_results', [
                            'from' => $inbound_shipments->firstItem() ?? 0,
                            'to' => $inbound_shipments->lastItem() ?? 0,
                            'total' => $inbound_shipments->total()
                        ])
                    </div>
                    {{ $inbound_shipments->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
