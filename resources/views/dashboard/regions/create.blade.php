<x-layout :title="trans('regions.actions.create')" :breadcrumbs="['dashboard.regions.create']">

    @push('styles')
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css">

        <style>
            #map {
                height: 500px;
                width: 100%;
                border-radius: 8px;
                border: 2px solid #ddd;
            }

            .map-instructions {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
                border-right: 4px solid #007bff;
            }

            .color-preview {
                width: 50px;
                height: 50px;
                border-radius: 5px;
                border: 2px solid #ddd;
                display: inline-block;
                vertical-align: middle;
            }

            .form-section {
                background: #ffffff;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            /* نقاط التحديد + علامة الـ + بالأسود */
            .leaflet-editing-icon {
                position: relative;
                margin-left: -4px !important;
                margin-top: -4px !important;
                width: 14px !important;
                height: 14px !important;
                background-color: #000000 !important;   /* أسود */
                border-radius: 50% !important;
                border: 2px solid #ffffff !important;   /* إطار أبيض */
                box-shadow: 0 0 4px rgba(0,0,0,0.8) !important;
            }

            .leaflet-editing-icon::before {
                content: '+';
                color: #ffffff;
                font-weight: bold;
                font-size: 10px;
                line-height: 10px;
                position: absolute;
                top: 1px;
                left: 3px;
            }

            /* مؤشر الماوس أثناء الرسم يكون crosshair واضح */
            .leaflet-container.drawing-cursor {
                cursor: crosshair !important;
            }
        </style>
    @endpush

    @php
        // تجهيز بيانات المناطق الموجودة للخريطة
        $regionsForMap = ($regions ?? collect())->map(function ($r) {
            return [
                'id'         => $r->id,
                'name'       => $r->name,
                'color'      => $r->color ?? '#FF0000',
                'boundaries' => $r->boundaries ?? [],
            ];
        })->values();
    @endphp

    {{ BsForm::resource('regions')->post(route('dashboard.regions.store'), ['id' => 'regionForm']) }}
        @component('dashboard::components.box')
            @slot('title', trans('regions.actions.create'))

            {{-- معلومات المنطقة الأساسية --}}
            <div class="form-section">
                <h5 class="mb-3">معلومات المنطقة</h5>

                <div class="row">
                    {{-- اسم المنطقة --}}
                    <div class="col-md-6">
                        {{ BsForm::text('name')
                            ->label('اسم المنطقة')
                            ->attribute('required', true) }}
                    </div>

                    {{-- لون المنطقة --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color">لون المنطقة على الخريطة <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center">
                                <input type="color"
                                       name="color"
                                       id="color"
                                       class="form-control @error('color') is-invalid @enderror"
                                       value="{{ old('color', '#FF0000') }}"
                                       style="width: 100px; height: 45px;"
                                       required>
                                <div class="color-preview mr-3" id="colorPreview"></div>
                            </div>
                            @error('color')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- مسؤول المنطقة --}}
                    <div class="col-md-6">
                        {{ BsForm::select('area_responsible_id')
                            ->label('مسؤول المنطقة')
                            ->options($areaResponsibles->pluck('name','id')->toArray())
                            ->placeholder('-- اختر مسؤول المنطقة --') }}
                    </div>

                    {{-- حالة المنطقة --}}
                    <div class="col-md-6">
                        {{ BsForm::select('is_active')
                            ->label('حالة المنطقة')
                            ->options([
                                1 => 'نشطة',
                                0 => 'غير نشطة',
                            ])->value(old('is_active', 1)) }}
                    </div>
                </div>

                {{-- الوصف --}}
                {{ BsForm::textarea('description')
                    ->label('وصف المنطقة')
                    ->rows(3) }}
            </div>

            {{-- الخريطة --}}
            <div class="form-section">
                <h5 class="mb-3">تحديد حدود المنطقة على الخريطة</h5>

                <div class="map-instructions">
                    <i class="fas fa-info-circle"></i>
                    <strong>تعليمات:</strong>
                    <ul class="mb-0 mt-2">
                        <li>تظهر على الخريطة جميع المناطق المُسجَّلة مسبقًا بخطوط ملوَّنة.</li>
                        <li>لإنشاء منطقة جديدة اضغط على أيقونة المضلع في أعلى الخريطة.</li>
                        <li>اضغط على الخريطة لتحديد نقاط حدود المنطقة، ثم اضغط على النقطة الأولى لإنهاء الرسم.</li>
                        <li>يمكنك تعديل أو حذف المنطقة الجديدة باستخدام أدوات التحرير في الأعلى.</li>
                    </ul>
                </div>

                <div id="map"></div>

                {{-- الحقل المخفي للحدود --}}
                <input type="hidden" name="boundaries" id="boundaries" required>
                @error('boundaries')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror

                <div id="boundariesError" class="text-danger mt-2" style="display:none;">
                    يجب تحديد حدود المنطقة على الخريطة
                </div>
            </div>

            @slot('footer')
                {{ BsForm::submit()->label(trans('regions.actions.save')) }}
            @endslot
        @endcomponent
    {{ BsForm::close() }}

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const map = L.map('map').setView([31.3461, 34.3064], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const drawnItems    = new L.FeatureGroup();
                const existingGroup = L.featureGroup().addTo(map);
                map.addLayer(drawnItems);

                // عرض كل المناطق الموجودة مسبقًا
                const regions = @json($regionsForMap);

                regions.forEach(function (region) {
                    if (!region.boundaries || !region.boundaries.length) return;

                    const latlngs = region.boundaries.map(function (p) {
                        return [parseFloat(p.lat), parseFloat(p.lng)];
                    });

                    const polygon = L.polygon(latlngs, {
                        color: region.color,
                        fillColor: region.color,
                        fillOpacity: 0.3
                    }).addTo(existingGroup);

                    polygon.bindPopup(region.name);
                });

                if (existingGroup.getLayers().length) {
                    map.fitBounds(existingGroup.getBounds());
                }

                // أدوات رسم المنطقة الجديدة
                const drawControl = new L.Control.Draw({
                    position: 'topright',
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            showArea: true,
                            shapeOptions: {
                                color: document.getElementById('color').value,
                                fillOpacity: 0.3
                            }
                        },
                        polyline: false,
                        rectangle: false,
                        circle: false,
                        marker: false,
                        circlemarker: false
                    },
                    edit: {
                        featureGroup: drawnItems,
                        remove: true
                    }
                });
                map.addControl(drawControl);

                let currentPolygon = null;

                // تغيير شكل المؤشر عند بدء/إيقاف وضع الرسم
                map.on(L.Draw.Event.DRAWSTART, function () {
                    map.getContainer().classList.add('drawing-cursor');
                });
                map.on(L.Draw.Event.DRAWSTOP, function () {
                    map.getContainer().classList.remove('drawing-cursor');
                });

                map.on(L.Draw.Event.CREATED, function (e) {
                    map.getContainer().classList.remove('drawing-cursor');

                    const layer = e.layer;

                    if (currentPolygon) {
                        drawnItems.removeLayer(currentPolygon);
                    }

                    drawnItems.addLayer(layer);
                    currentPolygon = layer;

                    saveBoundaries(layer);
                });

                map.on(L.Draw.Event.EDITED, function (e) {
                    e.layers.eachLayer(function (layer) {
                        saveBoundaries(layer);
                    });
                });

                map.on(L.Draw.Event.DELETED, function () {
                    currentPolygon = null;
                    document.getElementById('boundaries').value = '';
                    document.getElementById('boundariesError').style.display = 'block';
                });

                function saveBoundaries(layer) {
                    const latlngs = layer.getLatLngs()[0];
                    const boundaries = latlngs.map(function (latlng) {
                        return { lat: latlng.lat, lng: latlng.lng };
                    });

                    document.getElementById('boundaries').value = JSON.stringify(boundaries);
                    document.getElementById('boundariesError').style.display = 'none';
                }

                const colorInput   = document.getElementById('color');
                const colorPreview = document.getElementById('colorPreview');
                colorPreview.style.backgroundColor = colorInput.value;

                colorInput.addEventListener('change', function () {
                    const color = this.value;
                    colorPreview.style.backgroundColor = color;

                    if (currentPolygon) {
                        currentPolygon.setStyle({
                            color: color,
                            fillColor: color
                        });
                    }

                    drawControl.setDrawingOptions({
                        polygon: {
                            shapeOptions: {
                                color: color,
                                fillOpacity: 0.3
                            }
                        }
                    });
                });

                document.getElementById('regionForm').addEventListener('submit', function (e) {
                    const boundaries = document.getElementById('boundaries').value;

                    if (!boundaries || boundaries === '') {
                        e.preventDefault();
                        document.getElementById('boundariesError').style.display = 'block';
                        document.getElementById('map').scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layout>
