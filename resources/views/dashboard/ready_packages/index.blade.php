<x-layout :title="trans('ready_packages.plural')" :breadcrumbs="['dashboard.ready_packages.index']">
    @include('dashboard.ready_packages.partials.filter')

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
            <i class="fas fa-box"></i> @lang('ready_packages.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($ready_packages->total()) }}</span>
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
                                    type="{{ \App\Models\ReadyPackage::class }}"
                                    :resource="trans('ready_packages.plural')">
                                </x-check-all-delete>
                            </div>
                        </div>

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                                @include('dashboard.ready_packages.partials.actions.create')
                            </div>
                            <div>
                                @include('dashboard.ready_packages.partials.actions.trashed')
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
                <th style="width: 20%">
                    <i class="fas fa-box"></i> @lang('ready_packages.attributes.name')
                </th>
                <th style="width: 15%">
                    <i class="fas fa-truck-loading"></i> @lang('ready_packages.attributes.inbound_shipment_id')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-sort-numeric-up"></i> @lang('ready_packages.attributes.quantity')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-weight"></i> @lang('ready_packages.attributes.weight')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-weight-hanging"></i> @lang('ready_packages.table.total_weight')
                </th>
                <th class="text-center" style="width: 80px">
                    <i class="fas fa-cog"></i> @lang('ready_packages.actions.actions')
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($ready_packages as $ready_package)
                @php
                    $totalWeight = $ready_package->quantity * ($ready_package->weight ?? 0);
                @endphp
                <tr class="align-middle">
                    {{-- Checkbox --}}
                    <td>
                        <x-check-all-item :model="$ready_package"></x-check-all-item>
                    </td>

                    {{-- Name --}}
                    <td title="{{ $ready_package->name }}">
                        <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}"
                           class="text-decoration-none font-weight-bold text-primary">
                            <i class="fas fa-box"></i>
                            {{ $ready_package->name }}
                        </a>
                    </td>

                    {{-- Inbound Shipment --}}
                    <td title="{{ optional($ready_package->inboundShipment)->shipment_number }}">
                        @if($ready_package->inboundShipment)
                            <a href="{{ route('dashboard.inbound_shipments.show', $ready_package->inboundShipment) }}"
                               class="badge badge-info badge-pill">
                                <i class="fas fa-truck-loading"></i>
                                {{ $ready_package->inboundShipment->shipment_number }}
                            </a>
                        @else
                            <span class="badge badge-secondary badge-pill">
                                <i class="fas fa-minus-circle"></i>
                            </span>
                        @endif
                    </td>

                    {{-- Quantity --}}
                    <td class="text-center">
                        <span class="badge badge-primary badge-pill">
                            {{ $ready_package->quantity }}
                        </span>
                    </td>

                    {{-- Weight --}}
                    <td class="text-center">
                        @if($ready_package->weight)
                            {{ number_format($ready_package->weight, 2) }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Total Weight --}}
                    <td class="text-center">
                        <strong class="text-success">{{ number_format($totalWeight, 2) }}</strong>
                    </td>

                    {{-- Actions Dropdown --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="dropdownMenuButton{{ $ready_package->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton{{ $ready_package->id }}">

                                {{-- عرض التفاصيل --}}
                                <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-eye text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('ready_packages.actions.show')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- تعديل --}}
                                <a href="{{ route('dashboard.ready_packages.edit', $ready_package) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-edit text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('ready_packages.actions.edit')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- حذف --}}
                                <form action="{{ route('dashboard.ready_packages.destroy', $ready_package) }}"
                                      method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('@lang('ready_packages.dialogs.delete')')">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('ready_packages.actions.delete')</span>
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
                            <i class="fas fa-boxes fa-3x mb-3 d-block"></i>
                            <h5>@lang('ready_packages.empty')</h5>
                            <p class="mb-0">@lang('ready_packages.empty_hint')</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($ready_packages->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        @lang('ready_packages.pagination_results', [
                            'from' => $ready_packages->firstItem() ?? 0,
                            'to' => $ready_packages->lastItem() ?? 0,
                            'total' => $ready_packages->total()
                        ])
                    </div>
                    {{ $ready_packages->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
