<x-layout :title="trans('items.plural')" :breadcrumbs="['dashboard.items.index']">
    @include('dashboard.items.partials.filter')

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
            <i class="fas fa-cubes"></i> @lang('items.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($items->total()) }}</span>
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
                                    type="{{ \App\Models\Item::class }}"
                                    :resource="trans('items.plural')">
                                </x-check-all-delete>
                            </div>
                        </div>

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                                @include('dashboard.items.partials.actions.create')
                            </div>
                            <div>
                                @include('dashboard.items.partials.actions.trashed')
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
                    <i class="fas fa-tag"></i> @lang('items.attributes.name')
                </th>
                <th style="width: 15%">
                    <i class="fas fa-truck-loading"></i> @lang('items.attributes.inbound_shipment_id')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-sort-numeric-up"></i> @lang('items.attributes.quantity')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-weight"></i> @lang('items.attributes.weight')
                </th>
                <th style="width: 10%" class="text-center">
                    <i class="fas fa-weight-hanging"></i> @lang('items.table.total_weight')
                </th>
                <th class="text-center" style="width: 80px">
                    <i class="fas fa-cog"></i> @lang('items.actions.actions')
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($items as $item)
                @php
                    $totalWeight = $item->quantity * ($item->weight ?? 0);
                @endphp
                <tr class="align-middle">
                    {{-- Checkbox --}}
                    <td>
                        <x-check-all-item :model="$item"></x-check-all-item>
                    </td>

                    {{-- Name --}}
                    <td title="{{ $item->name }}">
                        <a href="{{ route('dashboard.items.show', $item) }}"
                           class="text-decoration-none font-weight-bold text-primary">
                            <i class="fas fa-box"></i>
                            {{ $item->name }}
                        </a>
                        @if($item->package)
                            <span class="badge badge-success badge-sm">
                                <i class="fas fa-boxes"></i>
                            </span>
                        @endif
                    </td>

                    {{-- Inbound Shipment --}}
                    <td>
                        @if($item->inboundShipment)
                            <a href="{{ route('dashboard.inbound_shipments.show', $item->inboundShipment) }}"
                               class="badge badge-info badge-pill">
                                <i class="fas fa-truck-loading"></i>
                                {{ $item->inboundShipment->shipment_number }}
                            </a>
                        @else
                            <span class="badge badge-secondary badge-pill">
                                <i class="fas fa-minus-circle"></i>
                                @lang('items.messages.no_shipment')
                            </span>
                        @endif
                    </td>

                    {{-- Quantity --}}
                    <td class="text-center">
                        <span class="badge badge-primary badge-pill">
                            {{ $item->quantity }}
                        </span>
                    </td>

                    {{-- Weight --}}
                    <td class="text-center">
                        @if($item->weight)
                            {{ number_format($item->weight, 2) }}
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
                                    id="dropdownMenuButton{{ $item->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton{{ $item->id }}">

                                {{-- عرض التفاصيل --}}
                                <a href="{{ route('dashboard.items.show', $item) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-eye text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('items.actions.show')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- تعديل --}}
                                <a href="{{ route('dashboard.items.edit', $item) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-edit text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('items.actions.edit')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- حذف --}}
                                <form action="{{ route('dashboard.items.destroy', $item) }}"
                                      method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('@lang('items.dialogs.delete')')">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('items.actions.delete')</span>
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
                            <h5>@lang('items.empty')</h5>
                            <p class="mb-0">@lang('items.empty_hint')</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($items->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        @lang('items.pagination_results', [
                            'from' => $items->firstItem() ?? 0,
                            'to' => $items->lastItem() ?? 0,
                            'total' => $items->total()
                        ])
                    </div>
                    {{ $items->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
