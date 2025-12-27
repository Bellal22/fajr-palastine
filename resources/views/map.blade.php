<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خريطة تغطية جمعية الفجر الشبابي الفلسطيني</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(
                        to bottom,
                        rgba(255, 255, 255, 0.8),
                        rgb(238, 178, 129)
                    ),
                    url({{ asset('background/image.jpg') }}) center center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
        }

        h1 {
            color: #FF6F00;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 100%;
            max-width: 150px;
            height: auto;
        }

        p {
            text-align: right;
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .styled-text {
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }

        #map {
            height: 500px;
            width: 100%;
            border-radius: 15px;
            border: 2px solid #ddd;
            margin-top: 15px;
        }

        .buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .link-btn {
            display: inline-block;
            background-color: #FF6F00;
            color: white;
            padding: 10px 18px;
            font-size: 1rem;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .link-btn:hover {
            background-color: #E65100;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- الشعار -->
        <div class="logo-container">
            <img src="{{ asset('background/image.jpg') }}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>

        <!-- العنوان الرئيسي -->
        <h1>خريطة تغطية جمعية الفجر الشبابي الفلسطيني</h1>

        <!-- وصف عن الخريطة -->
        <p>
            تعرض هذه الخريطة التفاعلية جميع المناطق المسجلة في نظام الجمعية مع حدودها،
            إضافة إلى مواقع اللوكيشنات الميدانية كنقاط (دبابيس).
        </p>

        <!-- الخريطة -->
        <div id="map"></div>

        <!-- روابط مساعدة -->
        <div class="buttons-container">
            <a href="{{ url('/') }}" class="link-btn">
                <i class="fas fa-home ms-1"></i> العودة للصفحة الرئيسية
            </a>
            <a href="{{ route('complaint') }}" class="link-btn">
                <i class="fas fa-exclamation-circle ms-1"></i> الانتقال لصفحة الشكاوى
            </a>
        </div>
    </div>

    @php
        $regionsForMap = $regions->map(function ($r) {
            return [
                'id'         => $r->id,
                'name'       => $r->name,
                'color'      => $r->color ?? '#FF0000',
                'boundaries' => $r->boundaries ?? [],
            ];
        })->values();

        $locationsForMap = $locations->map(function ($l) {
            return [
                'id'          => $l->id,
                'name'        => $l->name,
                'lat'         => (float) $l->latitude,
                'lng'         => (float) $l->longitude,
                'icon_color'  => $l->icon_color ?? '#e74c3c',
                'address'     => $l->address ?? null,
                'phone'       => $l->phone ?? null,
                'region_name' => optional($l->region)->name,
            ];
        })->values();
    @endphp

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regions   = @json($regionsForMap);
            const locations = @json($locationsForMap);

            const map = L.map('map').setView([31.3461, 34.3064], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const group = L.featureGroup().addTo(map);
            const regionPolygons = [];

            // رسم المناطق
            regions.forEach(function (region) {
                if (!region.boundaries || !region.boundaries.length) return;

                const latlngs = region.boundaries.map(function (p) {
                    return [parseFloat(p.lat), parseFloat(p.lng)];
                });

                const polygon = L.polygon(latlngs, {
                    color: region.color,
                    fillColor: region.color,
                    fillOpacity: 0.3
                }).addTo(group);

                polygon.bindPopup(`<strong>${region.name}</strong>`);

                regionPolygons.push({
                    id: region.id,
                    name: region.name,
                    polygon: polygon
                });
            });

            // رسم اللوكيشنات كدبابيس عادية بلون من الداتابيز (glow)
            locations.forEach(function (loc) {
                if (!loc.lat || !loc.lng) return;

                const color = loc.icon_color || '#e74c3c';

                const pinIcon = L.icon({
                    iconUrl: "{{ asset('icons/person-marker.png') }}",
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    iconSize:     [40, 40],
                    iconAnchor:   [12, 41],
                    popupAnchor:  [1, -34],
                    shadowSize:   [41, 41],
                });

                const marker = L.marker([loc.lat, loc.lng], { icon: pinIcon }).addTo(group);

                marker.on('add', () => {
                    if (marker._icon) {
                        marker._icon.style.filter =
                            `drop-shadow(0 0 0 ${color}) drop-shadow(0 0 5px ${color})`;
                    }
                });

                const popupHtml = `
                    <div style="min-width: 220px; font-size: 13px;">
                        <h6 style="color: ${color}; margin-bottom: 6px;">
                            <i class="fas fa-map-marker-alt ms-1"></i> ${loc.name}
                        </h6>
                        ${loc.region_name ? `<div class="small mb-1">المنطقة: ${loc.region_name}</div>` : ''}
                        ${loc.address ? `<div class="small mb-1"><i class="fas fa-map-pin ms-1"></i>${loc.address}</div>` : ''}
                        ${loc.phone ? `<div class="small mb-1"><i class="fas fa-phone ms-1"></i>${loc.phone}</div>` : ''}
                    </div>
                `;
                marker.bindPopup(popupHtml);
            });

            if (group.getLayers().length) {
                map.fitBounds(group.getBounds());
            }

            // تحديد موقع المستخدم مباشرة
            autoLocateUser(map, regionPolygons);
        });

        function autoLocateUser(map, regionPolygons) {
            if (!navigator.geolocation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'المتصفح لا يدعم تحديد الموقع الجغرافي.',
                });
                return;
            }

            console.log('سيتم طلب إذن تحديد الموقع من المتصفح الآن.');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    // أيقونة الشخص: ضع ملف PNG في public/icons/person-marker.png
                    const userIcon = L.icon({
                        iconUrl: "{{ asset('icons/marker-p.png') }}",
                        iconSize:     [40, 40],  // عدّل حسب أبعاد الصورة
                        iconAnchor:   [20, 40],  // أسفل منتصف الأيقونة
                        popupAnchor:  [0, -40],
                        shadowUrl:    'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                        shadowSize:   [41, 41],
                        shadowAnchor: [12, 41],
                    });

                    L.marker([lat, lng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup('هذا هو موقعك الحالي تقريباً')
                        .openPopup();

                    map.setView([lat, lng], 14);

                    const point = L.latLng(lat, lng);
                    let foundRegion = null;

                    regionPolygons.forEach(r => {
                        if (!foundRegion && r.polygon.getBounds().contains(point)) {
                            if (pointInPolygon(point, r.polygon)) {
                                foundRegion = r;
                            }
                        }
                    });

                    if (foundRegion) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم تحديد موقعك',
                            html: `أنت حالياً ضمن منطقة: <b>${foundRegion.name}</b>`,
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'خارج نطاق التغطية',
                            text: 'موقعك الحالي لا يقع داخل أي منطقة مسجلة في نظام الجمعية.',
                            confirmButtonText: 'فهمت'
                        });
                    }
                },
                function (err) {
                    console.warn('Geolocation error:', err);
                    let msg = 'تعذر الحصول على موقعك.';
                    if (err.code === 1) {
                        msg = 'رفضت إذن تحديد الموقع من المتصفح. الرجاء السماح للموقع باستخدام الموقع الجغرافي ثم إعادة تحميل الصفحة.';
                    } else if (err.code === 2) {
                        msg = 'تعذر الحصول على موقعك. تأكد من تفعيل خدمة الموقع (GPS) أو الاتصال بالإنترنت.';
                    } else if (err.code === 3) {
                        msg = 'انتهت مهلة طلب الموقع. حاول إعادة تحميل الصفحة.';
                    } else if (err.message) {
                        msg = err.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في تحديد الموقع',
                        text: msg,
                        confirmButtonText: 'حسناً'
                    });
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }

        // فحص إذا كانت النقطة داخل البوليجون
        function pointInPolygon(point, polygon) {
            const x = point.lng;
            const y = point.lat;
            const vs = polygon.getLatLngs()[0];

            let inside = false;
            for (let i = 0, j = vs.length - 1; i < vs.length; j = i++) {
                const xi = vs[i].lng, yi = vs[i].lat;
                const xj = vs[j].lng, yj = vs[j].lat;

                const intersect = ((yi > y) !== (yj > y)) &&
                    (x < (xj - xi) * (y - yi) / ((yj - yi) || 1e-10) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }
    </script>
</body>
</html>
