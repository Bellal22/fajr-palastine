<x-layout :title="$region->name" :breadcrumbs="['dashboard.regions.show', $region]">

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #region-map {
                height: 400px;
                width: 100%;
                border-radius: 8px;
                border: 2px solid #ddd;
                margin-top: 10px;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                        <tr>
                            <th width="200">@lang('regions.attributes.name')</th>
                            <td>{{ $region->name }}</td>
                        </tr>
                        <tr>
                            <th>مسؤول المنطقة</th>
                            <td>{{ optional($region->responsible)->name }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>{{ $region->is_active ? 'نشطة' : 'غير نشطة' }}</td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td>{{ $region->description }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- خريطة عرض المنطقة --}}
                <div id="region-map"></div>

                @slot('footer')
                    @include('dashboard.regions.partials.actions.edit')
                    @include('dashboard.regions.partials.actions.delete')
                    @include('dashboard.regions.partials.actions.restore')
                    @include('dashboard.regions.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // boundaries كـ array بسبب الـ cast
                const boundaries = @json($region->boundaries ?? []);
                const color      = @json($region->color ?? '#FF0000');

                const defaultLatLng = [31.3461, 34.3064];
                const map = L.map('region-map').setView(defaultLatLng, 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                if (boundaries.length) {
                    const latlngs = boundaries.map(function (p) {
                        return [parseFloat(p.lat), parseFloat(p.lng)];
                    });

                    const polygon = L.polygon(latlngs, {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.3
                    }).addTo(map);

                    map.fitBounds(polygon.getBounds());
                }
            });
        </script>
    @endpush
</x-layout>
