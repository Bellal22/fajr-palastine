<x-layout :title="trans('locations.actions.create')" :breadcrumbs="['dashboard.locations.create']">

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

        <style>
            #map {
                height: 500px;
                width: 100%;
                border-radius: 8px;
                border: 2px solid #ddd;
                margin-top: 15px;
            }

            .map-instructions {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
                border-right: 4px solid #007bff;
            }

            .icon-preview {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 2px solid #ddd;
                display: inline-block;
                vertical-align: middle;
                margin-left: 10px;
            }

            .form-section {
                background: white;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .location-types {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
            }

            .location-type-card {
                border: 2px solid #ddd;
                border-radius: 8px;
                padding: 15px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s;
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

            #area_responsible_id:disabled,
            #block_id:disabled {
                background-color: #f8f9fa;
                cursor: not-allowed;
            }

            #area_responsible_info {
                margin-top: 5px;
                color: #6c757d;
            }

            .loading-spinner {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid rgba(0, 0, 0, 0.3);
                border-radius: 50%;
                border-top-color: #007bff;
                animation: spin 1s ease-in-out infinite;
                margin-right: 8px;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>
    @endpush

    {{ BsForm::resource('locations')->post(route('dashboard.locations.store'), ['id' => 'locationForm']) }}
        @component('dashboard::components.box')
            @slot('title', trans('locations.actions.create'))

            {{-- معلومات اللوكيشن الأساسية --}}
            <div class="form-section">
                <h5 class="mb-3">معلومات اللوكيشن</h5>

                <div class="row">
                    {{-- اسم اللوكيشن --}}
                    <div class="col-md-6">
                        {{ BsForm::text('name')
                            ->label('اسم اللوكيشن')
                            ->attribute('required', true)
                            ->value(old('name')) }}
                    </div>

                    {{-- نوع اللوكيشن --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>نوع اللوكيشن <span class="text-danger">*</span></label>
                            <div class="location-types">
                                <div class="location-type-card{{ old('type') == 'house' ? ' active' : '' }}" data-type="house">
                                    <i class="fas fa-home"></i>
                                    <div>منزل</div>
                                </div>
                                <div class="location-type-card{{ old('type') == 'shelter' ? ' active' : '' }}" data-type="shelter">
                                    <i class="fas fa-warehouse"></i>
                                    <div>ملجأ</div>
                                </div>
                                <div class="location-type-card{{ old('type') == 'center' ? ' active' : '' }}" data-type="center">
                                    <i class="fas fa-building"></i>
                                    <div>مركز</div>
                                </div>
                                <div class="location-type-card{{ old('type', 'other') == 'other' ? ' active' : '' }}" data-type="other">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>أخرى</div>
                                </div>
                            </div>
                            <input type="hidden" name="type" id="type" value="{{ old('type', 'other') }}" required>
                            @error('type')
                                <span class="text-danger d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- المنطقة → مسؤول المنطقة → البلوك --}}
                <div class="row">
                    {{-- المنطقة (أول حاجة) --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="region_id">المنطقة <span class="text-danger">*</span></label>
                            <select name="region_id"
                                    id="region_id"
                                    class="form-control @error('region_id') is-invalid @enderror"
                                    required>
                                <option value="">-- اختر المنطقة --</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- مسؤول المنطقة --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="area_responsible_id">مسؤول المنطقة <span class="text-danger">*</span></label>
                            <select name="area_responsible_id"
                                    id="area_responsible_id"
                                    class="form-control @error('area_responsible_id') is-invalid @enderror"
                                    required
                                    disabled>
                                <option value="">-- اختر المنطقة أولاً --</option>
                            </select>
                            @error('area_responsible_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="text-muted" id="area_responsible_info" style="display: none;">
                                <i class="fas fa-phone"></i> <span id="area_responsible_phone"></span>
                            </small>
                        </div>
                    </div>

                    {{-- المندوب/البلوك --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="block_id">المندوب/البلوك <span class="text-danger">*</span></label>
                            <select name="block_id"
                                    id="block_id"
                                    class="form-control @error('block_id') is-invalid @enderror"
                                    required
                                    disabled>
                                <option value="">-- اختر مسؤول المنطقة أولاً --</option>
                            </select>
                            @error('block_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- العنوان --}}
                    <div class="col-md-6">
                        {{ BsForm::text('address')
                            ->label('العنوان')
                            ->value(old('address')) }}
                    </div>

                    {{-- رقم الهاتف --}}
                    <div class="col-md-3">
                        {{ BsForm::text('phone')
                            ->label('رقم الهاتف')
                            ->value(old('phone')) }}
                    </div>

                    {{-- لون الأيقونة --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="icon_color">لون الأيقونة</label>
                            <div class="d-flex align-items-center">
                                <input type="color"
                                       name="icon_color"
                                       id="icon_color"
                                       class="form-control"
                                       value="{{ old('icon_color', '#9C27B0') }}"
                                       style="width: 100px; height: 45px;">
                                <div class="icon-preview" id="iconPreview"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الوصف --}}
                {{ BsForm::textarea('description')
                    ->label('وصف اللوكيشن')
                    ->rows(3)
                    ->value(old('description')) }}
            </div>

            {{-- الخريطة --}}
            <div class="form-section">
                <h5 class="mb-3">تحديد الموقع على الخريطة</h5>

                <div class="map-instructions">
                    <i class="fas fa-info-circle"></i>
                    <strong>تعليمات:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>الخطوة 1:</strong> اختر المنطقة - سيظهر مسؤول المنطقة تلقائياً</li>
                        <li><strong>الخطوة 2:</strong> ستظهر البلوكات التابعة للمسؤول</li>
                        <li><strong>الخطوة 3:</strong> اختر المندوب/البلوك - سيظهر موقعه بعلامة زرقاء</li>
                        <li><strong>الخطوة 4:</strong> اضغط على الخريطة لتحديد موقع اللوكيشن (علامة حمراء)</li>
                        <li><strong>الخطوة 5:</strong> يمكنك سحب العلامة الحمراء لتعديل الموقع</li>
                        <li><strong>الخطوة 6:</strong> الإحداثيات ستتم حفظها تلقائياً عند سحب العلامة</li>
                    </ul>
                </div>

                <div id="map"></div>

                {{-- حقول مخفية للإحداثيات --}}
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
                @error('latitude')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror

                <div id="coordinatesError" class="text-danger mt-2" style="display: none;">
                    يجب تحديد الموقع على الخريطة
                </div>

                <div id="coordinatesDisplay" class="mt-2 text-muted" style="display: none;">
                    <i class="fas fa-map-marker-alt"></i>
                    الإحداثيات: <span id="latDisplay"></span>, <span id="lngDisplay"></span>
                </div>
            </div>

            @slot('footer')
                {{ BsForm::submit()->label(trans('locations.actions.save')) }}
            @endslot
        @endcomponent
    {{ BsForm::close() }}

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('✅ Page loaded - Location Create Map Ready!');

                // ✅ Laravel Route Helpers - مضمون 100%
                const areaResponsibleUrl = '{{ route("dashboard.locations.area-responsible-by-region") }}';
                const blocksUrl = '{{ route("dashboard.locations.blocks-by-area-responsible") }}';
                const regionBoundariesUrl = '{{ route("dashboard.locations.region-boundaries") }}';

                console.log('📍 URLs:', { areaResponsibleUrl, blocksUrl, regionBoundariesUrl });

                // إعداد الخريطة
                const map = L.map('map').setView([31.3461, 34.3064], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19
                }).addTo(map);

                let currentMarker = null;
                let blockMarker = null;
                let regionPolygon = null;

                // ✅ دوال مساعدة
                function showLoading(element, message = 'جاري التحميل...') {
                    element.innerHTML = `<option value="">${message}</option>`;
                    element.disabled = true;
                }

                function hideLoading(element) {
                    element.disabled = false;
                }

                // ✅ تحديد الخريطة على المنطقة + لونها من الداتابيز 🌈
                function centerMapOnRegion(centerLat, centerLng, boundaries = null, regionName = '', regionColor = '#3388ff') {
                    console.log('🗺️ Centering map on region:', regionName, { centerLat, centerLng, regionColor });

                    // إزالة حدود المنطقة السابقة
                    if (regionPolygon) {
                        map.removeLayer(regionPolygon);
                        regionPolygon = null;
                    }

                    // تحديد مركز الخريطة
                    map.setView([centerLat, centerLng], 14);

                    // إضافة حدود المنطقة بلونها الخاص من الداتابيز
                    if (boundaries && Array.isArray(boundaries) && boundaries.length > 0) {
                        console.log('📐 Adding region boundaries with color:', regionColor, boundaries.length, 'points');
                        regionPolygon = L.polygon(boundaries, {
                            color: regionColor,           // ✅ لون الحدود من الداتابيز
                            weight: 3,
                            fillColor: regionColor,       // ✅ لون التعبئة من الداتابيز
                            fillOpacity: 0.25,            // ✅ شفافية مثالية
                            stroke: true
                        }).addTo(map)
                        .bindPopup(`<b style="color: ${regionColor} !important;">${regionName}</b><br>حدود المنطقة`)
                        .openPopup();
                    }
                }

                // ✅ اختيار نوع اللوكيشن
                document.querySelectorAll('.location-type-card').forEach(card => {
                    card.addEventListener('click', function() {
                        document.querySelectorAll('.location-type-card').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('type').value = this.dataset.type;
                        console.log('🏠 Location type selected:', this.dataset.type);
                    });
                });

                // ✅ عند اختيار المنطقة - التحديث الكامل 🗺️🌈
                document.getElementById('region_id').addEventListener('change', function() {
                    const regionId = this.value;
                    const areaResponsibleSelect = document.getElementById('area_responsible_id');
                    const blockSelect = document.getElementById('block_id');

                    console.log('🌍 Region selected:', regionId);

                    // إعادة تعيين كل شيء
                    showLoading(areaResponsibleSelect);
                    showLoading(blockSelect, '-- اختر مسؤول المنطقة أولاً --');
                    document.getElementById('area_responsible_info').style.display = 'none';

                    // إزالة كل العلامات والحدود
                    if (blockMarker) {
                        map.removeLayer(blockMarker);
                        blockMarker = null;
                    }
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                        currentMarker = null;
                    }
                    if (regionPolygon) {
                        map.removeLayer(regionPolygon);
                        regionPolygon = null;
                    }

                    // إعادة تعيين الحقول
                    document.getElementById('address').value = '';
                    document.getElementById('phone').value = '';
                    document.getElementById('latitude').value = '';
                    document.getElementById('longitude').value = '';
                    document.getElementById('coordinatesDisplay').style.display = 'none';
                    document.getElementById('coordinatesError').style.display = 'none';

                    if (!regionId) {
                        map.setView([31.3461, 34.3064], 13);
                        hideLoading(areaResponsibleSelect);
                        hideLoading(blockSelect);
                        return;
                    }

                    // 1️⃣ جلب بيانات المنطقة وتحديد الخريطة + اللون 🌈
                    const regionUrl = new URL(regionBoundariesUrl, window.location.origin);
                    regionUrl.searchParams.set('region_id', regionId);

                    console.log('🗺️ Fetching Region Boundaries:', regionUrl.toString());

                    fetch(regionUrl.toString())
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ Region Data:', data);
                            if (data.success && data.region) {
                                const centerLat = parseFloat(data.region.center_lat);
                                const centerLng = parseFloat(data.region.center_lng);
                                const boundaries = data.region.boundaries;
                                const regionColor = data.region.color || '#3388ff'; // ✅ اللون من الداتابيز

                                centerMapOnRegion(centerLat, centerLng, boundaries, data.region.name, regionColor);
                            }
                        })
                        .catch(error => {
                            console.error('❌ Region boundaries error:', error);
                            centerMapOnRegion(31.3461, 34.3064);
                        });

                    // 2️⃣ جلب مسؤول المنطقة
                    const areaUrl = new URL(areaResponsibleUrl, window.location.origin);
                    areaUrl.searchParams.set('region_id', regionId);

                    console.log('📡 Fetching AreaResponsible:', areaUrl.toString());

                    fetch(areaUrl.toString())
                        .then(response => {
                            console.log('📡 Response status:', response.status);
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ AreaResponsible Data:', data);

                            if (data.success && data.area_responsible && data.area_responsible.id) {
                                areaResponsibleSelect.innerHTML = `
                                    <option value="${data.area_responsible.id}">
                                        ${data.area_responsible.name}
                                    </option>
                                `;
                                areaResponsibleSelect.value = data.area_responsible.id;
                                hideLoading(areaResponsibleSelect);

                                if (data.area_responsible.phone) {
                                    document.getElementById('area_responsible_phone').textContent = data.area_responsible.phone;
                                    document.getElementById('area_responsible_info').style.display = 'block';
                                }

                                // تحميل البلوكات
                                loadBlocksByAreaResponsible(data.area_responsible.id);
                            } else {
                                areaResponsibleSelect.innerHTML = `<option value="">${data.message || 'لا يوجد مسؤول'}</option>`;
                                hideLoading(areaResponsibleSelect);
                                blockSelect.innerHTML = '<option value="">اختر مسؤول منطقة أولاً</option>';
                            }
                        })
                        .catch(error => {
                            console.error('💥 AreaResponsible Error:', error);
                            areaResponsibleSelect.innerHTML = '<option value="">حدث خطأ في التحميل</option>';
                            hideLoading(areaResponsibleSelect);
                        });
                });

                // ✅ تحميل البلوكات
                function loadBlocksByAreaResponsible(areaResponsibleId) {
                    const blockSelect = document.getElementById('block_id');
                    showLoading(blockSelect, 'جاري تحميل المندوبين...');

                    const url = new URL(blocksUrl, window.location.origin);
                    url.searchParams.set('area_responsible_id', areaResponsibleId);

                    console.log('📦 Fetching Blocks:', url.toString());

                    fetch(url.toString())
                        .then(response => {
                            console.log('📦 Blocks response status:', response.status);
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ Blocks data:', data);

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
                                hideLoading(blockSelect);
                            } else {
                                blockSelect.innerHTML = `<option value="">${data.message || 'لا يوجد مندوبين'}</option>`;
                                hideLoading(blockSelect);
                            }
                        })
                        .catch(error => {
                            console.error('❌ Blocks Error:', error);
                            blockSelect.innerHTML = '<option value="">حدث خطأ في التحميل</option>';
                            hideLoading(blockSelect);
                        });
                }

                // ✅ عند اختيار المندوب/البلوك
                document.getElementById('block_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (!this.value) return;

                    const lat = parseFloat(selectedOption.dataset.lat) || 31.3461;
                    const lng = parseFloat(selectedOption.dataset.lng) || 34.3064;
                    const phone = selectedOption.dataset.phone || '';
                    const address = selectedOption.dataset.address || '';

                    console.log('🔵 Block selected:', { lat, lng, phone, address });

                    if (blockMarker) {
                        map.removeLayer(blockMarker);
                    }

                    // علامة المندوب الزرقاء
                    blockMarker = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: "{{ asset('icons/person-marker.png') }}",
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [40, 40],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map)
                    .bindPopup(`
                        <b>📍 موقع المندوب</b><br>
                        ${selectedOption.textContent}<br>
                        ${phone ? `📞 ${phone}<br>` : ''}${address ? `📍 ${address}` : ''}
                    `)
                    .openPopup();

                    map.setView([lat, lng], 15);

                    // تعبئة الحقول تلقائياً
                    document.getElementById('address').value = address;
                    document.getElementById('phone').value = phone;
                    document.getElementById('latitude').value = lat.toFixed(8);
                    document.getElementById('longitude').value = lng.toFixed(8);
                    document.getElementById('coordinatesDisplay').style.display = 'block';
                    document.getElementById('coordinatesError').style.display = 'none';

                    if (phone || address) {
                        document.getElementById('area_responsible_info').style.display = 'block';
                        document.getElementById('area_responsible_phone').textContent = phone || 'غير متوفر';
                    }

                    // علامة اللوكيشن الحمراء (قابلة للسحب)
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }

                    currentMarker = L.marker([lat, lng], {
                        draggable: true,
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map)
                    .bindPopup('🔴 موقع اللوكيشن<br><small>(اسحب العلامة لتعديل الموقع)</small>')
                    .openPopup();

                    saveCoordinates(lat, lng);

                    currentMarker.on('dragend', function(e) {
                        const position = e.target.getLatLng();
                        console.log('🖱️ Marker dragged to:', position);
                        saveCoordinates(position.lat, position.lng);
                    });
                });

                // ✅ حفظ الإحداثيات
                function saveCoordinates(lat, lng) {
                    document.getElementById('latitude').value = lat.toFixed(8);
                    document.getElementById('longitude').value = lng.toFixed(8);
                    document.getElementById('coordinatesError').style.display = 'none';

                    document.getElementById('latDisplay').textContent = lat.toFixed(6);
                    document.getElementById('lngDisplay').textContent = lng.toFixed(6);
                    document.getElementById('coordinatesDisplay').style.display = 'block';
                }

                // ✅ معاينة لون الأيقونة
                const iconColorInput = document.getElementById('icon_color');
                const iconPreview = document.getElementById('iconPreview');
                if (iconPreview && iconColorInput) {
                    iconPreview.style.backgroundColor = iconColorInput.value;
                    iconColorInput.addEventListener('change', function() {
                        iconPreview.style.backgroundColor = this.value;
                    });
                }

                // ✅ التحقق قبل الإرسال
                document.getElementById('locationForm').addEventListener('submit', function(e) {
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;

                    if (!lat || !lng) {
                        e.preventDefault();
                        document.getElementById('coordinatesError').style.display = 'block';
                        document.getElementById('map').scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        console.log('❌ Coordinates missing!');
                        return false;
                    }
                    console.log('✅ Form valid - submitting...');
                });

                console.log('🎉 Location Map Script Loaded Successfully! 🌈🗺️');
            });

        </script>
    @endpush

</x-layout>
