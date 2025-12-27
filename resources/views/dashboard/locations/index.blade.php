<x-layout :title="trans('locations.plural')" :breadcrumbs="['dashboard.locations.index']">
    @include('dashboard.locations.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('locations.actions.list') ({{ $locations->total() }})
        @endslot

        <thead>
        <tr>
         <th colspan="100">
          <div class="d-flex">
              <x-check-all-delete
                      type="{{ \App\Models\Location::class }}"
                      :resource="trans('locations.plural')"></x-check-all-delete>

              <div class="ml-2 d-flex justify-content-between flex-grow-1">
                  @include('dashboard.locations.partials.actions.create')
                  @include('dashboard.locations.partials.actions.trashed')
              </div>
          </div>
         </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('locations.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($locations as $location)
            <tr>
                <td class="text-center">
                 <x-check-all-item :model="$location"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.locations.show', $location) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $location->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.locations.partials.actions.show')
                    @include('dashboard.locations.partials.actions.edit')
                    @include('dashboard.locations.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('locations.empty')</td>
            </tr>
        @endforelse

        @if($locations->hasPages())
            @slot('footer')
                {{ $locations->links() }}
            @endslot
        @endif
    @endcomponent

    {{-- خريطة عرض اللوكيشنات + المناطق --}}
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .locations-map-section {
                background: white;
                padding: 25px;
                border-radius: 12px;
                margin-top: 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                border: 1px solid #e9ecef;
            }

            #locationsMap {
                height: 650px;
                width: 100%;
                border-radius: 8px;
                border: 3px solid #dee2e6;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .location-marker {
                background: transparent !important;
            }
        </style>
    @endpush

    <div class="locations-map-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-map-marked-alt mr-2 text-primary"></i>
                خريطة اللوكيشنات والمناطق
                <span class="badge badge-primary ml-2">{{ $locations->count() }} لوكيشن نشط</span>
            </h4>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary btn-sm" onclick="fitAllBounds()" title="تكبير على الكل">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleRegions()" title="إظهار/إخفاء المناطق" id="regionsToggleBtn">
                    <i class="fas fa-layer-group"></i>
                    <span id="regionsToggleText">إخفاء المناطق</span>
                </button>
            </div>
        </div>

        <div id="locationsMap"></div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // إعداد الخريطة
                const map = L.map('locationsMap').setView([31.3461, 34.3064], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap | Fajr Youth Organization',
                    maxZoom: 19
                }).addTo(map);

                let allBounds = [];
                let regionPolygons = {};
                let showRegions = true;

                // تحميل بيانات اللوكيشنات والمناطق
                Promise.all([
                    fetch('{{ route("dashboard.locations.map-data") }}'),
                    fetch('{{ route("dashboard.locations.regions-data") }}')
                ])
                .then(responses => Promise.all(responses.map(r => r.json())))
                .then(([locationsData, regionsData]) => {
                    // دبابيس اللوكيشنات
                    if (locationsData.success && locationsData.locations?.length > 0) {
                        locationsData.locations.forEach(location => {
                            const iconColor = location.icon_color || '#e74c3c';

                            // دبوس عادي (شكل marker) مع تلوين عن طريق shadow/filter
                            const pinIcon = L.icon({
                                iconUrl: "{{ asset('icons/person-marker.png') }}",
                                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                                iconSize:     [40, 40],
                                iconAnchor:   [12, 41],
                                popupAnchor:  [1, -34],
                                shadowSize:   [41, 41],
                            });

                            const marker = L.marker(
                                [location.coordinates.lat, location.coordinates.lng],
                                { icon: pinIcon }
                            ).addTo(map);

                            // تلوين الدبوس حسب اللون المخزن (glow حوالين الدبوس)
                            marker.on('add', () => {
                                if (marker._icon) {
                                    marker._icon.style.filter =
                                        `drop-shadow(0 0 0 ${iconColor}) drop-shadow(0 0 5px ${iconColor})`;
                                }
                            });

                            const popupContent = `
                                <div style="min-width: 260px; font-size: 13px;">
                                    <h6 style="color: ${iconColor}; margin-bottom: 8px;">
                                        <i class="fas fa-map-marker-alt mr-2"></i> ${location.name}
                                    </h6>
                                    ${location.address ? `<div class="small mb-1"><i class="fas fa-map-pin mr-1"></i>${location.address}</div>` : ''}
                                    ${location.phone ? `<div class="small mb-2"><i class="fas fa-phone mr-1"></i>${location.phone}</div>` : ''}
                                    <a href="{{ url('dashboard') }}/locations/${location.id}" class="btn btn-sm btn-primary btn-block">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            `;
                            marker.bindPopup(popupContent);

                            allBounds.push([location.coordinates.lat, location.coordinates.lng]);
                        });
                    }

                    // حدود المناطق (Polygons)
                    if (regionsData.success && regionsData.regions?.length > 0) {
                        regionsData.regions.forEach(region => {
                            if (region.boundaries && Array.isArray(region.boundaries) && region.boundaries.length > 0) {
                                const regionColor = region.color || '#3388ff';

                                const polygon = L.polygon(region.boundaries, {
                                    color: regionColor,
                                    weight: showRegions ? 3 : 0,
                                    fillColor: regionColor,
                                    fillOpacity: 0.15,
                                    stroke: true,
                                    className: 'region-polygon',
                                }).addTo(map);

                                polygon.bindPopup(`
                                    <div style="min-width: 220px; font-size: 13px;">
                                        <h6 style="color: ${regionColor}; margin-bottom: 10px;">
                                            <i class="fas fa-layer-group mr-2"></i> ${region.name}
                                        </h6>
                                        <div class="small text-muted">
                                            📊 ${region.locations_count || 0} لوكيشن
                                        </div>
                                    </div>
                                `);

                                regionPolygons[region.id] = polygon;
                                allBounds.push(region.boundaries[0]);
                            }
                        });
                    }

                    // تكبير تلقائي
                    if (allBounds.length > 0) {
                        setTimeout(() => {
                            map.fitBounds(allBounds, {
                                padding: [40, 40],
                                maxZoom: 15,
                                animate: true
                            });
                        }, 500);
                    }
                });

                // تكبير على كل شيء
                window.fitAllBounds = function() {
                    if (allBounds.length > 0) {
                        map.fitBounds(allBounds, {
                            padding: [50, 50],
                            maxZoom: 16,
                            animate: true
                        });
                    }
                };

                // إظهار / إخفاء المناطق
                window.toggleRegions = function() {
                    showRegions = !showRegions;
                    Object.values(regionPolygons).forEach(polygon => {
                        if (showRegions) {
                            map.addLayer(polygon);
                            polygon.setStyle({ weight: 3 });
                        } else {
                            map.removeLayer(polygon);
                        }
                    });
                    const toggleText = document.getElementById('regionsToggleText');
                    const toggleBtn = document.getElementById('regionsToggleBtn');
                    toggleText.textContent = showRegions ? 'إخفاء المناطق' : 'إظهار المناطق';
                    toggleBtn.style.backgroundColor = showRegions ? '' : '#e9ecef';
                };
            });
        </script>
    @endpush
</x-layout>
