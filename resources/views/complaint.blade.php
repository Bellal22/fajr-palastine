<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>صفحة الشكاوى - جمعية الفجر الشبابي الفلسطيني</title>

    <!-- استيراد الخطوط والأيقونات والمكتبات -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ==========================================
           إعدادات الخلفية المتحركة
           ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #fff3e0 100%);
        }

        /* الخلفية المتحركة - فقاعات */
        .bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            bottom: -150px;
            border-radius: 50%;
            animation: rise 15s infinite ease-in;
            box-shadow: 0 0 20px rgba(255, 111, 0, 0.3);
        }

        .bubble:nth-child(1) { width: 90px; height: 90px; left: 5%; animation-duration: 8s; animation-delay: 0s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.35), rgba(255, 111, 0, 0.15)); }
        .bubble:nth-child(2) { width: 70px; height: 70px; left: 15%; animation-duration: 9s; animation-delay: 1s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.2)); }
        .bubble:nth-child(3) { width: 110px; height: 110px; left: 25%; animation-duration: 7s; animation-delay: 0.5s; background: radial-gradient(circle at 30% 30%, rgba(230, 81, 0, 0.32), rgba(230, 81, 0, 0.12)); }
        .bubble:nth-child(4) { width: 80px; height: 80px; left: 35%; animation-duration: 8.5s; animation-delay: 1.5s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0.18)); }
        .bubble:nth-child(5) { width: 100px; height: 100px; left: 45%; animation-duration: 7.5s; animation-delay: 0.3s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.38), rgba(255, 111, 0, 0.16)); }
        .bubble:nth-child(6) { width: 85px; height: 85px; left: 55%; animation-duration: 8.2s; animation-delay: 0.8s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.58), rgba(255, 255, 255, 0.22)); }
        .bubble:nth-child(7) { width: 65px; height: 65px; left: 65%; animation-duration: 9.5s; animation-delay: 1.2s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.4), rgba(255, 111, 0, 0.18)); }
        .bubble:nth-child(8) { width: 95px; height: 95px; left: 75%; animation-duration: 7.8s; animation-delay: 0.2s; background: radial-gradient(circle at 30% 30%, rgba(230, 81, 0, 0.34), rgba(230, 81, 0, 0.14)); }
        .bubble:nth-child(9) { width: 75px; height: 75px; left: 85%; animation-duration: 8.8s; animation-delay: 1.8s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.62), rgba(255, 255, 255, 0.24)); }
        .bubble:nth-child(10) { width: 105px; height: 105px; left: 95%; animation-duration: 7.3s; animation-delay: 0.6s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.36), rgba(255, 111, 0, 0.14)); }

        @keyframes rise {
            0% { bottom: -150px; transform: translateX(0) rotate(0deg) scale(0.8); opacity: 0; }
            5% { opacity: 1; }
            95% { opacity: 1; }
            100% { bottom: 110%; transform: translateX(120px) rotate(720deg) scale(1.2); opacity: 0; }
        }

        /* موجات متحركة */
        .wave-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 250px;
            z-index: 0;
            pointer-events: none;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
        }

        .wave:nth-child(1) {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,50 Q300,0 600,50 T1200,50 L1200,120 L0,120 Z" fill="rgba(255,111,0,0.18)"/></svg>');
            background-size: 50% 100%;
            animation: wave 15s linear infinite;
        }

        .wave:nth-child(2) {
            bottom: 15px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,70 Q300,20 600,70 T1200,70 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.4)"/></svg>');
            background-size: 50% 100%;
            animation: wave 12s linear infinite reverse;
        }

        .wave:nth-child(3) {
            bottom: 30px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,40 Q300,10 600,40 T1200,40 L1200,120 L0,120 Z" fill="rgba(230,81,0,0.15)"/></svg>');
            background-size: 50% 100%;
            animation: wave 18s linear infinite;
        }

        .wave:nth-child(4) {
            bottom: 45px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,60 Q300,15 600,60 T1200,60 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.35)"/></svg>');
            background-size: 50% 100%;
            animation: wave 14s linear infinite reverse;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* دوائر متحركة كبيرة */
        .floating-circles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.3;
            animation: float-rotate 20s infinite ease-in-out;
        }

        .floating-circle:nth-child(1) { width: 350px; height: 350px; background: radial-gradient(circle, rgba(255, 111, 0, 0.25) 0%, transparent 70%); top: -100px; right: -100px; animation-duration: 12s; }
        .floating-circle:nth-child(2) { width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%); bottom: -80px; left: -80px; animation-duration: 15s; animation-delay: 2s; }
        .floating-circle:nth-child(3) { width: 280px; height: 280px; background: radial-gradient(circle, rgba(230, 81, 0, 0.22) 0%, transparent 70%); top: 40%; left: -50px; animation-duration: 18s; animation-delay: 1s; }
        .floating-circle:nth-child(4) { width: 320px; height: 320px; background: radial-gradient(circle, rgba(255, 111, 0, 0.28) 0%, transparent 70%); bottom: 20%; right: -60px; animation-duration: 16s; animation-delay: 3s; }

        @keyframes float-rotate {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(50px, -50px) rotate(90deg) scale(1.15); }
            50% { transform: translate(-30px, 60px) rotate(180deg) scale(0.9); }
            75% { transform: translate(60px, 30px) rotate(270deg) scale(1.1); }
        }

        /* جزيئات صغيرة سريعة */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: particle-float 8s infinite ease-in-out;
        }

        .particle:nth-child(1) { background: #FF6F00; left: 8%; animation-delay: 0s; animation-duration: 7s; }
        .particle:nth-child(2) { background: rgba(255, 255, 255, 0.8); left: 18%; animation-delay: 1s; animation-duration: 8s; }
        .particle:nth-child(3) { background: #E65100; left: 28%; animation-delay: 2s; animation-duration: 6.5s; }
        .particle:nth-child(4) { background: #FF6F00; left: 38%; animation-delay: 0.5s; animation-duration: 7.5s; }
        .particle:nth-child(5) { background: rgba(255, 255, 255, 0.8); left: 48%; animation-delay: 1.5s; animation-duration: 8.5s; }
        .particle:nth-child(6) { background: #E65100; left: 58%; animation-delay: 2.5s; animation-duration: 7s; }
        .particle:nth-child(7) { background: #FF6F00; left: 68%; animation-delay: 0.8s; animation-duration: 6.8s; }
        .particle:nth-child(8) { background: rgba(255, 255, 255, 0.8); left: 78%; animation-delay: 1.8s; animation-duration: 8.2s; }
        .particle:nth-child(9) { background: #E65100; left: 88%; animation-delay: 2.8s; animation-duration: 7.3s; }
        .particle:nth-child(10) { background: #FF6F00; left: 12%; animation-delay: 3s; animation-duration: 6.7s; }

        @keyframes particle-float {
            0% { bottom: -10px; opacity: 0; transform: translateX(0) scale(0.5); }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% { bottom: 110%; opacity: 0; transform: translateX(80px) scale(1.5); }
        }

        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* الهيدر العلوي */
        .top-bar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.15);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 4px solid #FF6F00;
        }

        .logo-small {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #FF6F00;
            box-shadow: 0 4px 15px rgba(255, 111, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-small:hover {
            transform: rotate(360deg) scale(1.1);
        }

        .top-bar h1 {
            color: #333;
            font-size: 22px;
            font-weight: 700;
        }

        .top-bar span {
            color: #FF6F00;
        }

        /* الصف الرئيسي */
        .main-row {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 25px;
            align-items: start;
        }

        /* القسم الجانبي */
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
        }

        .sidebar h2 {
            color: #333;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6F00;
        }

        .info-item {
            margin-bottom: 20px;
            padding: 18px;
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.08), rgba(230, 81, 0, 0.05));
            border-radius: 12px;
            border-right: 4px solid #FF6F00;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(255, 111, 0, 0.1);
        }

        .info-item:hover {
            transform: translateX(-8px) translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.25);
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.12), rgba(230, 81, 0, 0.08));
        }

        .info-item h3 {
            color: #E65100;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-item h3 i {
            margin-left: 8px;
            color: #FF6F00;
        }

        .info-item p {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            text-align: right;
        }

        /* القسم الرئيسي */
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
            transition: all 0.3s ease;
        }

        .welcome-box {
            background: linear-gradient(135deg, #FF6F00 0%, #E65100 100%);
            padding: 35px;
            border-radius: 15px;
            text-align: center;
            color: white;
            margin-bottom: 35px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.4);
        }

        .welcome-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 15px,
                rgba(255, 255, 255, 0.1) 15px,
                rgba(255, 255, 255, 0.1) 30px
            );
            animation: slide 25s linear infinite;
        }

        @keyframes slide {
            0% { transform: translate(0, 0); }
            100% { transform: translate(60px, 60px); }
        }

        .welcome-box h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .welcome-box p {
            font-size: 16px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* النموذج */
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }

        .form-group label {
            display: block;
            color: #555;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #FF6F00;
            font-size: 18px;
        }

        input[type="number"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 16px 55px 16px 18px;
            font-size: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            background: white;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 5px rgba(255, 111, 0, 0.15);
            transform: translateY(-2px);
        }

        /* صندوق حالة الفحص المباشر */
        .status-alert-box {
            display: none;
            margin-top: 15px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.6;
            text-align: right;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            animation: fadeInStatus 0.3s ease-in-out;
        }

        .status-alert-box.status-checking {
            background: #e3f2fd;
            color: #0d47a1;
            border-right: 5px solid #1976d2;
        }

        .status-alert-box.status-success {
            background: #e8f5e9;
            color: #1b5e20;
            border-right: 5px solid #2e7d32;
        }

        .status-alert-box.status-error {
            background: #ffebee;
            color: #b71c1c;
            border-right: 5px solid #c62828;
        }

        .status-alert-box i {
            margin-left: 8px;
            font-size: 16px;
        }

        #complaint_fields_step {
            animation: fadeInStatus 0.4s ease-in-out;
        }

        @keyframes fadeInStatus {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* الزر */
        .submit-btn {
            width: 100%;
            padding: 18px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
            margin-top: 10px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .submit-btn:hover::before {
            width: 400px;
            height: 400px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.5);
        }

        .submit-btn i {
            margin-left: 8px;
            position: relative;
            z-index: 1;
        }

        .submit-btn span {
            position: relative;
            z-index: 1;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .main-row {
                grid-template-columns: 1fr;
            }

            .sidebar {
                order: 2;
            }

            .main-content {
                order: 1;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .top-bar {
                padding: 18px 20px;
                flex-direction: column;
                text-align: center;
            }

            .top-bar h1 {
                font-size: 20px;
            }

            .sidebar {
                padding: 25px;
            }

            .main-content {
                padding: 30px 25px;
            }

            .welcome-box {
                padding: 28px;
            }

            .welcome-box h2 {
                font-size: 22px;
            }

            input[type="number"],
            input[type="text"],
            textarea {
                padding: 14px 50px 14px 16px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .top-bar {
                padding: 15px;
            }

            .logo-small {
                width: 50px;
                height: 50px;
            }

            .top-bar h1 {
                font-size: 18px;
            }

            .sidebar {
                padding: 20px;
            }

            .main-content {
                padding: 25px 20px;
            }

            .welcome-box {
                padding: 22px;
            }

            .welcome-box h2 {
                font-size: 20px;
            }

            .welcome-box p {
                font-size: 15px;
            }

            .submit-btn {
                padding: 15px;
                font-size: 17px;
            }
        }
    </style>
</head>
<body>

    <!-- الفقاعات المتحركة -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <!-- الموجات -->
    <div class="wave-container">
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>

    <!-- الدوائر الكبيرة -->
    <div class="floating-circles">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <!-- الجزيئات الصغيرة -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="page-container">
        <!-- الهيدر العلوي -->
        <div class="top-bar">
            <img src="{{asset('background/image.jpg')}}" alt="الشعار" class="logo-small">
            <h1>جمعية <span>الفجر الشبابي الفلسطيني</span></h1>
        </div>

        <!-- الصف الرئيسي -->
        <div class="main-row">
            <!-- المحتوى الرئيسي -->
            <div class="main-content">
                <!-- صندوق الترحيب -->
                <div class="welcome-box">
                    <h2>تقديم شكوى أو استفسار</h2>
                    <p>نحن هنا للاستماع إليكم ومساعدتكم</p>
                </div>

                <!-- رسائل الخطأ من Laravel (بشكل جميل) -->
                @if ($errors->any())
                    <div style="background: #ffebee; color: #c62828; padding: 15px; margin-bottom: 20px; border-radius: 12px; border-right: 4px solid #ef5350;">
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul style="margin: 10px 0 0 20px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- النموذج -->
                <form action="{{ route('complaints.store') }}" method="POST" id="complaintForm">
                    @csrf

                    <!-- الخطوة 1: رقم الهوية -->
                    <div class="form-group">
                        <label><i class="fas fa-id-card" style="color: #FF6F00; margin-left: 6px;"></i> رقم الهوية الفلسطيني</label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card input-icon"></i>
                            <input
                                type="text"
                                id="id_num"
                                name="id_num"
                                placeholder="أدخل 9 أرقام"
                                value="{{ old('id_num') }}"
                                maxlength="9"
                                pattern="\d{9}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); handleIdInput(this.value);"
                                onblur="handleIdInput(this.value);"
                                title="يجب إدخال 9 أرقام فقط"
                                required>
                        </div>
                        <div id="id_status_box" class="status-alert-box"></div>
                    </div>

                    <!-- الخطوة 2: تحديد نوع الشكوى (يظهر عند مطابقة رقم الهوية للشروط) -->
                    <div id="complaint_type_step" style="display: none; margin-top: 20px;">
                        <div class="form-group">
                            <label><i class="fas fa-layer-group" style="color: #FF6F00; margin-left: 6px;"></i> نوع الشكوى / الاستفسار</label>
                            <div class="input-wrapper">
                                <i class="fas fa-list-alt input-icon"></i>
                                <select id="complaint_type_id" name="complaint_type_id" class="custom-select-field" onchange="handleTypeChange(this.value)" required style="width: 100%; padding: 16px 55px 16px 18px; font-size: 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-family: 'Cairo', sans-serif; background: white; cursor: pointer;">
                                    <option value="">-- اختر نوع الشكوى --</option>
                                </select>
                            </div>
                        </div>
                        <div id="type_status_box" class="status-alert-box"></div>
                    </div>

                    <!-- الخطوة 3: عنوان ونص الشكوى وزر الإرسال (تظهر فقط إذا كان نوع الشكوى فعّال وغير متجاوز للفترة الفاصلة) -->
                    <div id="complaint_fields_step" style="display: none; margin-top: 20px;">
                        <div class="form-group">
                            <label><i class="fas fa-heading" style="color: #FF6F00; margin-left: 6px;"></i> عنوان الشكوى</label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input
                                    type="text"
                                    id="complaint_title"
                                    name="complaint_title"
                                    maxlength="150"
                                    placeholder="عنوان مختصر للشكوى"
                                    value="{{ old('complaint_title') }}"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-comment-alt" style="color: #FF6F00; margin-left: 6px;"></i> نص الشكوى</label>
                            <div class="input-wrapper">
                                <i class="fas fa-comment-alt input-icon"></i>
                                <textarea
                                    id="complaint_text"
                                    name="complaint_text"
                                    maxlength="3000"
                                    placeholder="اكتب تفاصيل الشكوى هنا (حد أقصى 3000 حرف)..."
                                    required>{{ old('complaint_text') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">
                            <span>
                                <i class="fas fa-paper-plane"></i>
                                إرسال الشكوى
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- القسم الجانبي -->
            <div class="sidebar">
                <h2>إرشادات الشكاوى</h2>

                <div class="info-item">
                    <h3><i class="fas fa-user-check"></i> شروط التقديم</h3>
                    <p>استقبال الشكاوى متاح فقط للأشخاص المسجلين مسبقاً في النظام والذين مدينتهم الحالية هي خانيونس أو رفح.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-clock"></i> وقت الاستجابة</h3>
                    <p>سيتم الرد على شكواك في أقرب وقت ممكن من قبل الفريق المختص.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-shield-alt"></i> السرية والحد اليومي</h3>
                    <p>جميع الشكاوى سرية تماماً، مع العلم أنه يتم استقبال 200 شكوى يومياً كحد أقصى بالنظام.</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        let fetchedTypes = [];

        document.addEventListener("DOMContentLoaded", function() {
            const idVal = document.getElementById('id_num').value;
            if (idVal && idVal.length === 9) {
                checkIdRealtime(idVal);
            }
        });

        function handleIdInput(val) {
            const statusBox = document.getElementById('id_status_box');
            const typeStep = document.getElementById('complaint_type_step');
            const fieldsStep = document.getElementById('complaint_fields_step');

            if (val.length < 9) {
                statusBox.style.display = 'none';
                typeStep.style.display = 'none';
                fieldsStep.style.display = 'none';
                document.getElementById('complaint_type_id').value = '';
                return;
            }

            if (val.length === 9) {
                checkIdRealtime(val);
            }
        }

        function checkIdRealtime(idNum) {
            const statusBox = document.getElementById('id_status_box');
            const typeStep = document.getElementById('complaint_type_step');
            const fieldsStep = document.getElementById('complaint_fields_step');
            const typeSelect = document.getElementById('complaint_type_id');
            const typeStatusBox = document.getElementById('type_status_box');

            statusBox.style.display = 'block';
            statusBox.className = 'status-alert-box status-checking';
            statusBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري فحص رقم الهوية والنطاق الجغرافي...';

            typeStep.style.display = 'none';
            fieldsStep.style.display = 'none';
            typeStatusBox.style.display = 'none';
            typeSelect.innerHTML = '<option value="">-- اختر نوع الشكوى --</option>';

            fetch("{{ route('complaints.verifyId') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id_num: idNum })
            })
            .then(res => res.json())
            .then(data => {
                if (data.eligible) {
                    statusBox.className = 'status-alert-box status-success';
                    statusBox.innerHTML = `<i class="fas fa-check-circle"></i> <strong>مسجل ومطابق للشروط:</strong> ${data.name} (المدينة الحالية: ${data.city})`;

                    // تعبئة أنواع الشكاوى
                    fetchedTypes = data.complaint_types || [];
                    typeStep.style.display = 'block';

                    if (fetchedTypes.length > 0) {
                        fetchedTypes.forEach(type => {
                            const opt = document.createElement('option');
                            opt.value = type.id;
                            opt.textContent = type.name + (!type.is_active ? ' (غير متاح حالياً)' : '');
                            typeSelect.appendChild(opt);
                        });

                        // إذا كان هناك نوع واحد فقط فعال، اختياره تلقائياً
                        if (fetchedTypes.length === 1 && fetchedTypes[0].is_active) {
                            typeSelect.value = fetchedTypes[0].id;
                            handleTypeChange(fetchedTypes[0].id);
                        }
                    } else {
                        typeStatusBox.style.display = 'block';
                        typeStatusBox.className = 'status-alert-box status-error';
                        typeStatusBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> لا توجد أنواع شكاوى معرّفة حالياً بجدول النظام.';
                    }
                } else {
                    statusBox.className = 'status-alert-box status-error';
                    statusBox.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${data.message}`;
                    typeStep.style.display = 'none';
                    fieldsStep.style.display = 'none';
                }
            })
            .catch(err => {
                statusBox.style.display = 'none';
                typeStep.style.display = 'none';
                fieldsStep.style.display = 'none';
            });
        }

        function handleTypeChange(typeId) {
            const fieldsStep = document.getElementById('complaint_fields_step');
            const typeStatusBox = document.getElementById('type_status_box');
            const idNum = document.getElementById('id_num').value;

            if (!typeId) {
                typeStatusBox.style.display = 'none';
                fieldsStep.style.display = 'none';
                return;
            }

            typeStatusBox.style.display = 'block';
            typeStatusBox.className = 'status-alert-box status-checking';
            typeStatusBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الفحص...';
            fieldsStep.style.display = 'none';

            fetch("{{ route('complaints.checkType') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id_num: idNum,
                    complaint_type_id: typeId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.eligible) {
                    typeStatusBox.className = 'status-alert-box status-success';
                    typeStatusBox.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}`;
                    fieldsStep.style.display = 'block';
                } else {
                    typeStatusBox.className = 'status-alert-box status-error';
                    typeStatusBox.innerHTML = `<i class="fas fa-info-circle"></i> ${data.message}`;
                    fieldsStep.style.display = 'none';
                }
            })
            .catch(err => {
                typeStatusBox.style.display = 'none';
                fieldsStep.style.display = 'none';
            });
        }

        document.getElementById('complaintForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const idNum = document.getElementById('id_num').value;
            const title = document.getElementById('complaint_title').value;
            const text = document.getElementById('complaint_text').value;

            const codePattern = /<script|javascript:|alert\(|<\?php|eval\(|document\.|[<>/{}[\]\\^]/i;

            if (codePattern.test(title) || codePattern.test(text)) {
                Swal.fire({
                    icon: 'error',
                    title: 'تنبيه أمني',
                    text: 'يرجى عدم استخدام رموز خاصة أو أكواد برمجية!',
                    confirmButtonColor: '#FF6F00'
                });
                return;
            }

            if (idNum.length !== 9) {
                Swal.fire({
                    icon: 'warning',
                    title: 'رقم الهوية خطأ',
                    text: 'يجب أن يتكون رقم الهوية من 9 أرقام بالضبط.',
                    confirmButtonColor: '#FF6F00'
                });
                return;
            }

            Swal.fire({
                title: 'جاري الإرسال...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: 'تم تسجيل شكواك بنجاح في جمعية الفجر.',
                    confirmButtonColor: '#FF6F00',
                    confirmButtonText: 'حسنًا'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('complaints.successcomplaint') }}";
                    }
                });
            })
            .catch(error => {
                let errorMessage = 'حدثت مشكلة أثناء الإرسال، يرجى المحاولة مرة أخرى.';
                if (error.errors && error.errors.id_num) {
                    errorMessage = error.errors.id_num.join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                } else if (typeof error === 'object') {
                    errorMessage = Object.values(error).flat().join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في التحقق',
                    text: errorMessage,
                    confirmButtonColor: '#FF6F00'
                });
            });
        });
    </script>
</body>
</html>
