<x-layout :title="trans('regions.plural')" :breadcrumbs="['dashboard.regions.index']">
    @include('dashboard.regions.partials.filter')

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #regions-map {
                height: 450px;
                width: 100%;
                border-radius: 8px;
                border: 2px solid #ddd;
                margin-top: 15px;
            }
        </style>
    @endpush

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('regions.actions.list') ({{ $regions->total() }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex">
                    <x-check-all-delete
                        type="{{ \App\Models\Region::class }}"
                        :resource="trans('regions.plural')"></x-check-all-delete>

                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                        @include('dashboard.regions.partials.actions.create')
                        @include('dashboard.regions.partials.actions.trashed')
                    </div>
                </div>
            </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
                <x-check-all></x-check-all>
            </th>
            <th>@lang('regions.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>

        <tbody>
        @forelse($regions as $region)
            <tr>
                <td class="text-center">
                    <x-check-all-item :model="$region"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.regions.show', $region) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $region->name }}
                    </a>
                </td>
                <td style="width: 160px">
                    @include('dashboard.regions.partials.actions.show')
                    @include('dashboard.regions.partials.actions.edit')
                    @include('dashboard.regions.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('regions.empty')</td>
            </tr>
        @endforelse

        @if($regions->hasPages())
            @slot('footer')
                {{ $regions->links() }}
            @endslot
        @endif
        </tbody>
    @endcomponent

    {{-- خريطة كل المناطق --}}
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">خريطة المناطق</h3>
        </div>
        <div class="card-body p-0">
            <div id="regions-map"></div>
        </div>
    </div>

    @php
        // تجهيز بيانات المناطق للخريطة (boundaries cast => array في الموديل)
        $regionsForMap = $regions->map(function ($r) {
            return [
                'id'         => $r->id,
                'name'       => $r->name,
                'color'      => $r->color ?? '#FF0000',
                'boundaries' => $r->boundaries ?? [],
            ];
        })->values();
    @endphp

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const regions = @json($regionsForMap);

                const defaultLatLng = [31.3461, 34.3064];
                const map = L.map('regions-map').setView(defaultLatLng, 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const group = L.featureGroup().addTo(map); // تجميع المضلعات لضبط fitBounds [web:134]

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
                        fillOpacity: 0.3
                    }).addTo(group);

                    polygon.bindPopup(region.name);
                });

                if (group.getLayers().length) {
                    map.fitBounds(group.getBounds());
                }
            });
        </script>
    @endpush
</x-layout>
