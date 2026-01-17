<x-layout :title="$region->name" :breadcrumbs="['dashboard.regions.show', $region]">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex p-4 shadow"
                             style="background-color: {{ $region->color }}20;">
                            <i class="fas fa-map-marked-alt fa-4x" style="color: {{ $region->color }};"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $region->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marked-alt"></i> @lang('regions.singular')
                    </p>
                    <div class="mb-3">
                        @if($region->is_active)
                            <span class="badge badge-success badge-pill px-3 py-2">
                                <i class="fas fa-check-circle"></i>
                                @lang('regions.status.active')
                            </span>
                        @else
                            <span class="badge badge-danger badge-pill px-3 py-2">
                                <i class="fas fa-times-circle"></i>
                                @lang('regions.status.inactive')
                            </span>
                        @endif
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-palette" style="color: {{ $region->color }};"></i>
                            <span class="ml-2">@lang('regions.attributes.color'):</span>
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $region->color }}; border: 2px solid #ddd; border-radius: 3px; vertical-align: middle; margin-right: 5px;"></span>
                            <code>{{ $region->color }}</code>
                        </p>
                        @if($region->areaResponsible)
                            <p class="mb-0">
                                <i class="fas fa-user-tie text-success"></i>
                                <span class="ml-2">{{ $region->areaResponsible->name }}</span>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer text-center">
                    @include('dashboard.regions.partials.actions.edit')
                    @include('dashboard.regions.partials.actions.delete')
                    @include('dashboard.regions.partials.actions.restore')
                    @include('dashboard.regions.partials.actions.forceDelete')
                </div>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('regions.sections.basic_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-map-marked-alt text-primary"></i>
                                    @lang('regions.attributes.name')
                                </th>
                                <td class="font-weight-bold">{{ $region->name }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-palette text-danger"></i>
                                    @lang('regions.attributes.color')
                                </th>
                                <td>
                                    <span style="display: inline-block; width: 30px; height: 30px; background-color: {{ $region->color }}; border: 2px solid #ddd; border-radius: 5px; vertical-align: middle;"></span>
                                    <code class="ml-2">{{ $region->color }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-tie text-success"></i>
                                    @lang('regions.attributes.area_responsible')
                                </th>
                                <td>
                                    @if($region->areaResponsible)
                                        <a href="{{ route('dashboard.area_responsibles.show', $region->areaResponsible) }}"
                                           class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $region->areaResponsible->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">@lang('regions.messages.no_responsible')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-power-off text-warning"></i>
                                    @lang('regions.attributes.status')
                                </th>
                                <td>
                                    @if($region->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            @lang('regions.status.active')
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i>
                                            @lang('regions.status.inactive')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if($region->description)
                                <tr>
                                    <th>
                                        <i class="fas fa-align-right text-secondary"></i>
                                        @lang('regions.attributes.description')
                                    </th>
                                    <td>{{ $region->description }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>
                                    <i class="fas fa-calendar-plus text-primary"></i>
                                    @lang('regions.attributes.created_at')
                                </th>
                                <td>{{ $region->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-calendar-check text-info"></i>
                                    @lang('regions.attributes.updated_at')
                                </th>
                                <td>{{ $region->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Card - Full Width --}}
    @if($region->boundaries && count($region->boundaries) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt"></i> @lang('regions.sections.map_view')
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="region-map" style="height: 500px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                const regionData = {
                    name: @json($region->name),
                    color: @json($region->color ?? '#FF0000'),
                    boundaries: @json($region->boundaries ?? []),
                    responsible: @json(optional($region->areaResponsible)->name),
                    is_active: {{ $region->is_active ? 'true' : 'false' }},
                };

                if (!regionData.boundaries || regionData.boundaries.length === 0) {
                    return;
                }

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

                const map = L.map('region-map', {
                    layers: [osm]
                }).setView([31.3461, 34.3064], 13);

                L.control.layers(baseLayers).addTo(map);

                const latlngs = regionData.boundaries.map(function (p) {
                    return [parseFloat(p.lat), parseFloat(p.lng)];
                });

                const polygon = L.polygon(latlngs, {
                    color: regionData.color,
                    weight: 3,
                    fillColor: regionData.color,
                    fillOpacity: 0.3
                }).addTo(map);

                // Build Simple Popup HTML
                let popupHtml = `
                    <h6 style="color: ${regionData.color};">
                        <i class="fas fa-map-marked-alt"></i> ${regionData.name}
                    </h6>
                `;

                if (regionData.responsible) {
                    popupHtml += `
                        <div class="popup-info-row">
                            <div class="popup-label">
                                <i class="fas fa-user-tie text-success"></i>
                                @lang('regions.attributes.area_responsible')
                            </div>
                            <div class="popup-value">${regionData.responsible}</div>
                        </div>
                    `;
                }

                const statusBadge = regionData.is_active
                    ? '<span class="badge badge-success badge-sm"><i class="fas fa-check-circle"></i> @lang("regions.status.active")</span>'
                    : '<span class="badge badge-danger badge-sm"><i class="fas fa-times-circle"></i> @lang("regions.status.inactive")</span>';

                popupHtml += `
                    <div class="popup-info-row">
                        <div class="popup-label">
                            <i class="fas fa-power-off text-warning"></i>
                            @lang('regions.attributes.status')
                        </div>
                        <div class="popup-value">${statusBadge}</div>
                    </div>
                `;

                polygon.bindPopup(popupHtml, {
                    maxWidth: 300
                }).openPopup();

                map.fitBounds(polygon.getBounds(), {
                    padding: [30, 30]
                });
            });
        </script>
    @endpush
</x-layout>
