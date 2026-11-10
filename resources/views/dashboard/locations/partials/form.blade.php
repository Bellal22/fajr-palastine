@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .icon-preview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #ddd;
            display: inline-block;
            vertical-align: middle;
        }

        .location-type-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
            width: 23%;
        }

        .location-type-card:hover {
            border-color: #007bff;
            background: #f8f9fa;
        }

        .location-type-card.active {
            border-color: #007bff;
            background: #e7f3ff;
        }

        .location-type-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
@endpush

@include('dashboard.errors')

<div class="row">
    {{-- المعلومات الأساسية --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> @lang('locations.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')
                    ->label(trans('locations.attributes.name'))
                    ->placeholder(trans('locations.placeholders.name'))
                    ->required() }}

                <div class="form-group">
                    <label>
                        @lang('locations.attributes.type')
                        <span class="text-danger">*</span>
                    </label>
                    <div>
                        <div class="location-type-card{{ old('type', $location->type ?? 'other') == 'house' ? ' active' : '' }}" data-type="house">
                            <i class="fas fa-home text-primary"></i>
                            <div>@lang('locations.types.house')</div>
                        </div>
                        <div class="location-type-card{{ old('type', $location->type ?? 'other') == 'shelter' ? ' active' : '' }}" data-type="shelter">
                            <i class="fas fa-warehouse text-info"></i>
                            <div>@lang('locations.types.shelter')</div>
                        </div>
                        <div class="location-type-card{{ old('type', $location->type ?? 'other') == 'center' ? ' active' : '' }}" data-type="center">
                            <i class="fas fa-building text-success"></i>
                            <div>@lang('locations.types.center')</div>
                        </div>
                        <div class="location-type-card{{ old('type', $location->type ?? 'other') == 'other' ? ' active' : '' }}" data-type="other">
                            <i class="fas fa-map-pin text-danger"></i>
                            <div>@lang('locations.types.other')</div>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="type" value="{{ old('type', $location->type ?? 'other') }}" required>
                </div>

                <div class="form-group">
                    <label for="icon_color">
                        @lang('locations.attributes.icon_color')
                    </label>
                    <div class="d-flex align-items-center">
                        <input type="color"
                               name="icon_color"
                               id="icon_color"
                               class="form-control"
                               value="{{ old('icon_color', $location->icon_color ?? '#9C27B0') }}"
                               style="width: 100px; height: 45px;">
                        <div class="icon-preview" id="iconPreview"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- معلومات الاتصال والوصف --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-gray text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-address-card"></i> @lang('locations.sections.contact_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('address')
                    ->label(trans('locations.attributes.address'))
                    ->placeholder(trans('locations.placeholders.address')) }}

                {{ BsForm::text('phone')
                    ->label(trans('locations.attributes.phone'))
                    ->placeholder(trans('locations.placeholders.phone')) }}

                {{ BsForm::textarea('description')
                    ->label(trans('locations.attributes.description'))
                    ->placeholder(trans('locations.placeholders.description'))
                    ->rows(4) }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @lang('locations.hints.description')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- معلومات المنطقة --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-map-marked-alt"></i> @lang('locations.sections.region_info')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="region_id">
                        @lang('locations.attributes.region')
                        <span class="text-danger">*</span>
                    </label>
                    <select name="region_id" id="region_id" class="form-control" required>
                        <option value="">@lang('locations.placeholders.region')</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}"
                                {{ old('region_id', $location->region_id ?? '') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="area_responsible_id">
                        @lang('locations.attributes.area_responsible')
                        <span class="text-danger">*</span>
                    </label>
                    <select name="area_responsible_id" id="area_responsible_id" class="form-control"
                        {{ (!isset($location) || !$location->areaResponsible) ? 'disabled' : '' }}>
                        <option value="">@lang('locations.placeholders.area_responsible')</option>
                        @if(isset($location) && $location->areaResponsible)
                            <option value="{{ $location->areaResponsible->id }}" selected>
                                {{ $location->areaResponsible->name }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="block_id">
                        @lang('locations.attributes.block')
                        <span class="text-danger">*</span>
                    </label>
                    <select name="block_id" id="block_id" class="form-control" required
                        {{ !isset($location) ? 'disabled' : '' }}>
                        <option value="">@lang('locations.placeholders.block')</option>
                        @if(isset($location) && $location->block)
                            <option value="{{ $location->block->id }}" selected
                                data-lat="{{ $location->block->lat }}"
                                data-lng="{{ $location->block->lng ?? $location->block->lan }}"
                                data-phone="{{ $location->block->phone }}"
                                data-address="{{ $location->block->title }}">
                                {{ $location->block->name }} - {{ $location->block->title }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- الخريطة وتحديد الإحداثيات --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-map"></i> @lang('locations.sections.map')
        </h5>
        <span class="badge badge-light text-dark font-weight-normal">
            <i class="fas fa-crosshairs text-danger"></i> إدخال الإحداثيات وتحديد الموقع
        </span>
    </div>
    <div class="card-body">
        {{-- صندوق إدخال الإحداثيات المباشر واللصق من خرائط جوجل --}}
        <div class="card mb-3 border-primary" style="background: #f8fafc; border-width: 1px;">
            <div class="card-body py-3 px-3">
                <div class="row">
                    {{-- لصق سريع من Google Maps --}}
                    <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                        <label class="font-weight-bold text-dark mb-1 d-block text-truncate" for="quick_coords" style="height: 24px; line-height: 24px;">
                            <i class="fas fa-paste text-primary"></i> لصق إحداثيات من Google Maps (سريع):
                        </label>
                        <div class="input-group">
                            <input type="text" id="quick_coords" class="form-control"
                                   style="height: 38px;"
                                   placeholder="مثال: 31.338683, 34.245407 أو رابط خرائط جوجل">
                            <div class="input-group-append">
                                <button class="btn btn-primary font-weight-bold" type="button" id="apply_quick_coords"
                                        style="height: 38px; padding: 0 16px;" title="تطبيق الإحداثيات على الخريطة">
                                    <i class="fas fa-check"></i> تطبيق
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- خط العرض Latitude --}}
                    <div class="col-lg-3 col-md-5 mb-2 mb-lg-0">
                        <label class="font-weight-bold text-dark mb-1 d-block text-truncate" for="latitude" style="height: 24px; line-height: 24px;">
                            <i class="fas fa-arrows-alt-v text-info"></i> خط العرض (Latitude) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="latitude" id="latitude" class="form-control font-weight-bold"
                               style="height: 38px;"
                               value="{{ old('latitude', $location->latitude ?? '') }}"
                               placeholder="مثال: 31.338683" required>
                    </div>

                    {{-- خط الطول Longitude --}}
                    <div class="col-lg-3 col-md-5 mb-2 mb-lg-0">
                        <label class="font-weight-bold text-dark mb-1 d-block text-truncate" for="longitude" style="height: 24px; line-height: 24px;">
                            <i class="fas fa-arrows-alt-h text-info"></i> خط الطول (Longitude) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="longitude" id="longitude" class="form-control font-weight-bold"
                               style="height: 38px;"
                               value="{{ old('longitude', $location->longitude ?? '') }}"
                               placeholder="مثال: 34.245407" required>
                    </div>

                    {{-- زر إسقاط وتحديد على الخريطة --}}
                    <div class="col-lg-1 col-md-2 mb-2 mb-lg-0">
                        <label class="font-weight-bold text-dark mb-1 d-block text-center" style="height: 24px; line-height: 24px;">
                            إسقاط
                        </label>
                        <button type="button" id="btn_locate_coords" class="btn btn-success btn-block"
                                style="height: 38px; display: flex; align-items: center; justify-content: center;"
                                title="إسقاط وتحديد الإحداثيات المدخلة على الخريطة">
                            <i class="fas fa-crosshairs"></i>
                        </button>
                    </div>
                </div>

                {{-- نص توضيحي أسفل الصف --}}
                <div class="mt-2 text-muted" style="font-size: 12.5px;">
                    <i class="fas fa-info-circle text-primary"></i> يمكنك نسخ الإحداثيات أو الرابط من Google Maps ولصقها في خانة اللصق السريع، أو تعديل خط العرض والطول يدوياً والضغط على زر الإسقاط.
                </div>

                {{-- تنبيه الموقع المحدد يقع خارج حدود المنطقة --}}
                <div id="outsideBoundaryAlert" class="alert alert-warning mt-3 mb-0" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>تنبيه جغرافي:</strong> <span id="outsideBoundaryText">الموقع المحدد حالياً يقع خارج نطاق حدود المنطقة!</span>
                </div>
            </div>
        </div>

        {{-- تنبيه المناديب خارج نطاق حدود المنطقة --}}
        <div id="outsideDelegatesAlert" class="alert alert-warning mb-3" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>تنبيه المناديب:</strong> <span id="outsideDelegatesText"></span>
        </div>

        {{-- التعليمات فوق الخريطة --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <div style="background: #e8f4f8; padding: 15px; border-radius: 5px; border-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 4px solid #17a2b8;">
                    <h6 class="mb-2" style="color: #0c5460;">
                        <i class="fas fa-info-circle"></i>
                        <strong>@lang('locations.map.instructions_title')</strong>
                    </h6>
                    <ul class="mb-0" style="font-size: 14px; color: #0c5460; padding-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px;">
                        <li class="mb-1">@lang('locations.map.instruction_1')</li>
                        <li class="mb-1">@lang('locations.map.instruction_2')</li>
                        <li class="mb-1">@lang('locations.map.instruction_3')</li>
                        <li class="mb-0">@lang('locations.map.instruction_4')</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 4px solid #ffc107;">
                    <h6 class="mb-2" style="color: #856404;">
                        <i class="fas fa-lightbulb"></i>
                        <strong>@lang('locations.map.tips_title')</strong>
                    </h6>
                    <ul class="mb-0" style="font-size: 14px; color: #856404; padding-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px;">
                        <li class="mb-1">@lang('locations.map.tip_1')</li>
                        <li class="mb-1">@lang('locations.map.tip_2')</li>
                        <li class="mb-0">@lang('locations.map.tip_3')</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- الخريطة بعرض كامل --}}
        <div class="mb-2">
            <span class="text-muted" style="font-size: 13px;">
                <i class="fas fa-hand-pointer text-primary"></i> انقر في أي مكان على الخريطة أو اسحب الدبوس لتعديل موقع المندوب والمخيم بدقة
            </span>
        </div>
        <div id="map"></div>

        <div id="coordinatesError" class="text-danger mt-2" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            @lang('locations.map.error_required')
        </div>

        <div id="coordinatesDisplay" class="mt-2 text-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            @lang('locations.map.coordinates'): <span id="latDisplay"></span>, <span id="lngDisplay"></span>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const areaResponsibleUrl = '{{ route("dashboard.locations.area-responsible-by-region") }}';
            const blocksUrl = '{{ route("dashboard.locations.blocks-by-area-responsible") }}';
            const regionBoundariesUrl = '{{ route("dashboard.locations.region-boundaries") }}';

            const isEdit = {{ isset($location) ? 'true' : 'false' }};
            const existingLat = {{ $location->latitude ?? 'null' }};
            const existingLng = {{ $location->longitude ?? 'null' }};

            const map = L.map('map').setView([existingLat || 31.3461, existingLng || 34.3064], 13);

            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            const googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            L.control.layers({
                "OpenStreetMap": osm,
                "Google Satellite": googleSat
            }).addTo(map);

            let currentMarker = null;
            let regionPolygon = null;
            let currentRegionBoundaries = null;
            let currentLoadedBlocks = [];

            const locationMarkerIcon = L.icon({
                iconUrl: "{{ asset('icons/person-marker.png') }}",
                iconSize: [42, 42],
                iconAnchor: [21, 42],
                popupAnchor: [0, -42],
                shadowSize: [41, 41]
            });

            // التحقق مما إذا كانت النقطة داخل مضلع المنطقة (Ray-Casting Algorithm)
            function isPointInPolygon(lat, lng, polygonCoords) {
                if (!polygonCoords || polygonCoords.length < 3) return true;
                const x = parseFloat(lat);
                const y = parseFloat(lng);
                if (isNaN(x) || isNaN(y) || (x === 0 && y === 0)) return true;

                let inside = false;
                const count = polygonCoords.length;
                let j = count - 1;

                for (let i = 0; i < count; i++) {
                    const pi = polygonCoords[i];
                    const pj = polygonCoords[j];
                    const xi = parseFloat(pi.lat !== undefined ? pi.lat : pi[0]);
                    const yi = parseFloat(pi.lng !== undefined ? pi.lng : pi[1]);
                    const xj = parseFloat(pj.lat !== undefined ? pj.lat : pj[0]);
                    const yj = parseFloat(pj.lng !== undefined ? pj.lng : pj[1]);

                    const intersect = ((yi > y) !== (yj > y)) &&
                        (x < (xj - xi) * (y - yi) / (yj - yi) + xi);

                    if (intersect) inside = !inside;
                    j = i;
                }
                return inside;
            }

            // فحص إحداثيات اللوكيشن الحالي مقابل حدود المنطقة وإظهار تنبيه إذا كان خارجها
            function checkBoundary(lat, lng) {
                const alertBox = document.getElementById('outsideBoundaryAlert');
                const alertText = document.getElementById('outsideBoundaryText');
                if (!alertBox) return;

                if (currentRegionBoundaries && currentRegionBoundaries.length >= 3) {
                    const isInside = isPointInPolygon(lat, lng, currentRegionBoundaries);
                    if (!isInside) {
                        if (alertText) {
                            alertText.textContent = `الموقع المحدد (${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}) يقع خارج نطاق حدود المنطقة!`;
                        }
                        alertBox.style.display = 'block';
                    } else {
                        alertBox.style.display = 'none';
                    }
                } else {
                    alertBox.style.display = 'none';
                }
            }

            // تقييم مناديب المنطقة المحملين وإبراز من هم خارج حدود المنطقة
            function evaluateLoadedBlocks() {
                const blockSelect = document.getElementById('block_id');
                const outsideAlert = document.getElementById('outsideDelegatesAlert');
                const outsideText = document.getElementById('outsideDelegatesText');
                const outsideList = [];

                if (!currentRegionBoundaries || currentRegionBoundaries.length < 3) {
                    if (outsideAlert) outsideAlert.style.display = 'none';
                    return;
                }

                if (blockSelect) {
                    Array.from(blockSelect.options).forEach(opt => {
                        if (!opt.value) return;
                        const optLat = parseFloat(opt.dataset.lat);
                        const optLng = parseFloat(opt.dataset.lng);
                        const baseTitle = opt.dataset.baseTitle || opt.textContent.replace(' ⚠️ (خارج نطاق المنطقة)', '');
                        opt.dataset.baseTitle = baseTitle;

                        if (optLat && optLng && (optLat !== 0 || optLng !== 0)) {
                            const inside = isPointInPolygon(optLat, optLng, currentRegionBoundaries);
                            if (!inside) {
                                opt.textContent = baseTitle + ' ⚠️ (خارج نطاق المنطقة)';
                                opt.style.color = '#dc3545';
                                opt.dataset.outside = '1';
                                outsideList.push(baseTitle);
                            } else {
                                opt.textContent = baseTitle;
                                opt.style.color = '';
                                opt.dataset.outside = '0';
                            }
                        }
                    });
                }

                if (outsideAlert && outsideText) {
                    if (outsideList.length > 0) {
                        outsideText.innerHTML = 'المناديب التاليين في هذه المنطقة تقع إحداثياتهم خارج نطاق حدود المنطقة المرسومة: <b>' + outsideList.join('، ') + '</b>';
                        outsideAlert.style.display = 'block';
                    } else {
                        outsideAlert.style.display = 'none';
                    }
                }
            }

            // استخراج الإحداثيات من النصوص وروابط Google Maps
            function parseGoogleCoords(str) {
                if (!str) return null;
                str = str.trim();
                const match = str.match(/([-+]?\d{1,2}\.\d+)[,\s]+([-+]?\d{1,3}\.\d+)/);
                if (match) {
                    return {
                        lat: parseFloat(match[1]),
                        lng: parseFloat(match[2])
                    };
                }
                return null;
            }

            // تطبيق الإحداثيات الملصوقة من خرائط جوجل
            function applyQuickCoords() {
                const input = document.getElementById('quick_coords');
                if (!input) return;
                const coords = parseGoogleCoords(input.value);
                if (coords) {
                    setLocationMarker(coords.lat, coords.lng, true);
                    map.setView([coords.lat, coords.lng], 16);
                } else if (input.value.trim() !== '') {
                    alert('لم نتمكن من استخراج الإحداثيات. يرجى إدخال إحداثيات صحيحة، مثل: 31.338683, 34.245407');
                }
            }

            const applyQuickBtn = document.getElementById('apply_quick_coords');
            if (applyQuickBtn) {
                applyQuickBtn.addEventListener('click', applyQuickCoords);
            }
            const quickCoordsInput = document.getElementById('quick_coords');
            if (quickCoordsInput) {
                quickCoordsInput.addEventListener('paste', function() {
                    setTimeout(applyQuickCoords, 100);
                });
                quickCoordsInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyQuickCoords();
                    }
                });
            }

            // تطبيق الإحداثيات عند الكتابة اليدوية في حقلي Latitude و Longitude
            function applyManualCoords() {
                const lat = parseFloat(document.getElementById('latitude').value);
                const lng = parseFloat(document.getElementById('longitude').value);
                if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                    setLocationMarker(lat, lng, true);
                    map.setView([lat, lng], 15);
                }
            }

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const locateBtn = document.getElementById('btn_locate_coords');
            if (latInput) latInput.addEventListener('change', applyManualCoords);
            if (lngInput) lngInput.addEventListener('change', applyManualCoords);
            if (locateBtn) locateBtn.addEventListener('click', applyManualCoords);

            function setLocationMarker(lat, lng, openPopup = false) {
                const numLat = parseFloat(lat);
                const numLng = parseFloat(lng);
                if (isNaN(numLat) || isNaN(numLng)) return;

                if (currentMarker) {
                    currentMarker.setLatLng([numLat, numLng]);
                } else {
                    currentMarker = L.marker([numLat, numLng], {
                        draggable: true,
                        icon: locationMarkerIcon
                    }).addTo(map);

                    currentMarker.on('dragend', function(e) {
                        const position = e.target.getLatLng();
                        saveCoordinates(position.lat, position.lng);
                    });
                }

                currentMarker.bindPopup('📍 <b>موقع المندوب / المخيم</b><br><small>اسحب الدبوس أو انقر على الخريطة لتغيير الموقع</small>');

                if (openPopup) {
                    currentMarker.openPopup();
                }

                saveCoordinates(numLat, numLng);
            }

            function drawRegionPolygon(boundaries, name, color) {
                if (regionPolygon) {
                    map.removeLayer(regionPolygon);
                    regionPolygon = null;
                }
                currentRegionBoundaries = boundaries;
                if (!boundaries || boundaries.length === 0) return;

                regionPolygon = L.polygon(boundaries, {
                    color: color || '#3388ff',
                    weight: 3,
                    fillOpacity: 0.18
                }).addTo(map).bindPopup(name || '');

                // النقر على مضلع المنطقة يحدد اللوكيشن فوراً
                regionPolygon.on('click', function(e) {
                    setLocationMarker(e.latlng.lat, e.latlng.lng, true);
                });

                evaluateLoadedBlocks();
                if (currentMarker) {
                    const pos = currentMarker.getLatLng();
                    checkBoundary(pos.lat, pos.lng);
                }
            }

            // إذا كان Edit وفيه إحداثيات موجودة
            if (isEdit && existingLat && existingLng) {
                setLocationMarker(existingLat, existingLng, true);
            }

            // السماح بالنقر على الخريطة لتحديد أو تعديل موقع اللوكيشن
            map.on('click', function(e) {
                setLocationMarker(e.latlng.lat, e.latlng.lng, true);
            });

            // اختيار نوع اللوكيشن
            document.querySelectorAll('.location-type-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.location-type-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('type').value = this.dataset.type;
                });
            });

            // اختيار المنطقة
            document.getElementById('region_id').addEventListener('change', function() {
                const regionId = this.value;
                const areaSelect = document.getElementById('area_responsible_id');
                const blockSelect = document.getElementById('block_id');

                areaSelect.innerHTML = '<option value="">جاري التحميل...</option>';
                areaSelect.disabled = true;
                blockSelect.innerHTML = '<option value="">-- اختر مسؤول المنطقة أولاً --</option>';
                blockSelect.disabled = true;

                if (!regionId) return;

                // جلب مسؤول المنطقة
                fetch(`${areaResponsibleUrl}?region_id=${regionId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.area_responsible) {
                            areaSelect.innerHTML = `<option value="${data.area_responsible.id}" selected>${data.area_responsible.name}</option>`;
                            areaSelect.value = data.area_responsible.id;
                            areaSelect.disabled = false;
                            loadBlocksByAreaResponsible(data.area_responsible.id);
                        }
                    });

                // جلب حدود المنطقة
                fetch(`${regionBoundariesUrl}?region_id=${regionId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.region) {
                            const lat = parseFloat(data.region.center_lat);
                            const lng = parseFloat(data.region.center_lng);
                            map.setView([lat, lng], 14);

                            if (data.region.boundaries && data.region.boundaries.length > 0) {
                                drawRegionPolygon(data.region.boundaries, data.region.name, data.region.color);
                            }
                        }
                    });
            });

            // تحميل البلوكات
            function loadBlocksByAreaResponsible(areaResponsibleId, selectedBlockId = null) {
                const blockSelect = document.getElementById('block_id');
                const existingSelectedOption = selectedBlockId ? blockSelect.querySelector(`option[value="${selectedBlockId}"]`) : null;
                const existingOptionClone = existingSelectedOption ? existingSelectedOption.cloneNode(true) : null;

                if (!selectedBlockId) {
                    blockSelect.innerHTML = '<option value="">جاري التحميل...</option>';
                }

                fetch(`${blocksUrl}?area_responsible_id=${areaResponsibleId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.blocks && data.blocks.length > 0) {
                            currentLoadedBlocks = data.blocks;
                            blockSelect.innerHTML = '<option value="">-- اختر المندوب --</option>';
                            let foundSelected = false;
                            data.blocks.forEach(block => {
                                const option = document.createElement('option');
                                option.value = block.id;
                                option.textContent = `${block.name ?? ''} - ${block.title ?? ''}`.trim();
                                option.dataset.lat = block.lat || 0;
                                option.dataset.lng = block.lng || block.lan || 0;
                                option.dataset.phone = block.phone || '';
                                option.dataset.address = block.title || '';
                                if (selectedBlockId && String(block.id) === String(selectedBlockId)) {
                                    option.selected = true;
                                    foundSelected = true;
                                }
                                blockSelect.appendChild(option);
                            });
                            if (selectedBlockId && !foundSelected && existingOptionClone) {
                                existingOptionClone.selected = true;
                                blockSelect.appendChild(existingOptionClone);
                            }
                            blockSelect.disabled = false;
                            evaluateLoadedBlocks();
                        } else if (selectedBlockId && existingOptionClone) {
                            blockSelect.disabled = false;
                            evaluateLoadedBlocks();
                        }
                    });
            }

            // اختيار البلوك (المندوب هو نفسه لوكيشن المخيم)
            document.getElementById('block_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!this.value) return;

                const lat = parseFloat(selectedOption.dataset.lat);
                const lng = parseFloat(selectedOption.dataset.lng);

                // اقتراح اسم المخيم/المندوب تلقائياً إذا كان حقل الاسم فارغاً
                const nameInput = document.getElementById('name');
                if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                    nameInput.value = selectedOption.textContent.trim();
                }

                // اقتراح العنوان ورقم الهاتف إذا كانا فارغين
                const addressInput = document.getElementById('address');
                if (addressInput && (!addressInput.value || addressInput.value.trim() === '') && selectedOption.dataset.address) {
                    addressInput.value = selectedOption.dataset.address;
                }
                const phoneInput = document.getElementById('phone');
                if (phoneInput && (!phoneInput.value || phoneInput.value.trim() === '') && selectedOption.dataset.phone) {
                    phoneInput.value = selectedOption.dataset.phone;
                }

                if (!isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)) {
                    map.setView([lat, lng], 15);
                    // دبوس واحد فقط يمثل موقع المندوب / المخيم
                    setLocationMarker(lat, lng, true);
                }
            });

            // حفظ الإحداثيات
            function saveCoordinates(lat, lng) {
                const numLat = parseFloat(lat);
                const numLng = parseFloat(lng);
                if (isNaN(numLat) || isNaN(numLng)) return;
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                if (latInput) latInput.value = numLat.toFixed(8);
                if (lngInput) lngInput.value = numLng.toFixed(8);

                const latDisplay = document.getElementById('latDisplay');
                const lngDisplay = document.getElementById('lngDisplay');
                if (latDisplay) latDisplay.textContent = numLat.toFixed(6);
                if (lngDisplay) lngDisplay.textContent = numLng.toFixed(6);

                const coordDisplay = document.getElementById('coordinatesDisplay');
                const coordError = document.getElementById('coordinatesError');
                if (coordDisplay) coordDisplay.style.display = 'block';
                if (coordError) coordError.style.display = 'none';

                checkBoundary(numLat, numLng);
            }

            // معاينة اللون
            const iconColor = document.getElementById('icon_color');
            const iconPreview = document.getElementById('iconPreview');
            if (iconColor && iconPreview) {
                iconPreview.style.backgroundColor = iconColor.value;
                iconColor.addEventListener('input', function() {
                    iconPreview.style.backgroundColor = this.value;
                });
            }

            // Validation وحفظ الإحداثيات المؤكدة قبل الإرسال
            const latInputElem = document.getElementById('latitude');
            const targetForm = latInputElem ? latInputElem.closest('form') : document.querySelector('form');
            if (targetForm) {
                targetForm.addEventListener('submit', function(e) {
                    // التأكد من أخذ إحداثيات الدبوس الحالية على الخريطة
                    if (currentMarker) {
                        const markerPos = currentMarker.getLatLng();
                        saveCoordinates(markerPos.lat, markerPos.lng);
                    }

                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    if (!lat || !lng) {
                        e.preventDefault();
                        document.getElementById('coordinatesError').style.display = 'block';
                        document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            // تحميل البلوكات وحدود المنطقة في حالة Edit
            @if(isset($location) && $location->area_responsible_id)
                loadBlocksByAreaResponsible({{ $location->area_responsible_id }}, {{ old('block_id', $location->block_id ?? 'null') }});
            @endif
            @if(isset($location) && $location->region_id)
                fetch(`${regionBoundariesUrl}?region_id={{ $location->region_id }}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.region && data.region.boundaries && data.region.boundaries.length > 0) {
                            drawRegionPolygon(data.region.boundaries, data.region.name, data.region.color);
                        }
                    });
            @endif
        });
    </script>
@endpush
