<x-layout :title="$location->name" :breadcrumbs="['dashboard.locations.index', 'dashboard.locations.show']">

    <div class="row">
        {{-- Profile Card (Basic Info) --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex p-4 shadow"
                             style="background-color: {{ $location->icon_color }}20;">
                            <i class="fas fa-map-marker-alt fa-4x" style="color: {{ $location->icon_color }};"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $location->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt"></i> @lang('locations.singular')
                    </p>
                    <div class="mb-3">
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
                        <span class="badge badge-{{ $typeColors[$location->type] ?? 'secondary' }} badge-pill px-3 py-2">
                            <i class="fas {{ $typeIcons[$location->type] ?? 'fa-map-marker-alt' }}"></i>
                            @lang('locations.types.' . $location->type)
                        </span>
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-palette" style="color: {{ $location->icon_color }};"></i>
                            <span class="ml-2">@lang('locations.attributes.icon_color'):</span>
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $location->icon_color }}; border: 2px solid #ddd; border-radius: 3px; vertical-align: middle; margin-right: 5px;"></span>
                            <code>{{ $location->icon_color }}</code>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="ml-2">@lang('locations.attributes.created_at'):</span>
                            <span class="text-muted">{{ $location->created_at->format('Y-m-d H:i') }}</span>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-calendar-check text-info"></i>
                            <span class="ml-2">@lang('locations.attributes.updated_at'):</span>
                            <span class="text-muted">{{ $location->updated_at->format('Y-m-d H:i') }}</span>
                        </p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    @include('dashboard.locations.partials.actions.edit')
                    @include('dashboard.locations.partials.actions.delete')
                    @include('dashboard.locations.partials.actions.restore')
                    @include('dashboard.locations.partials.actions.forceDelete')
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-12">

            {{-- Contact Info Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-gray text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-address-card"></i> @lang('locations.sections.contact_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    @lang('locations.attributes.address')
                                </th>
                                <td>
                                    @if($location->address)
                                        {{ $location->address }}
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('locations.messages.no_address')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-phone text-secondary"></i>
                                    @lang('locations.attributes.phone')
                                </th>
                                <td>
                                    @if($location->phone)
                                        <a href="tel:{{ $location->phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone-square-alt"></i>
                                            {{ $location->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('locations.messages.no_phone')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if($location->description)
                                <tr>
                                    <th>
                                        <i class="fas fa-align-right text-secondary"></i>
                                        @lang('locations.attributes.description')
                                    </th>
                                    <td>{{ $location->description }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Region Info Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt"></i> @lang('locations.sections.region_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-map-marked-alt text-info"></i>
                                    @lang('locations.attributes.region')
                                </th>
                                <td>
                                    @if($location->region)
                                        <a href="{{ route('dashboard.regions.show', $location->region) }}"
                                           class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $location->region->name }}
                                        </a>
                                        @if($location->region->color)
                                            <span style="display: inline-block; width: 15px; height: 15px; background-color: {{ $location->region->color }}; border: 1px solid #ddd; border-radius: 3px; vertical-align: middle; margin-right: 5px;"></span>
                                        @endif
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('locations.messages.no_region')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-tie text-success"></i>
                                    @lang('locations.attributes.area_responsible')
                                </th>
                                <td>
                                    @if($location->region && $location->region->areaResponsible)
                                        <a href="{{ route('dashboard.area_responsibles.show', $location->region->areaResponsible) }}"
                                           class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $location->region->areaResponsible->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('locations.messages.no_responsible')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-walking text-warning"></i>
                                    @lang('locations.attributes.block')
                                </th>
                                <td>
                                    @if($location->block)
                                        <a href="{{ route('dashboard.blocks.show', $location->block) }}"
                                            class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $location->block->name ?? $location->block->title }}
                                        </a>
                                        @if($location->block->phone)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i>
                                                    <a href="tel:{{ $location->block->phone }}">{{ $location->block->phone }}</a>
                                                </small>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('locations.messages.no_block')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Coordinates Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-crosshairs"></i> @lang('locations.sections.coordinates')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-globe text-primary"></i>
                                    @lang('locations.attributes.latitude')
                                </th>
                                <td>
                                    <code>{{ number_format($location->latitude, 6) }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-globe text-danger"></i>
                                    @lang('locations.attributes.longitude')
                                </th>
                                <td>
                                    <code>{{ number_format($location->longitude, 6) }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-map-marked-alt text-info"></i>
                                    @lang('locations.attributes.google_maps')
                                </th>
                                <td>
                                    <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i>
                                        @lang('locations.actions.open_in_google_maps')
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Map Card - Full Width --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt"></i> @lang('locations.sections.map_view')
                    </h5>
                    <div class="badge badge-light">
                        <i class="fas fa-crosshairs"></i>
                        {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="locationMap" style="height: 500px; width: 100%;"></div>
                </div>
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                @lang('locations.map.legend_location'):
                                <span style="color: {{ $location->icon_color }};">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>{{ $location->name }}</strong>
                                </span>
                            </small>
                        </div>
                        @if($location->block)
                        <div class="col-md-6 text-md-right">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                @lang('locations.map.legend_block'):
                                <i class="fas fa-walking text-warning"></i>
                                <strong>{{ $location->block->name ?? $location->block->title }}</strong>
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            .leaflet-popup-content-wrapper {
                border-radius: 8px;
                box-shadow: 0 3px 14px rgba(0,0,0,0.4);
            }

            .leaflet-popup-content {
                margin: 13px 19px;
                line-height: 1.4;
            }

            .leaflet-popup-content h6 {
                margin: 0 0 10px 0;
                font-size: 16px;
                font-weight: bold;
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
            }

            .popup-value {
                color: #333;
                font-size: 13px;
                margin-top: 2px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const locationData = {
                    name: @json($location->name),
                    lat: {{ (float) $location->latitude }},
                    lng: {{ (float) $location->longitude }},
                    color: @json($location->icon_color ?? '#9C27B0'),
                    address: @json($location->address),
                    phone: @json($location->phone),
                    description: @json($location->description),
                    type: @json($location->type),
                    region_name: @json(optional($location->region)->name),
                    region_color: @json(optional($location->region)->color ?? '#3388ff'),
                    boundaries: @json(optional($location->region)->boundaries ?? []),
                    block_name: @json($location->block ? ($location->block->name ?? $location->block->title) : null),
                    block_lat: {{ $location->block ? (float)($location->block->lat ?? 0) : 'null' }},
                    block_lng: {{ $location->block ? (float)($location->block->lng ?? $location->block->lan ?? 0) : 'null' }},
                };

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

                const map = L.map('locationMap', {
                    layers: [osm]
                }).setView([locationData.lat, locationData.lng], 15);

                L.control.layers(baseLayers).addTo(map);

                const bounds = [];

                // رسم مضلع المنطقة
                if (locationData.boundaries && locationData.boundaries.length) {
                    const latlngs = locationData.boundaries.map(function (p) {
                        return [parseFloat(p.lat), parseFloat(p.lng)];
                    });

                    const polygon = L.polygon(latlngs, {
                        color: locationData.region_color,
                        weight: 3,
                        fillColor: locationData.region_color,
                        fillOpacity: 0.2
                    }).addTo(map);

                    polygon.bindPopup(`
                        <div style="min-width: 180px; font-size: 13px;">
                            <strong style="color: ${locationData.region_color};">
                                <i class="fas fa-walking"></i>
                                ${locationData.region_name || '@lang('locations.messages.no_region')'}
                            </strong>
                        </div>
                    `);

                    bounds.push(...latlngs);
                }

                // دبوس البلوك (المندوب)
                if (locationData.block_lat && locationData.block_lng) {
                    const blockMarker = L.marker([locationData.block_lat, locationData.block_lng], {
                        icon: L.icon({
                            iconUrl: "{{ asset('icons/person-marker.png') }}",
                            iconSize: [40, 40],
                            iconAnchor: [20, 40],
                            popupAnchor: [0, -40],
                            shadowSize: [41, 41]
                            })
                    }).addTo(map);

                    blockMarker.bindPopup(`
                        <div style="min-width: 150px;">
                            <h6 style="color: #856404;">
                                <i class="fas fa-walking"></i> ${locationData.block_name}
                            </h6>
                            <small class="text-muted">@lang('locations.attributes.block')</small>
                        </div>
                    `);

                    bounds.push([locationData.block_lat, locationData.block_lng]);
                }

                // دبوس اللوكيشن الرئيسي
                const locationMarker = L.marker([locationData.lat, locationData.lng], {
                    icon: L.icon({
                        iconUrl: "{{ asset('icons/person-marker.png') }}",
                        iconSize: [40, 40],
                        iconAnchor: [20, 40],
                        popupAnchor: [0, -40],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);

                // Build Detailed Popup HTML
                let popupHtml = `
                    <h6 style="color: ${locationData.color};">
                        <i class="fas fa-map-marker-alt"></i> ${locationData.name}
                    </h6>
                `;

                if (locationData.address) {
                    popupHtml += `
                        <div class="popup-info-row">
                            <div class="popup-label">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                @lang('locations.attributes.address')
                            </div>
                            <div class="popup-value">${locationData.address}</div>
                        </div>
                    `;
                }

                if (locationData.phone) {
                    popupHtml += `
                        <div class="popup-info-row">
                            <div class="popup-label">
                                <i class="fas fa-phone text-success"></i>
                                @lang('locations.attributes.phone')
                            </div>
                            <div class="popup-value">
                                <a href="tel:${locationData.phone}">${locationData.phone}</a>
                            </div>
                        </div>
                    `;
                }

                if (locationData.region_name) {
                    popupHtml += `
                        <div class="popup-info-row">
                            <div class="popup-label">
                                <i class="fas fa-map-marked-alt" style="color: ${locationData.region_color};"></i>
                                @lang('locations.attributes.region')
                            </div>
                            <div class="popup-value">${locationData.region_name}</div>
                        </div>
                    `;
                }

                if (locationData.description) {
                    popupHtml += `
                        <div class="popup-info-row">
                            <div class="popup-value text-muted">
                                <small>${locationData.description}</small>
                            </div>
                        </div>
                    `;
                }

                locationMarker.bindPopup(popupHtml, {
                    maxWidth: 300
                }).openPopup();

                bounds.push([locationData.lat, locationData.lng]);

                if (bounds.length > 0) {
                    map.fitBounds(bounds, {
                        padding: [30, 30],
                        maxZoom: 16
                    });
                } else {
                    map.setView([locationData.lat, locationData.lng], 15);
                }
            });
        </script>
    @endpush
</x-layout>
