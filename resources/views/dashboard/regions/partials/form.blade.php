@include('dashboard.errors')

<div class="row">
    {{-- معلومات المنطقة الأساسية --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map"></i> @lang('regions.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')
                    ->label(trans('regions.attributes.name'))
                    ->placeholder(trans('regions.placeholders.name'))
                    ->required() }}

                <div class="form-group">
                    <label for="color">
                        @lang('regions.attributes.color')
                        <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex align-items-center">
                        <input type="color"
                               name="color"
                               id="color"
                               class="form-control"
                               value="{{ old('color', $region->color ?? '#FF0000') }}"
                               style="width: 100px; height: 45px;"
                               required>
                        <div class="color-preview" id="colorPreview" style="width: 50px; height: 50px; border-radius: 5px; border: 2px solid #ddd; margin-right: 15px;"></div>
                    </div>
                </div>

                {{ BsForm::select('area_responsible_id')
                    ->label(trans('regions.attributes.area_responsible'))
                    ->options($areaResponsibles->pluck('name','id')->toArray())
                    ->placeholder(trans('regions.placeholders.area_responsible')) }}
            </div>
        </div>
    </div>

    {{-- إعدادات المنطقة --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-gray text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> @lang('regions.sections.settings')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::select('is_active')
                    ->label(trans('regions.attributes.status'))
                    ->options([
                        1 => trans('regions.status.active'),
                        0 => trans('regions.status.inactive')
                    ])
                    ->value(old('is_active', $region->is_active ?? 1))
                    ->required() }}

                {{ BsForm::textarea('description')
                    ->label(trans('regions.attributes.description'))
                    ->placeholder(trans('regions.placeholders.description'))
                    ->rows(6) }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @lang('regions.hints.description')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- الخريطة --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-draw-polygon"></i> @lang('regions.sections.map')
        </h5>
    </div>
    <div class="card-body">
        {{-- التعليمات فوق الخريطة --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <div style="background: #e8f4f8; padding: 15px; border-radius: 5px; border-right: 4px solid #17a2b8;">
                    <h6 class="mb-2" style="color: #0c5460;">
                        <i class="fas fa-info-circle"></i>
                        <strong>@lang('regions.map.instructions_title')</strong>
                    </h6>
                    <ul class="mb-0" style="font-size: 14px; color: #0c5460; padding-right: 20px;">
                        <li class="mb-1">@lang('regions.map.instruction_1')</li>
                        <li class="mb-1">@lang('regions.map.instruction_2')</li>
                        <li class="mb-1">@lang('regions.map.instruction_3')</li>
                        <li class="mb-0">@lang('regions.map.instruction_4')</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-right: 4px solid #ffc107;">
                    <h6 class="mb-2" style="color: #856404;">
                        <i class="fas fa-lightbulb"></i>
                        <strong>@lang('regions.map.tips_title')</strong>
                    </h6>
                    <ul class="mb-0" style="font-size: 14px; color: #856404; padding-right: 20px;">
                        <li class="mb-1">@lang('regions.map.tip_1')</li>
                        <li class="mb-1">@lang('regions.map.tip_2')</li>
                        <li class="mb-1">@lang('regions.map.tip_3')</li>
                        <li class="mb-0">@lang('regions.map.tip_4')</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- الخريطة بعرض كامل --}}
        <div id="map" style="height: 500px; width: 100%; border-radius: 8px; border: 2px solid #ddd;"></div>

        <input type="hidden"
               name="boundaries"
               id="boundaries"
               value="{{ old('boundaries', isset($region) ? json_encode($region->boundaries ?? []) : '') }}"
               required>

        <div id="boundariesError" class="text-danger mt-2" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i>
            @lang('regions.map.error_required')
        </div>

        <div id="boundariesSuccess" class="text-success mt-2 font-weight-bold" style="display:none; font-size: 15px;">
            <i class="fas fa-check-circle"></i>
            @lang('regions.map.success')
            <span id="liveAreaBadge" class="badge badge-info ml-2 px-3 py-2" style="font-size: 14px;"></span>
        </div>

        {{-- تنبيه فوري إذا كان هناك مناديب خارج حدود المنطقة المرسومة --}}
        <div id="regionOutsideDelegatesAlert" class="alert alert-warning mt-3" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>تنبيه جغرافي:</strong> <span id="regionOutsideDelegatesText"></span>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    {{-- Leaflet.PM CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />

    <style>
        .leaflet-pm-icon-marker {
            z-index: 10000;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- Leaflet.PM JS --}}
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isEdit = {{ isset($region) ? 'true' : 'false' }};
            const existingBoundaries = @json($region->boundaries ?? []);
            const currentRegionId = {{ $region->id ?? 'null' }};

            const map = L.map('map').setView([31.3461, 34.3064], 13);

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

            const existingGroup = L.featureGroup().addTo(map);

            const allRegions = @json($regions ?? []);
            const regionsToShow = allRegions.filter(r => r.id !== currentRegionId);

            regionsToShow.forEach(function (region) {
                if (!region.boundaries || !region.boundaries.length) return;

                const latlngs = region.boundaries.map(p => [parseFloat(p.lat), parseFloat(p.lng)]);
                const polygon = L.polygon(latlngs, {
                    color: region.color || '#FF0000',
                    fillColor: region.color || '#FF0000',
                    fillOpacity: 0.3,
                    interactive: false // مناطق الآخرين غير قابلة للتفاعل
                }).addTo(existingGroup).bindPopup(region.name);
            });

            let currentPolygon = null;

            // دالة حساب المساحة الجيوديسية الحية بالأمتار المربعة والكيلومتر المربع
            function computeGeodesicArea(latlngs) {
                if (!latlngs || latlngs.length < 3) return '';
                const radius = 6378137.0;
                let areaMeters = 0.0;
                const count = latlngs.length;

                for (let i = 0; i < count; i++) {
                    const p1 = latlngs[i];
                    const p2 = latlngs[(i + 1) % count];

                    const lat1 = p1.lat * Math.PI / 180;
                    const lng1 = p1.lng * Math.PI / 180;
                    const lat2 = p2.lat * Math.PI / 180;
                    const lng2 = p2.lng * Math.PI / 180;

                    areaMeters += (lng2 - lng1) * (2 + Math.sin(lat1) + Math.sin(lat2));
                }

                areaMeters = Math.abs(areaMeters * (radius * radius) / 2.0);
                const roundedMeters = Math.round(areaMeters);

                if (areaMeters >= 1000000) {
                    const km2 = (areaMeters / 1000000.0).toFixed(2);
                    return `المساحة: ${km2} كم² (${roundedMeters.toLocaleString('ar-EG')} م²)`;
                } else {
                    return `المساحة: ${roundedMeters.toLocaleString('ar-EG')} م²`;
                }
            }

            const blocksUrl = '{{ route("dashboard.locations.blocks-by-area-responsible") }}';
            const blocksGroup = L.featureGroup().addTo(map);
            let currentResponsibleBlocks = [];

            // خوارزمية فحص النقطة داخل المضلع
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

            // فحص مناديب مسؤول المنطقة المختار مقابل حدود المنطقة المرسومة
            function checkDelegatesInsideCurrentPolygon() {
                const alertBox = document.getElementById('regionOutsideDelegatesAlert');
                const alertText = document.getElementById('regionOutsideDelegatesText');
                if (!alertBox) return;

                const rawBoundaries = document.getElementById('boundaries').value;
                let boundaries = [];
                try {
                    boundaries = rawBoundaries ? JSON.parse(rawBoundaries) : [];
                } catch(e) {}

                if (!boundaries || boundaries.length < 3 || currentResponsibleBlocks.length === 0) {
                    alertBox.style.display = 'none';
                    return;
                }

                const outsideNames = [];
                currentResponsibleBlocks.forEach(b => {
                    const bLat = parseFloat(b.lat);
                    const bLng = parseFloat(b.lng || b.lan);
                    if (bLat && bLng && (bLat !== 0 || bLng !== 0)) {
                        const inside = isPointInPolygon(bLat, bLng, boundaries);
                        if (!inside) {
                            outsideNames.push(b.name + (b.title ? ' (' + b.title + ')' : ''));
                        }
                    }
                });

                if (outsideNames.length > 0) {
                    alertText.innerHTML = 'المناديب التاليين التابعين لمسؤول المنطقة تقع إحداثياتهم <b>خارج حدود المنطقة المرسومة</b>: ' + outsideNames.join('، ');
                    alertBox.style.display = 'block';
                } else {
                    alertBox.style.display = 'none';
                }
            }

            function loadResponsibleBlocks(areaId) {
                blocksGroup.clearLayers();
                currentResponsibleBlocks = [];
                if (!areaId) {
                    checkDelegatesInsideCurrentPolygon();
                    return;
                }

                fetch(`${blocksUrl}?area_responsible_id=${areaId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.blocks && data.blocks.length > 0) {
                            currentResponsibleBlocks = data.blocks;
                            data.blocks.forEach(block => {
                                const bLat = parseFloat(block.lat);
                                const bLng = parseFloat(block.lng || block.lan);
                                if (bLat && bLng && (bLat !== 0 || bLng !== 0)) {
                                    const marker = L.marker([bLat, bLng], {
                                        icon: L.icon({
                                            iconUrl: "{{ asset('icons/person-marker.png') }}",
                                            iconSize: [32, 32],
                                            iconAnchor: [16, 32],
                                            popupAnchor: [0, -32]
                                        })
                                    }).addTo(blocksGroup);
                                    marker.bindPopup(`📍 <b>مندوب: ${block.name}</b><br><small>${block.title || ''}</small>`);
                                }
                            });
                            checkDelegatesInsideCurrentPolygon();
                        }
                    });
            }

            const areaSelect = document.querySelector('select[name="area_responsible_id"]');
            if (areaSelect) {
                areaSelect.addEventListener('change', function() {
                    loadResponsibleBlocks(this.value);
                });
                if (areaSelect.value) {
                    loadResponsibleBlocks(areaSelect.value);
                }
            }

            // دالة لحفظ الإحداثيات وحساب المساحة الحية عند أي تغيير
            function saveBoundaries(layer) {
                const latlngs = layer.getLatLngs()[0];
                const boundaries = latlngs.map(ll => ({ lat: ll.lat, lng: ll.lng }));
                document.getElementById('boundaries').value = JSON.stringify(boundaries);
                document.getElementById('boundariesError').style.display = 'none';
                document.getElementById('boundariesSuccess').style.display = 'block';

                const liveAreaText = computeGeodesicArea(latlngs);
                const liveBadge = document.getElementById('liveAreaBadge');
                if (liveBadge && liveAreaText) {
                    liveBadge.innerText = liveAreaText;
                }

                checkDelegatesInsideCurrentPolygon();
            }

            // إعداد Leaflet.PM
            map.pm.addControls({
                position: 'topright',
                drawCircle: false,
                drawCircleMarker: false,
                drawMarker: false,
                drawPolyline: false,
                drawRectangle: false,
                drawText: false,
                rotateMode: false,
                cutPolygon: false,
            });

            // رسم المضلع الموجود إذا كان تعديل
            if (isEdit && existingBoundaries && existingBoundaries.length > 0) {
                const latlngs = existingBoundaries.map(p => [parseFloat(p.lat), parseFloat(p.lng)]);
                
                currentPolygon = L.polygon(latlngs, {
                    color: document.getElementById('color').value,
                    fillColor: document.getElementById('color').value,
                    fillOpacity: 0.3,
                    pmIgnore: false // السماح بتعديل هذا المضلع
                }).addTo(map);

                // حفظ الحدود للتأكد
                saveBoundaries(currentPolygon);
                map.fitBounds(currentPolygon.getBounds());
                
                // تفعيل التعديل والسحب للمضلع الحالي مباشرة
                // currentPolygon.pm.enable(); // اختيارياً يمكن تفعيل وضع التعديل فوراً
            } else if (existingGroup.getLayers().length) {
                map.fitBounds(existingGroup.getBounds());
            }

            // الاستماع لأحداث الإنشاء (عند رسم مضلع جديد)
            map.on('pm:create', (e) => {
                // حذف المضلع القديم إذا وجد (لأننا نسمح بمنطقة واحدة فقط)
                if (currentPolygon && currentPolygon !== e.layer) {
                    map.removeLayer(currentPolygon);
                }

                currentPolygon = e.layer;
                currentPolygon.setStyle({
                    color: document.getElementById('color').value,
                    fillColor: document.getElementById('color').value
                });

                saveBoundaries(currentPolygon);

                // إضافة مستمعات الأحداث للمضلع الجديد
                currentPolygon.on('pm:edit', (x) => saveBoundaries(x.layer));
                currentPolygon.on('pm:dragend', (x) => saveBoundaries(x.layer));
                currentPolygon.on('pm:rotateend', (x) => saveBoundaries(x.layer));
                currentPolygon.on('pm:markerdragend', (x) => saveBoundaries(x.layer)); // عند تحريك نقطة
            });

            // إذا كان هناك مضلع حالي، أضف مستمعات الأحداث له أيضاً
            if (currentPolygon) {
                currentPolygon.on('pm:edit', (e) => saveBoundaries(e.layer));
                currentPolygon.on('pm:dragend', (e) => saveBoundaries(e.layer));
                currentPolygon.on('pm:markerdragend', (e) => saveBoundaries(e.layer));
            }

            // تحديث اللون عند تغييره من الإدخال
            const colorInput = document.getElementById('color');
            const colorPreview = document.getElementById('colorPreview');
            colorPreview.style.backgroundColor = colorInput.value;

            colorInput.addEventListener('change', function () {
                const color = this.value;
                colorPreview.style.backgroundColor = color;

                if (currentPolygon) {
                    currentPolygon.setStyle({ color: color, fillColor: color });
                }
                
                map.pm.setGlobalOptions({ 
                    pathOptions: { 
                        color: color, 
                        fillColor: color 
                    } 
                });
            });
            
            // عند الحذف
            map.on('pm:remove', (e) => {
                if(e.layer === currentPolygon) {
                    currentPolygon = null;
                    document.getElementById('boundaries').value = '';
                    document.getElementById('boundariesError').style.display = 'block';
                    document.getElementById('boundariesSuccess').style.display = 'none';
                }
            });

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const boundaries = document.getElementById('boundaries').value;
                    if (!boundaries || boundaries === '' || boundaries === '[]') {
                        e.preventDefault();
                        document.getElementById('boundariesError').style.display = 'block';
                        document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }
        });
    </script>
@endpush
