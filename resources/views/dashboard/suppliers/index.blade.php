<x-layout :title="trans('suppliers.plural')" :breadcrumbs="['dashboard.suppliers.index']">
    @include('dashboard.suppliers.partials.filter')

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

            /* Image styling */
            .supplier-logo {
                width: 45px;
                height: 45px;
                object-fit: cover;
                border: 2px solid #ddd;
            }
        </style>
    @endpush

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-list"></i> @lang('suppliers.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($suppliers->total()) }}</span>
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
                                    type="{{ \App\Models\Supplier::class }}"
                                    :resource="trans('suppliers.plural')">
                                </x-check-all-delete>
                            </div>
                        </div>

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                                @include('dashboard.suppliers.partials.actions.create')
                            </div>
                            <div>
                                @include('dashboard.suppliers.partials.actions.trashed')
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
                <th style="width: 25%">
                    <i class="fas fa-tag"></i> @lang('suppliers.attributes.name')
                </th>
                <th style="width: 15%">
                    <i class="fas fa-layer-group"></i> @lang('suppliers.attributes.type')
                </th>
                <th style="width: 35%">
                    <i class="fas fa-align-right"></i> @lang('suppliers.attributes.description')
                </th>
                <th class="text-center" style="width: 80px">
                    <i class="fas fa-cog"></i> @lang('suppliers.actions.actions')
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($suppliers as $supplier)
                <tr class="align-middle">
                    {{-- Checkbox --}}
                    <td>
                        <x-check-all-item :model="$supplier"></x-check-all-item>
                    </td>

                    {{-- Name with Logo --}}
                    <td title="{{ $supplier->name }}">
                        <div class="d-flex align-items-center">
                            <img src="{{ $supplier->getFirstMediaUrl() ?: asset('images/default-supplier.png') }}"
                                 alt="{{ $supplier->name }}"
                                 class="img-circle supplier-logo {{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}"
                                 onerror="this.src='{{ asset('images/default-supplier.png') }}'">
                            <a href="{{ route('dashboard.suppliers.show', $supplier) }}"
                               class="text-decoration-none font-weight-bold text-primary">
                                {{ $supplier->name }}
                            </a>
                        </div>
                    </td>

                    {{-- Type --}}
                    <td>
                        @if($supplier->type == 'donor')
                            <span class="badge badge-info badge-pill">
                                <i class="fas fa-hand-holding-heart"></i>
                                @lang('suppliers.types.donor')
                            </span>
                        @elseif($supplier->type == 'operator')
                            <span class="badge badge-success badge-pill">
                                <i class="fas fa-cogs"></i>
                                @lang('suppliers.types.operator')
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Description --}}
                    <td title="{{ $supplier->description }}">
                        @if($supplier->description)
                            <small class="text-muted">
                                {{ $supplier->description }}
                            </small>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-minus-circle"></i>
                            </span>
                        @endif
                    </td>

                    {{-- Actions Dropdown --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="dropdownMenuButton{{ $supplier->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton{{ $supplier->id }}">

                                {{-- عرض التفاصيل --}}
                                <a href="{{ route('dashboard.suppliers.show', $supplier) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-eye text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('suppliers.actions.show')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- تعديل --}}
                                <a href="{{ route('dashboard.suppliers.edit', $supplier) }}"
                                   class="dropdown-item">
                                    <i class="fas fa-edit text-primary"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('suppliers.actions.edit')</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- حذف --}}
                                <form action="{{ route('dashboard.suppliers.destroy', $supplier) }}"
                                      method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('@lang('suppliers.dialogs.delete')')">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('suppliers.actions.delete')</span>
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
                            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                            <h5>@lang('suppliers.empty')</h5>
                            <p class="mb-0">@lang('suppliers.empty_hint')</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($suppliers->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        @lang('suppliers.pagination_results', [
                            'from' => $suppliers->firstItem() ?? 0,
                            'to' => $suppliers->lastItem() ?? 0,
                            'total' => $suppliers->total()
                        ])
                    </div>
                    {{ $suppliers->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
