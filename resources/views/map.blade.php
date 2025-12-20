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
            تعرض هذه الخريطة التفاعلية جميع المناطق المسجلة في نظام الجمعية، مع حدود كل منطقة كما حددها فريق العمل،
            مما يساعد في متابعة نطاق التغطية الإغاثية وتوزيع الخدمات بشكل بصري واضح.
        </p>
        <p class="styled-text">
            يمكنكم تحريك الخريطة والتقريب لعرض تفاصيل كل منطقة، والضغط على أي مضلع لمعرفة اسم المنطقة المرتبطة به.
        </p>

        <!-- خريطة المناطق -->
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
        // تجهيز بيانات المناطق للخريطة
        $regionsForMap = $regions->map(function ($r) {
            return [
                'id'         => $r->id,
                'name'       => $r->name,
                'color'      => $r->color ?? '#FF0000',
                'boundaries' => $r->boundaries ?? [],
            ];
        })->values();
    @endphp

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regions = @json($regionsForMap);

            const map = L.map('map').setView([31.3461, 34.3064], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const group = L.featureGroup().addTo(map);

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
</body>
</html>
