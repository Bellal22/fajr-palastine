<x-layout :title="trans('locations.plural')" :breadcrumbs="['dashboard.locations.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.locations.partials.filter')
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #locations-map {
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
                border-border: 1px solid #eee;
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
            <i class="fas fa-map-pin"></i> @lang('locations.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($locations->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                            <x-check-all-delete
                                type="{{ \App\Models\Location::class }}"
                                :resource="trans('locations.plural')">
                            </x-check-all-delete>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="{{ app()->isLocale('ar') ? 'ml-2' : 'mr-2' }}">
                            @include('dashboard.locations.partials.actions.create')
                        </div>

                        <div>
                            @include('dashboard.locations.partials.actions.trashed')
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
            <th style="width: 12%"><i class="fas fa-map-pin"></i> @lang('locations.attributes.name')</th>
            <th style="width: 10%"><i class="fas fa-shapes"></i> @lang('locations.attributes.type')</th>
            <th style="width: 13%"><i class="fas fa-map-marked-alt"></i> @lang('locations.attributes.region')</th>
            <th style="width: 13%"><i class="fas fa-user-tie"></i> @lang('locations.attributes.area_responsible')</th>
            <th style="width: 13%"><i class="fas fa-walking"></i> @lang('locations.attributes.block')</th>
            <th style="width: 15%"><i class="fas fa-location-arrow"></i> @lang('locations.attributes.address')</th>
            <th class="text-center" style="width: 80px"><i class="fas fa-cog"></i> @lang('locations.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($locations as $location)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$location"></x-check-all-item>
                </td>

                {{-- Name --}}
                <td title="{{ $location->name }}">
                    <a href="{{ route('dashboard.locations.show', $location) }}"
                       class="text-decoration-none font-weight-bold">
                        <i class="fas fa-map-pin" style="color: {{ $location->icon_color }};"></i>
                        {{ $location->name }}
                    </a>
                </td>

                {{-- Type --}}
                <td>
                    @php
                        $typeIcons = [
                            'house' => 'fa-home',
                            'shelter' => 'fa-warehouse',
                            'center' => 'fa-building',
                            'other' => 'fa-map-marker-alt'
                        ];
                        $typeColors = [
                            'house' => 'primary',
                            'shelter' => 'info',
                            'center' => 'success',
                            'other' => 'danger'
                        ];
                    @endphp
                    <span class="badge badge-{{ $typeColors[$location->type] ?? 'secondary' }} badge-pill">
                        <i class="fas {{ $typeIcons[$location->type] ?? 'fa-map-marker-alt' }}"></i>
                        @lang('locations.types.' . $location->type)
                    </span>
                </td>

                {{-- Region --}}
                <td title="{{ optional($location->region)->name }}">
                    @if($location->region)
                        <a href="{{ route('dashboard.regions.show', $location->region) }}"
                           class="text-decoration-none">
                            <i class="fas fa-map-marked-alt" style="color: {{ $location->region->color ?? '#3388ff' }};"></i>
                            {{ $location->region->name }}
                        </a>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-minus-circle"></i>
                        </span>
                    @endif
                </td>

                {{-- Area Responsible --}}
                <td title="{{ optional($location->region->areaResponsible ?? null)->name }}">
                    @if($location->region && $location->region->areaResponsible)
                        <a href="{{ route('dashboard.area_responsibles.show', $location->region->areaResponsible) }}"
                           class="text-decoration-none">
                            <i class="fas fa-user-tie text-success"></i>
                            {{ $location->region->areaResponsible->name }}
                        </a>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-minus-circle"></i>
                        </span>
                    @endif
                </td>

                {{-- Block --}}
                <td>
                    @if($location->block)
                        @php
                            $blockName = $location->block->name ?? $location->block->title ?? '';
                            if (is_array($blockName)) {
                                $blockName = implode(', ', $blockName);
                            }
                        @endphp
                        <a href="{{ route('dashboard.blocks.show', $location->block) }}"
                           class="text-decoration-none"
                           title="{{ $blockName }}">
                            <i class="fas fa-walking text-info"></i>
                            {{ $blockName }}
                        </a>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-minus-circle"></i>
                        </span>
                    @endif
                </td>

                {{-- Address --}}
                <td title="{{ $location->address }}">
                    @if($location->address)
                        <small class="text-muted">
                            <i class="fas fa-location-arrow"></i>
                            {{ $location->address }}
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
                                id="dropdownMenuButton{{ $location->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'left' : 'right' }}"
                            aria-labelledby="dropdownMenuButton{{ $location->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.locations.show', $location) }}"
                               class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('locations.actions.show')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- تعديل --}}
                            <a href="{{ route('dashboard.locations.edit', $location) }}"
                               class="dropdown-item">
                                <i class="fas fa-edit text-primary"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('locations.actions.edit')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- حذف --}}
                            <form action="{{ route('dashboard.locations.destroy', $location) }}"
                                  method="POST"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="dropdown-item"
                                        onclick="return confirm('@lang('locations.dialogs.delete')')">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('locations.actions.delete')</span>
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
                        <i class="fas fa-map-pin fa-3x mb-3 d-block"></i>
                        <h5>@lang('locations.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($locations->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('locations.pagination_info', [
                            'from' => $locations->firstItem() ?? 0,
                            'to' => $locations->lastItem() ?? 0,
                            'total' => $locations->total()
                        ])
                    </div>
                    {{ $locations->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent

    {{-- خريطة كل اللوكيشنات --}}
    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-map-marked-alt"></i>
                @lang('locations.sections.all_locations_map')
            </h5>
        </div>
        <div class="card-body p-0">
            <div id="locations-map"></div>
        </div>
    </div>

    @php
        $locationsForMap = $locations->map(function ($l) {
            $blockName = $l->block->name ?? $l->block->title ?? '';
            if (is_array($blockName)) {
                $blockName = implode(', ', $blockName);
            }

            return [
                'id'           => $l->id,
                'name'         => $l->name,
                'description'  => $l->description,
                'color'        => $l->icon_color ?? '#9C27B0',
                'address'      => $l->address,
                'phone'        => $l->phone,
                'coordinates'  => [
                    'lat' => (float) $l->latitude,
                    'lng' => (float) $l->longitude,
                ],
                'region_name'  => optional($l->region)->name,
                'region_color' => optional($l->region)->color ?? '#3388ff',
                'boundaries'   => optional($l->region)->boundaries ?? [],
                'block_name'   => $blockName,
            ];
        })->values();

        $isRTL = app()->isLocale('ar');
        $iconMargin = $isRTL ? 'margin-right' : 'margin-left';
    @endphp

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const locations = @json($locationsForMap);
                const isRTL = @json($isRTL);
                const iconMargin = @json($iconMargin);

                const defaultLatLng = [31.3461, 34.3064];
                const map = L.map('locations-map').setView(defaultLatLng, 12);

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
                const regionsDrawn = new Set();

                locations.forEach(function (location) {
                    // رسم حدود المنطقة مرة واحدة
                    if (location.region_name && location.boundaries && location.boundaries.length && !regionsDrawn.has(location.region_name)) {
                        const latlngs = location.boundaries.map(function (p) {
                            return [parseFloat(p.lat), parseFloat(p.lng)];
                        });

                        const polygon = L.polygon(latlngs, {
                            color: location.region_color,
                            fillColor: location.region_color,
                            fillOpacity: 0.15,
                            weight: 3
                        }).addTo(group);

                        polygon.bindPopup(`
                            <div style="min-width: 180px; font-size: 13px; direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <strong style="color: ${location.region_color};">
                                    <i class="fas fa-map-marked-alt" style="${iconMargin}: 5px;"></i>
                                    ${location.region_name}
                                </strong>
                            </div>
                        `);

                        regionsDrawn.add(location.region_name);
                    }

                    // دبوس اللوكيشن
                    const pinIcon = L.icon({
                        iconUrl: "{{ asset('icons/person-marker.png') }}",
                        iconSize: [40, 40],
                        iconAnchor: [20, 40],
                        popupAnchor: [0, -40],
                        shadowSize: [41, 41]
                    });

                    const marker = L.marker(
                        [location.coordinates.lat, location.coordinates.lng],
                        { icon: pinIcon }
                    ).addTo(group);

                    let popupHtml = `
                        <h6 style="color: ${location.color}; margin-bottom: 10px; direction: ${isRTL ? 'rtl' : 'ltr'};">
                            <i class="fas fa-map-pin" style="${iconMargin}: 5px;"></i> ${location.name}
                        </h6>
                    `;

                    if (location.region_name) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-label">
                                    <i class="fas fa-map-marked-alt" style="color: ${location.region_color}; ${iconMargin}: 5px;"></i>
                                    @lang('locations.attributes.region')
                                </span>
                                <span class="popup-value">${location.region_name}</span>
                            </div>
                        `;
                    }

                    if (location.block_name) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-label">
                                    <i class="fas fa-map-pin text-primary" style="${iconMargin}: 5px;"></i>
                                    @lang('locations.attributes.block')
                                </span>
                                <span class="popup-value">${location.block_name}</span>
                            </div>
                        `;
                    }

                    if (location.address) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-label">
                                    <i class="fas fa-map-pin text-danger" style="${iconMargin}: 5px;"></i>
                                    @lang('locations.attributes.address')
                                </span>
                                <span class="popup-value">${location.address}</span>
                            </div>
                        `;
                    }

                    if (location.phone) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-label">
                                    <i class="fas fa-phone text-success" style="${iconMargin}: 5px;"></i>
                                    @lang('locations.attributes.phone')
                                </span>
                                <span class="popup-value">${location.phone}</span>
                            </div>
                        `;
                    }

                    if (location.description) {
                        popupHtml += `
                            <div class="popup-info-row" style="direction: ${isRTL ? 'rtl' : 'ltr'};">
                                <span class="popup-value text-muted">${location.description}</span>
                            </div>
                        `;
                    }

                    popupHtml += `
                        <a href="/dashboard/locations/${location.id}" class="popup-btn" style="background-color: ${location.color}; direction: ${isRTL ? 'rtl' : 'ltr'};">
                            <i class="fas fa-eye" style="${iconMargin}: 5px;"></i>
                            @lang('locations.actions.show')
                        </a>
                    `;

                    marker.bindPopup(popupHtml, {
                        maxWidth: 300
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
