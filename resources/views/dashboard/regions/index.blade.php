<x-layout :title="trans('regions.plural')" :breadcrumbs="['dashboard.regions.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.regions.partials.filter')
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #regions-map {
                height: 500px;
                width: 100%;
            }

            .leaflet-popup-content-wrapper {
                border-radius: 8px;
                box-shadow: 0 3px 14px rgba(0,0,0,0.4);
            }

            .leaflet-popup-content {
                margin: 13px 19px;
                line-height: 1.4;
            }

            .popup-info-row {
                margin-bottom: 8px;
                padding-bottom: 8px;
                border-bottom: 1px solid #eee;
            }

            .popup-info-row:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .popup-label {
                font-weight: 600;
                color: #666;
                font-size: 13px;
                display: block;
                margin-bottom: 3px;
            }

            .popup-value {
                color: #333;
                font-size: 13px;
            }

            .popup-btn {
                display: block;
                width: 100%;
                padding: 8px 15px;
                color: white !important;
                text-align: center;
                text-decoration: none !important;
                border-radius: 5px;
                font-size: 13px;
                font-weight: 600;
                margin-top: 12px;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            }

            .popup-btn:hover {
                color: white !important;
                text-decoration: none !important;
            }

            .popup-btn i {
                color: white !important;
            }

            /* منع السكرول الأفقي نهائياً */
            html, body {
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }

            .container, .container-fluid {
                overflow-x: hidden !important;
            }

            /* Table fixed layout */
            .table-box table {
                table-layout: fixed;
                width: 100%;
            }

            .table-box {
                overflow-x: hidden !important;
            }

            .table-responsive {
                overflow-x: hidden !important;
                overflow-y: visible !important;
            }

            /* Prevent dropdown from affecting table layout */
            .table-box tbody tr {
                position: relative;
            }

            .table-box .dropdown {
                position: static !important;
            }

            .table-box .dropdown-menu {
                position: fixed !important;
                z-index: 9999 !important;
                will-change: auto !important;
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

            /* إخفاء أي محتوى يطلع برة الصفحة */
            .card {
                overflow: hidden !important;
            }

            .card-body {
                overflow-x: hidden !important;
            }
        </style>
    @endpush

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-map-marked-alt"></i> @lang('regions.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($regions->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                            <x-check-all-delete
                                type="{{ \App\Models\Region::class }}"
                                :resource="trans('regions.plural')">
                            </x-check-all-delete>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                            @include('dashboard.regions.partials.actions.create')
                        </div>

                        <div>
                            @include('dashboard.regions.partials.actions.trashed')
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
            <th style="width: 30%"><i class="fas fa-map-marked-alt"></i> @lang('regions.attributes.name')</th>
            <th style="width: 35%" class="d-none d-lg-table-cell"><i class="fas fa-user-tie"></i> @lang('regions.attributes.area_responsible')</th>
            <th style="width: 20%" class="text-center"><i class="fas fa-power-off"></i> @lang('regions.attributes.status')</th>
            <th class="text-center" style="width: 80px"><i class="fas fa-cog"></i> @lang('regions.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($regions as $region)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$region"></x-check-all-item>
                </td>

                {{-- Name --}}
                <td title="{{ $region->name }}">
                    <a href="{{ route('dashboard.regions.show', $region) }}"
                       class="text-decoration-none font-weight-bold">
                        <i class="fas fa-map-marked-alt" style="color: {{ $region->color }};"></i>
                        {{ $region->name }}
                    </a>
                </td>

                {{-- Area Responsible --}}
                <td class="d-none d-lg-table-cell" title="{{ optional($region->areaResponsible)->name }}">
                    @if($region->areaResponsible)
                        <a href="{{ route('dashboard.area_responsibles.show', $region->areaResponsible) }}"
                           class="text-decoration-none">
                            <i class="fas fa-user-tie text-success"></i>
                            {{ $region->areaResponsible->name }}
                        </a>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-minus-circle"></i>
                        </span>
                    @endif
                </td>

                {{-- Status --}}
                <td class="text-center">
                    @if($region->is_active)
                        <span class="badge badge-success badge-pill">
                            <i class="fas fa-check-circle"></i>
                            @lang('regions.status.active')
                        </span>
                    @else
                        <span class="badge badge-danger badge-pill">
                            <i class="fas fa-times-circle"></i>
                            @lang('regions.status.inactive')
                        </span>
                    @endif
                </td>

                {{-- Actions Dropdown --}}
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton{{ $region->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                            aria-labelledby="dropdownMenuButton{{ $region->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.regions.show', $region) }}"
                               class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('regions.actions.show')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- تعديل --}}
                            <a href="{{ route('dashboard.regions.edit', $region) }}"
                               class="dropdown-item">
                                <i class="fas fa-edit text-primary"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('regions.actions.edit')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- حذف --}}
                            <form action="{{ route('dashboard.regions.destroy', $region) }}"
                                  method="POST"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="dropdown-item"
                                        onclick="return confirm('@lang('regions.dialogs.delete.info')')">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('regions.actions.delete')</span>
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
                        <i class="fas fa-map-marked-alt fa-3x mb-3 d-block"></i>
                        <h5>@lang('regions.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($regions->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('regions.pagination_info', [
                            'from' => $regions->firstItem() ?? 0,
                            'to' => $regions->lastItem() ?? 0,
                            'total' => $regions->total()
                        ])
                    </div>
                    {{ $regions->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent

    {{-- خريطة كل المناطق --}}
    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-map-marked-alt"></i>
                @lang('regions.sections.all_regions_map')
            </h5>
        </div>
        <div class="card-body p-0">
            <div id="regions-map"></div>
        </div>
    </div>

    @php
        $regionsForMap = $regions->map(function ($r) {
            return [
                'id'           => $r->id,
                'name'         => $r->name,
                'color'        => $r->color ?? '#FF0000',
                'boundaries'   => $r->boundaries ?? [],
                'is_active'    => $r->is_active,
                'responsible'  => optional($r->areaResponsible)->name,
            ];
        })->values();

        $isRTL = app()->isLocale('ar');
        $iconMargin = $isRTL ? 'margin-right' : 'margin-left';
    @endphp

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const regions = @json($regionsForMap);
                const isRTL = @json($isRTL);
                const iconMargin = @json($iconMargin);

                const defaultLatLng = [31.3461, 34.3064];
                const map = L.map('regions-map').setView(defaultLatLng, 12);

                const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                });

                const googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });

                const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });

                const baseLayers = {
                    "OpenStreetMap": osm,
                    "Google Satellite": googleSat,
                    "Google Hybrid": googleHybrid
                };

                osm.addTo(map);
                L.control.layers(baseLayers).addTo(map);

                const group = L.featureGroup().addTo(map);

                regions.forEach(function (region) {
                    if (!region.boundaries || !region.boundaries.length) {
                        return;
                    }

                    const latlngs = region.boundaries.map(function (p) {
                        return [parseFloat(p.lat), parseFloat(p.lng)];
                    });

                    const polygon = L.polygon(latlngs, {
                        color: region.color,
                        fillColor: region.color,
                        fillOpacity: 0.3,
                        weight: 3
                    }).addTo(group);

                    let popupHtml = `
                        <h6 style="color: ${region.color}; margin-bottom: 10px; direction: ${isRTL ? 'rtl' : 'ltr'};">
                            <i class="fas fa-map-marked-alt" style="${iconMargin}: 5px;"></i> ${region.name}
                        </h6>
                    `;

                    if (region.responsible) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-label">
                                    <i class="fas fa-user-tie text-success" style="${iconMargin}: 5px;"></i>
                                    @lang('regions.attributes.area_responsible')
                                </span>
                                <span class="popup-value">${region.responsible}</span>
                            </div>
                        `;
                    }

                    const statusBadge = region.is_active
                        ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> @lang("regions.status.active")</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> @lang("regions.status.inactive")</span>';

                    popupHtml += `
                        <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                            <span class="popup-label">
                                <i class="fas fa-power-off text-warning" style="${iconMargin}: 5px;"></i>
                                @lang('regions.attributes.status')
                            </span>
                            <div class="popup-value">${statusBadge}</div>
                        </div>
                        <a href="/dashboard/regions/${region.id}" class="popup-btn" style="background-color: ${region.color}; direction: ${isRTL ? 'rtl' : 'ltr'};">
                            <i class="fas fa-eye" style="${iconMargin}: 5px;"></i>
                            @lang('regions.actions.show')
                        </a>
                    `;

                    polygon.bindPopup(popupHtml, {
                        maxWidth: 280
                    });
                });

                if (group.getLayers().length) {
                    map.fitBounds(group.getBounds(), {
                        padding: [20, 20]
                    });
                }
            });
        </script>
    @endpush
</x-layout>
