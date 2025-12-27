<x-layout :title="$location->name" :breadcrumbs="['dashboard.locations.show', $location]">
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                {{-- بيانات اللوكيشن --}}
                <div class="p-3 pb-0">
                    <h4 class="mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                        {{ $location->name }}
                    </h4>

                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                        <tr>
                            <th width="200">@lang('locations.attributes.name')</th>
                            <td>{{ $location->name }}</td>
                        </tr>

                        @if($location->description)
                            <tr>
                                <th>@lang('locations.attributes.description')</th>
                                <td>{{ $location->description }}</td>
                            </tr>
                        @endif

                        <tr>
                            <th>@lang('regions.singular')</th>
                            <td>{{ optional($location->region)->name }}</td>
                        </tr>

                        <tr>
                            <th>@lang('blocks.singular')</th>
                            <td>{{ optional($location->block)->name ?? optional($location->block)->title }}</td>
                        </tr>

                        @if($location->address)
                            <tr>
                                <th>@lang('locations.attributes.address')</th>
                                <td>{{ $location->address }}</td>
                            </tr>
                        @endif

                        @if($location->phone)
                            <tr>
                                <th>@lang('locations.attributes.phone')</th>
                                <td>{{ $location->phone }}</td>
                            </tr>
                        @endif

                        <tr>
                            <th>@lang('locations.attributes.type')</th>
                            <td>{{ $location->type }}</td>
                        </tr>

                        <tr>
                            <th>الإحداثيات</th>
                            <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                {{-- خريطة أنيقة تحت البيانات --}}
                <div class="p-3 pt-0">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-map mr-2 text-primary"></i>
                            خريطة اللوكيشن داخل منطقته
                        </h5>
                        <span class="badge badge-pill"
                              style="background-color: {{ $location->icon_color }}20; color: {{ $location->icon_color }}">
                            {{ optional($location->region)->name }}
                        </span>
                    </div>

                    <div id="locationMap"
                         style="height: 550px; width: 100%; border-radius: 10px; border: 2px solid #e1e5ea;"></div>
                </div>

                @slot('footer')
                    @include('dashboard.locations.partials.actions.edit')
                    @include('dashboard.locations.partials.actions.delete')
                    @include('dashboard.locations.partials.actions.restore')
                    @include('dashboard.locations.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #locationMap .leaflet-popup-content-wrapper {
                border-radius: 10px;
                box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            }
            #locationMap .leaflet-popup-content {
                margin: 10px 14px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const locationData = {
                    name: @json($location->name),
                    description: @json($location->description),
                    lat: {{ (float) $location->latitude }},
                    lng: {{ (float) $location->longitude }},
                    color: @json($location->icon_color ?? '#9C27B0'),
                    address: @json($location->address),
                    phone: @json($location->phone),
                    region_name: @json(optional($location->region)->name),
                    region_color: @json(optional($location->region)->color ?? '#3388ff'),
                    boundaries: @json(optional($location->region)->boundaries ?? []),
                    block_name: @json(optional($location->block)->name ?? optional($location->block)->title),
                };

                const map = L.map('locationMap').setView([locationData.lat, locationData.lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const bounds = [];

                // رسم مضلع المنطقة حول اللوكيشن
                if (locationData.boundaries && locationData.boundaries.length) {
                    const latlngs = locationData.boundaries.map(function (p) {
                        return [parseFloat(p.lat), parseFloat(p.lng)];
                    });

                    const polygon = L.polygon(latlngs, {
                        color: locationData.region_color,
                        weight: 3,
                        fillColor: locationData.region_color,
                        fillOpacity: 0.18
                    }).addTo(map);

                    polygon.bindPopup(`
                        <div style="min-width: 180px; font-size: 13px;">
                            <strong>${locationData.region_name || 'منطقة غير معرّفة'}</strong>
                        </div>
                    `);

                    bounds.push(...latlngs);
                }

                // دبوس اللوكيشن داخل المنطقة
                const iconColor = locationData.color || '#9C27B0';

                const pinIcon = L.icon({
                    iconUrl: "{{ asset('icons/person-marker.png') }}",
                    iconSize:     [40, 40],
                    iconAnchor:   [12, 41],
                    popupAnchor:  [1, -34],
                });

                const marker = L.marker([locationData.lat, locationData.lng], { icon: pinIcon }).addTo(map);

                marker.on('add', () => {
                    if (marker._icon) {
                        marker._icon.style.filter =
                            `drop-shadow(0 0 0 ${iconColor}) drop-shadow(0 0 6px ${iconColor})`;
                    }
                });

                const popupHtml = `
                    <div style="min-width: 230px; font-size: 13px;">
                        <h6 style="color: ${iconColor}; margin-bottom: 6px;">
                            <i class="fas fa-map-marker-alt mr-1"></i> ${locationData.name}
                        </h6>
                        ${locationData.region_name ? `<div class="small mb-1"><strong>المنطقة:</strong> ${locationData.region_name}</div>` : ''}
                        ${locationData.block_name ? `<div class="small mb-1"><strong>البلوك / المندوب:</strong> ${locationData.block_name}</div>` : ''}
                        ${locationData.address ? `<div class="small mb-1"><i class="fas fa-map-pin mr-1"></i>${locationData.address}</div>` : ''}
                        ${locationData.phone ? `<div class="small mb-1"><i class="fas fa-phone mr-1"></i>${locationData.phone}</div>` : ''}
                        ${locationData.description ? `<div class="small text-muted mt-1">${locationData.description}</div>` : ''}
                        <div class="small text-muted mt-1">
                            <i class="fas fa-crosshairs mr-1"></i>
                            ${locationData.lat.toFixed(6)}, ${locationData.lng.toFixed(6)}
                        </div>
                    </div>
                `;
                marker.bindPopup(popupHtml).openPopup();

                bounds.push([locationData.lat, locationData.lng]);

                if (bounds.length > 0) {
                    map.fitBounds(bounds, {
                        padding: [20, 20],
                        maxZoom: 17
                    });
                } else {
                    map.setView([locationData.lat, locationData.lng], 15);
                }
            });
        </script>
    @endpush
</x-layout>
