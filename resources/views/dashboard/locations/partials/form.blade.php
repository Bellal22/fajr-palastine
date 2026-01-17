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
                    <select name="area_responsible_id" id="area_responsible_id" class="form-control" required
                        {{ !isset($location) ? 'disabled' : '' }}>
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

{{-- الخريطة --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-map"></i> @lang('locations.sections.map')
        </h5>
    </div>
    <div class="card-body">
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
        <div id="map"></div>

        <input type="hidden" name="latitude" id="latitude"
               value="{{ old('latitude', $location->latitude ?? '') }}" required>
        <input type="hidden" name="longitude" id="longitude"
               value="{{ old('longitude', $location->longitude ?? '') }}" required>

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
            let blockMarker = null;
            let regionPolygon = null;

            // إذا كان Edit وفيه إحداثيات موجودة
            if (isEdit && existingLat && existingLng) {
                currentMarker = L.marker([existingLat, existingLng], {
                    draggable: true,
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('🔴 موقع اللوكيشن').openPopup();

                saveCoordinates(existingLat, existingLng);

                currentMarker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    saveCoordinates(position.lat, position.lng);
                });
            }

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
                            areaSelect.innerHTML = `<option value="${data.area_responsible.id}">${data.area_responsible.name}</option>`;
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

                            if (regionPolygon) map.removeLayer(regionPolygon);

                            if (data.region.boundaries && data.region.boundaries.length > 0) {
                                regionPolygon = L.polygon(data.region.boundaries, {
                                    color: data.region.color || '#3388ff',
                                    weight: 3,
                                    fillOpacity: 0.2
                                }).addTo(map).bindPopup(data.region.name);
                            }
                        }
                    });
            });

            // تحميل البلوكات
            function loadBlocksByAreaResponsible(areaResponsibleId) {
                const blockSelect = document.getElementById('block_id');
                blockSelect.innerHTML = '<option value="">جاري التحميل...</option>';

                fetch(`${blocksUrl}?area_responsible_id=${areaResponsibleId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.blocks && data.blocks.length > 0) {
                            blockSelect.innerHTML = '<option value="">-- اختر المندوب --</option>';
                            data.blocks.forEach(block => {
                                const option = document.createElement('option');
                                option.value = block.id;
                                option.textContent = `${block.name ?? ''} - ${block.title ?? ''}`.trim();
                                option.dataset.lat = block.lat || 0;
                                option.dataset.lng = block.lng || block.lan || 0;
                                option.dataset.phone = block.phone || '';
                                option.dataset.address = block.title || '';
                                blockSelect.appendChild(option);
                            });
                            blockSelect.disabled = false;
                        }
                    });
            }

            // اختيار البلوك
            document.getElementById('block_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!this.value) return;

                const lat = parseFloat(selectedOption.dataset.lat);
                const lng = parseFloat(selectedOption.dataset.lng);

                if (blockMarker) map.removeLayer(blockMarker);

                blockMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: "{{ asset('icons/person-marker.png') }}",
                        iconSize: [40, 40],
                        iconAnchor: [20, 40]
                    })
                }).addTo(map).bindPopup('📍 موقع المندوب').openPopup();

                map.setView([lat, lng], 15);

                if (!isEdit || !existingLat) {
                    if (currentMarker) map.removeLayer(currentMarker);

                    currentMarker = L.marker([lat, lng], {
                        draggable: true,
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map).bindPopup('🔴 موقع اللوكيشن');

                    saveCoordinates(lat, lng);

                    currentMarker.on('dragend', function(e) {
                        const pos = e.target.getLatLng();
                        saveCoordinates(pos.lat, pos.lng);
                    });
                }
            });

            // حفظ الإحداثيات
            function saveCoordinates(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                document.getElementById('latDisplay').textContent = lat.toFixed(6);
                document.getElementById('lngDisplay').textContent = lng.toFixed(6);
                document.getElementById('coordinatesDisplay').style.display = 'block';
                document.getElementById('coordinatesError').style.display = 'none';
            }

            // معاينة اللون
            const iconColor = document.getElementById('icon_color');
            const iconPreview = document.getElementById('iconPreview');
            iconPreview.style.backgroundColor = iconColor.value;
            iconColor.addEventListener('input', function() {
                iconPreview.style.backgroundColor = this.value;
            });

            // Validation قبل الإرسال
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    if (!lat || !lng) {
                        e.preventDefault();
                        document.getElementById('coordinatesError').style.display = 'block';
                        document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            // تحميل البلوكات في حالة Edit
            @if(isset($location) && $location->area_responsible_id)
                loadBlocksByAreaResponsible({{ $location->area_responsible_id }});
            @endif
        });
    </script>
@endpush
