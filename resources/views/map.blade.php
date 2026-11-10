<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خريطة تغطية جمعية الفجر الشبابي الفلسطيني</title>

    <!-- Google Fonts: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            color: #0f172a;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #fff7ed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            padding: 1.8rem;
            border-radius: 24px;
            width: 98%;
            max-width: 1800px;
            box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.12), 0 0 2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            margin: 20px auto;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .top-header {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            margin: 6px 0;
            letter-spacing: -0.5px;
        }

        h1 span {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 6px;
        }

        .logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid #ea580c;
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.25);
            transition: transform 0.4s ease;
        }

        .logo:hover {
            transform: rotate(360deg) scale(1.08);
        }

        .description-text {
            text-align: center;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* Map Container */
        .map-relative-wrapper {
            position: relative;
            width: 100%;
            height: 750px;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 15px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Right Floating Sidebar Panel */
        .floating-sidebar-right {
            position: absolute;
            top: 20px;
            right: 20px;
            bottom: 20px;
            width: 410px;
            max-width: calc(100% - 40px);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.25), 0 0 1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.9);
            animation: slideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        .sidebar-header-bar {
            padding: 18px 22px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header-title .header-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #fff7ed;
            color: #ea580c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .close-sidebar-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .close-sidebar-btn:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            transform: rotate(90deg) scale(1.05);
        }

        .sidebar-scroll-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .sidebar-scroll-body::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll-body::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        /* Hero Card Header */
        .hero-banner-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 16px;
            padding: 20px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
        }

        .hero-banner-card::before {
            content: '';
            position: absolute;
            top: -40px;
            left: -40px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-banner-content {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 2;
        }

        .hero-avatar-ring {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #f97316;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-title-text {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 4px 0;
            line-height: 1.3;
            color: #ffffff;
        }

        .hero-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
        }

        /* Section Titles */
        .section-label {
            font-size: 13px;
            font-weight: 800;
            color: #334155;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section-label:first-child {
            margin-top: 0;
        }

        .section-label .label-line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* Stat Cards Grid */
        .metrics-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 14px 12px;
            box-shadow: 0 4px 15px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -5px rgba(15, 23, 42, 0.1);
            border-color: #cbd5e1;
        }

        .metric-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .metric-icon-box {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .bg-orange-soft { background: #fff7ed; color: #ea580c; }
        .bg-green-soft  { background: #f0fff4; color: #16a34a; }
        .bg-amber-soft  { background: #fefce8; color: #d97706; }
        .bg-blue-soft   { background: #eff6ff; color: #2563eb; }
        .bg-indigo-soft { background: #e0e7ff; color: #4f46e5; }

        .metric-card-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            line-height: 1.4;
        }

        .metric-card-val {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        /* Info Row Items */
        .info-pill-item {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .info-pill-item:hover {
            border-color: #cbd5e1;
            background: #fafafa;
        }

        .info-pill-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #ea580c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .info-pill-content {
            display: flex;
            flex-direction: column;
        }

        .info-pill-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
        }

        .info-pill-val {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Bottom Buttons */
        .buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 22px;
        }

        .link-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: white;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 18px rgba(234, 88, 12, 0.35);
            transition: all 0.3s ease;
        }

        .link-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.45);
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            .map-relative-wrapper {
                height: 550px;
            }

            .floating-sidebar-right {
                top: 12px;
                right: 12px;
                left: 12px;
                bottom: 12px;
                width: auto;
                max-width: 100%;
            }

            h1 {
                font-size: 20px;
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
        <div class="top-header">
            <h1>جمعية <span>الفجر الشبابي الفلسطيني</span></h1>
            <p class="description-text">
                خريطة التغطية التفاعلية للمناطق الجغرافية ومواقع المناديب الميدانيين وإحصائيات الأسر والأفراد المعتمدين.
            </p>
        </div>

        <!-- Map wrapper with Right Floating Sidebar -->
        <div class="map-relative-wrapper">
            <!-- Map element -->
            <div id="map"></div>

            <!-- Right Floating Sidebar Panel -->
            <div class="floating-sidebar-right" id="floatingSidebarRight">
                <div class="sidebar-header-bar">
                    <span class="sidebar-header-title">
                        <div class="header-icon-box">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        التفاصيل والإحصائيات
                    </span>
                    <button type="button" class="close-sidebar-btn" id="closeSidebarBtn" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="sidebar-scroll-body" id="floatingSidebarBody">
                    <!-- Dynamic Content via JS -->
                </div>
            </div>
        </div>

        <!-- روابط مساعدة -->
        <div class="buttons-container">
            <a href="{{ url('/') }}" class="link-btn">
                <i class="fas fa-home"></i> العودة للصفحة الرئيسية
            </a>
            <a href="{{ route('complaint') }}" class="link-btn">
                <i class="fas fa-exclamation-circle"></i> الانتقال لصفحة الشكاوى
            </a>
        </div>
    </div>

    @php
        $regionsForMap = $regions->map(function ($r) {
            $resp = $r->areaResponsible;
            
            $approvedFamiliesCount = $resp ? $resp->people()->familyHead()->approved()->count() : 0;
            $approvedIndividualsCount = $resp ? $resp->people()->familyHead()->approved()->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)')) : 0;

            return [
                'id'                         => $r->id,
                'name'                       => $r->name,
                'color'                      => $r->color ?? '#ea580c',
                'boundaries'                 => $r->boundaries ?? [],
                'area_km2'                   => $r->area_km2,
                'area_formatted'             => $r->area_formatted,
                'responsible_name'           => optional($resp)->name ?? $r->name,
                'responsible_phone'          => optional($resp)->phone,
                'blocks_count'               => $resp ? ($resp->blocks_count ?? $resp->blocks()->count()) : 0,
                'approved_families_count'    => (int) $approvedFamiliesCount,
                'approved_individuals_count' => (int) $approvedIndividualsCount,
            ];
        })->values();

        $locationsForMap = $locations->map(function ($l) {
            $block = $l->block;
            $areaResp = $block ? $block->areaResponsible : optional($l->region)->areaResponsible;

            $lat = (float) ($l->latitude ?: optional($block)->lat);
            $lng = (float) ($l->longitude ?: (optional($block)->lng ?: optional($block)->lan));

            $delegateFamiliesCount = $block ? $block->people()->familyHead()->approved()->count() : 0;
            $delegateIndividualsCount = $block ? $block->people()->familyHead()->approved()->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)')) : 0;

            $areaFamiliesCount = $areaResp ? $areaResp->people()->familyHead()->approved()->count() : 0;
            $areaIndividualsCount = $areaResp ? $areaResp->people()->familyHead()->approved()->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)')) : 0;

            return [
                'id'                         => $l->id,
                'name'                       => $l->name,
                'lat'                        => $lat,
                'lng'                        => $lng,
                'icon_color'                 => $l->icon_color ?? '#ea580c',
                'address'                    => $l->address ?? optional($block)->title,
                'phone'                      => $l->phone ?? optional($block)->phone,
                'region_name'                => optional($l->region)->name,
                'delegate_name'              => optional($block)->name ?? $l->name,
                'camp_name'                  => optional($block)->title ?? $l->name,
                'responsible_name'           => optional($areaResp)->name,
                'delegate_families_count'    => (int) $delegateFamiliesCount,
                'delegate_individuals_count' => (int) $delegateIndividualsCount,
                'area_families_count'        => (int) $areaFamiliesCount,
                'area_individuals_count'      => (int) $areaIndividualsCount,
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
            const boundsGroup = L.featureGroup();

            const regionPolygons = [];
            const locationMarkers = [];

            const sidebarPanel = document.getElementById('floatingSidebarRight');
            const sidebarBody  = document.getElementById('floatingSidebarBody');
            const closeBtn     = document.getElementById('closeSidebarBtn');

            closeBtn.addEventListener('click', function () {
                sidebarPanel.style.display = 'none';
            });

            // Draw Regions (Polygons)
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

                boundsGroup.addLayer(polygon);

                polygon.on('click', function () {
                    openRegionSidebar(region);
                });

                regionPolygons.push({
                    id: region.id,
                    polygon: polygon,
                    data: region
                });
            });

            // Draw Locations (Markers)
            locations.forEach(function (loc) {
                if (!loc.lat || !loc.lng) return;

                // Validate coordinates (filter out outlier pins in the ocean/abroad)
                const isValidGazaCoord = (loc.lat >= 31.0 && loc.lat <= 32.2 && loc.lng >= 34.0 && loc.lng <= 34.7);

                const color = loc.icon_color || '#ea580c';

                const pinIcon = L.icon({
                    iconUrl: "{{ asset('icons/person-marker.png') }}",
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    iconSize:     [40, 40],
                    iconAnchor:   [12, 41],
                    popupAnchor:  [1, -34],
                    shadowSize:   [41, 41],
                });

                const marker = L.marker([loc.lat, loc.lng], { icon: pinIcon }).addTo(group);

                if (isValidGazaCoord) {
                    boundsGroup.addLayer(marker);
                }

                marker.on('add', () => {
                    if (marker._icon) {
                        marker._icon.style.filter =
                            `drop-shadow(0 0 0 ${color}) drop-shadow(0 0 5px ${color})`;
                    }
                });

                marker.on('click', function () {
                    openLocationSidebar(loc);
                });

                locationMarkers.push({
                    id: loc.id,
                    marker: marker,
                    data: loc
                });
            });

            // Fit bounds strictly to active coverage area
            function fitMapToCoverage() {
                if (boundsGroup.getLayers().length) {
                    map.fitBounds(boundsGroup.getBounds(), { padding: [30, 30] });
                } else if (group.getLayers().length) {
                    map.fitBounds(group.getBounds(), { padding: [30, 30] });
                }
            }

            fitMapToCoverage();

            // Open Region Sidebar
            function openRegionSidebar(region) {
                const themeColor = region.color || '#ea580c';

                sidebarBody.innerHTML = `
                    <div class="hero-banner-card" style="background: linear-gradient(135deg, ${themeColor} 0%, #0f172a 100%);">
                        <div class="hero-banner-content">
                            <div class="hero-avatar-ring">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h3 class="hero-title-text">${region.responsible_name || region.name}</h3>
                                <div class="hero-pill-badge">
                                    <i class="fas fa-layer-group"></i> منطقة: ${region.name}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-label">
                        <span>إحصائيات مسئول المنطقة</span>
                        <div class="label-line"></div>
                    </div>

                    <div class="metrics-grid-2x2">
                        ${region.area_formatted ? `
                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">المساحة الجغرافية</div>
                                <div class="metric-icon-box bg-blue-soft"><i class="fas fa-ruler-combined"></i></div>
                            </div>
                            <div class="metric-card-val" style="font-size:13.5px; color:#2563eb;">${region.area_formatted}</div>
                        </div>` : ''}

                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">عدد المناديب</div>
                                <div class="metric-icon-box bg-amber-soft"><i class="fas fa-walking"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#d97706;">${region.blocks_count}</div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">الأسر المعتمدة</div>
                                <div class="metric-icon-box bg-orange-soft"><i class="fas fa-users"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#ea580c;">${region.approved_families_count}</div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">إجمالي الأفراد</div>
                                <div class="metric-icon-box bg-green-soft"><i class="fas fa-user-friends"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#16a34a;">${region.approved_individuals_count}</div>
                        </div>
                    </div>

                    ${region.responsible_phone ? `
                    <div class="section-label">
                        <span>التواصل والتفاصيل</span>
                        <div class="label-line"></div>
                    </div>
                    <div class="info-pill-item">
                        <div class="info-pill-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-pill-content">
                            <span class="info-pill-label">رقم الهاتف والتواصل</span>
                            <span class="info-pill-val">${region.responsible_phone}</span>
                        </div>
                    </div>` : ''}
                `;
                sidebarPanel.style.display = 'flex';
            }

            // Open Location/Delegate Sidebar
            function openLocationSidebar(loc) {
                const themeColor = loc.icon_color || '#ea580c';

                sidebarBody.innerHTML = `
                    <div class="hero-banner-card" style="background: linear-gradient(135deg, ${themeColor} 0%, #0f172a 100%);">
                        <div class="hero-banner-content">
                            <div class="hero-avatar-ring">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h3 class="hero-title-text">${loc.delegate_name || loc.name}</h3>
                                ${loc.camp_name ? `
                                <div class="hero-pill-badge">
                                    <i class="fas fa-campground"></i> المخيم: ${loc.camp_name}
                                </div>` : ''}
                            </div>
                        </div>
                    </div>

                    <div class="section-label">
                        <span>إحصائيات المندوب</span>
                        <div class="label-line"></div>
                    </div>

                    <div class="metrics-grid-2x2">
                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">أسر المندوب</div>
                                <div class="metric-icon-box bg-orange-soft"><i class="fas fa-users"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#ea580c;">${loc.delegate_families_count}</div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">أفراد المندوب</div>
                                <div class="metric-icon-box bg-green-soft"><i class="fas fa-user-friends"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#16a34a;">${loc.delegate_individuals_count}</div>
                        </div>
                    </div>

                    <div class="section-label">
                        <span>بيانات مسئول المنطقة</span>
                        <div class="label-line"></div>
                    </div>

                    <div class="info-pill-item mb-3">
                        <div class="info-pill-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="info-pill-content">
                            <span class="info-pill-label">مسئول المنطقة الفعلي</span>
                            <span class="info-pill-val">${loc.responsible_name || 'غير محدد'}</span>
                        </div>
                    </div>

                    <div class="metrics-grid-2x2">
                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">أسر مسؤول المنطقة</div>
                                <div class="metric-icon-box bg-amber-soft"><i class="fas fa-users"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#d97706;">${loc.area_families_count}</div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-card-top">
                                <div class="metric-card-label">أفراد مسؤول المنطقة</div>
                                <div class="metric-icon-box bg-indigo-soft"><i class="fas fa-user-friends"></i></div>
                            </div>
                            <div class="metric-card-val" style="color:#4f46e5;">${loc.area_individuals_count}</div>
                        </div>
                    </div>

                    ${(loc.region_name || loc.address || loc.phone) ? `
                    <div class="section-label">
                        <span>معلومات التواصل والعنوان</span>
                        <div class="label-line"></div>
                    </div>
                    ` : ''}

                    ${loc.region_name ? `
                    <div class="info-pill-item">
                        <div class="info-pill-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="info-pill-content">
                            <span class="info-pill-label">المنطقة الجغرافية</span>
                            <span class="info-pill-val">${loc.region_name}</span>
                        </div>
                    </div>` : ''}

                    ${loc.address ? `
                    <div class="info-pill-item">
                        <div class="info-pill-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-pill-content">
                            <span class="info-pill-label">العنوان التفصيلي</span>
                            <span class="info-pill-val">${loc.address}</span>
                        </div>
                    </div>` : ''}

                    ${loc.phone ? `
                    <div class="info-pill-item">
                        <div class="info-pill-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-pill-content">
                            <span class="info-pill-label">رقم الجوال والتواصل</span>
                            <span class="info-pill-val">${loc.phone}</span>
                        </div>
                    </div>` : ''}
                `;
                sidebarPanel.style.display = 'flex';
            }

            // Location Service
            autoLocateUser(map, regionPolygons, group, fitMapToCoverage);
        });

        function autoLocateUser(map, regionPolygons, group, fitMapToCoverage) {
            if (!navigator.geolocation) {
                fitMapToCoverage();
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    const userIcon = L.icon({
                        iconUrl: "{{ asset('icons/marker-p.png') }}",
                        iconSize:     [40, 40],
                        iconAnchor:   [20, 40],
                        popupAnchor:  [0, -40],
                        shadowUrl:    'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                        shadowSize:   [41, 41],
                        shadowAnchor: [12, 41],
                    });

                    L.marker([lat, lng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup('هذا هو موقعك الحالي تقريباً');

                    // إبقاء التكبير والتركيز دائماً على نطاق تغطية المناطق
                    fitMapToCoverage();

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
                    }
                },
                function (err) {
                    console.warn('Geolocation error:', err);
                    fitMapToCoverage();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

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
