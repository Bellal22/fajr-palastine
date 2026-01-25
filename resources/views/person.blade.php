<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نموذج تسجيل المواطنين - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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
        .bubble:nth-child(11) { width: 88px; height: 88px; left: 10%; animation-duration: 8.7s; animation-delay: 2s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.33), rgba(255, 111, 0, 0.13)); }
        .bubble:nth-child(12) { width: 72px; height: 72px; left: 20%; animation-duration: 9.2s; animation-delay: 2.5s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.56), rgba(255, 255, 255, 0.2)); }
        .bubble:nth-child(13) { width: 98px; height: 98px; left: 30%; animation-duration: 7.6s; animation-delay: 2.2s; background: radial-gradient(circle at 30% 30%, rgba(230, 81, 0, 0.31), rgba(230, 81, 0, 0.11)); }
        .bubble:nth-child(14) { width: 82px; height: 82px; left: 40%; animation-duration: 8.4s; animation-delay: 2.8s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.59), rgba(255, 255, 255, 0.21)); }
        .bubble:nth-child(15) { width: 92px; height: 92px; left: 50%; animation-duration: 7.9s; animation-delay: 2.3s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.37), rgba(255, 111, 0, 0.15)); }

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
            max-width: 1400px;
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
        }

        /* القسم الجانبي */
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
            height: fit-content;
            position: sticky;
            top: 20px;
            order: 2; /* في RTL: العمود الثاني (اليسار) */
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
            cursor: pointer;
        }

        .info-item:hover {
            transform: translateX(-8px) translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.25);
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.12), rgba(230, 81, 0, 0.08));
        }

        .info-item.active {
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.15), rgba(230, 81, 0, 0.1));
            border-right: 6px solid #FF6F00;
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
            order: 1; /* في RTL: العمود الأول (اليمين) */
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

        /* أقسام النموذج */
        .form-section {
            scroll-margin-top: 80px;
            padding: 25px;
            margin-bottom: 25px;
            background: rgba(255, 111, 0, 0.02);
            border-radius: 12px;
            border-right: 3px solid #FF6F00;
        }

        .form-section h3 {
            color: #FF6F00;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(255, 111, 0, 0.2);
        }

        .form-section h3 i {
            margin-left: 8px;
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
        input[type="date"],
        select,
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
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 5px rgba(255, 111, 0, 0.15);
            transform: translateY(-2px);
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .error-message {
            margin-top: 5px;
            font-size: 0.9rem;
            color: #d32f2f;
            text-align: right;
            display: none;
        }

        /* عرض رسائل الخطأ من Laravel */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 12px;
            text-align: right;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 2px solid #ef5350;
            border-right: 4px solid #c62828;
        }

        .alert-danger ul {
            margin: 10px 0 0 0;
            padding: 0 20px;
            list-style-position: inside;
        }

        .alert-danger li {
            margin: 5px 0;
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
            margin-top: 20px;
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

        .buttons-container {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .link-btn {
            flex: 1;
            padding: 18px;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid #FF6F00;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            background: white;
            color: #FF6F00;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .link-btn:hover {
            background: #FF6F00;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.3);
        }

        .area-responsible-container {
            display: flex;
            flex-direction: column;
        }

        #areaResponsibleField[style*="display:none"] .error-message {
            display: block !important;
            visibility: hidden;
        }

        #areaResponsibleField[style*="display:none"] label.form-label,
        #areaResponsibleField[style*="display:none"] select.form-control {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .main-row {
                grid-template-columns: 1fr;
            }

            .sidebar {
                order: 2;
                position: relative;
                top: 0;
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

            .row {
                grid-template-columns: 1fr;
            }

            input, select, textarea {
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

            .submit-btn,
            .link-btn {
                padding: 15px;
                font-size: 16px;
            }

            .buttons-container {
                flex-direction: column;
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
                    <h2>نموذج تسجيل المواطنين</h2>
                    <p>يرجى ملء البيانات بدقة وبشكل كامل</p>
                </div>

                <!-- عرض رسائل الخطأ من Laravel -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- النموذج -->
                <form action="{{ route('persons.store') }}" method="POST" id="form">
                    @csrf

                    <!-- قسم البيانات الشخصية -->
                    <div class="form-section" id="personal-info">
                        <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                        <div class="row">
                            <div class="form-group">
                                <label for="first_name">الاسم الأول</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="first_name" name="first_name" placeholder="الاسم الأول" value="{{ old('first_name') }}" oninput="validateArabicInput('first_name')" onfocus="resetBorderAndError('first_name')" required>
                                </div>
                                <div class="error-message" id="first_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="father_name">اسم الأب</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="father_name" name="father_name" placeholder="اسم الأب" value="{{ old('father_name') }}" oninput="validateArabicInput('father_name')" onfocus="resetBorderAndError('father_name')" required>
                                </div>
                                <div class="error-message" id="father_name_error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="grandfather_name">اسم الجد</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="grandfather_name" name="grandfather_name" placeholder="اسم الجد" value="{{ old('grandfather_name') }}" oninput="validateArabicInput('grandfather_name')" onfocus="resetBorderAndError('grandfather_name')" required>
                                </div>
                                <div class="error-message" id="grandfather_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="family_name">اسم العائلة</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-users input-icon"></i>
                                    <input type="text" id="family_name" name="family_name" placeholder="اسم العائلة" value="{{ old('family_name') }}" oninput="validateArabicInput('family_name')" onfocus="resetBorderAndError('family_name')" required>
                                </div>
                                <div class="error-message" id="family_name_error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="id_num">رقم الهوية</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="number" disabled id="id_num" name="id_num" value="{{ $id_num }}" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gender">الجنس</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-venus-mars input-icon"></i>
                                    <select id="gender" name="gender" required oninput="validateGender()" onfocus="resetBorderAndError('gender')">
                                        <option value="">اختر الجنس</option>
                                        @foreach($chooses['gender'] ?? [] as $choice)
                                            <option {{ old('gender') == $choice->slug ? 'selected' : '' }} value="{{ $choice->slug }}">{{ $choice->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="gender_error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="dob">تاريخ الميلاد</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar input-icon"></i>
                                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}" oninput="validatedob()" onfocus="resetBorderAndError('dob')" required>
                                </div>
                                <div class="error-message" id="dob_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone">رقم الجوال المعتمد</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" dir="ltr" placeholder="059-123-1234" id="phone" name="phone" value="{{ old('phone') }}" oninput="validatePhoneInput()" onfocus="resetPhoneError()" required>
                                </div>
                                <div class="error-message" id="phone_error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- قسم الحالة الاجتماعية والوظيفية -->
                    <div class="form-section" id="social-info">
                        <h3><i class="fas fa-briefcase"></i> الحالة الاجتماعية والوظيفية</h3>
                        <div class="row">
                            <div class="form-group">
                                <label for="social_status">الحالة الاجتماعية</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-heart input-icon"></i>
                                    <select id="social_status" name="social_status" required oninput="validateSocialStatus()" onfocus="resetBorderAndError('social_status')">
                                        <option value="">اختر الحالة</option>
                                        @foreach($chooses['social_status'] ?? [] as $choice)
                                            <option {{ old('social_status') == $choice->slug ? 'selected' : '' }} value="{{ $choice->slug }}">{{ $choice->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="social_status_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="employment_status">حالة العمل</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-briefcase input-icon"></i>
                                    <select id="employment_status" name="employment_status" required oninput="validateEmploymentStatus()" onfocus="resetBorderAndError('employment_status')">
                                        <option value="">اختر حالة العمل</option>
                                        @foreach($chooses['employment_status'] ?? [] as $choice)
                                            <option {{ old('employment_status') == $choice->slug ? 'selected' : (old('employment_status') == null && $choice->slug == 'unemployed' ? 'selected' : '') }} value="{{ $choice->slug }}">{{ $choice->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="employment_status_error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- قسم الحالة الصحية -->
                    <div class="form-section" id="health-info">
                        <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                        <div class="form-group">
                            <label for="has_condition">هل لديك حالة صحية مرض مزمن حالة خالصة إصابة حرب ؟</label>
                            <div class="input-wrapper">
                                <i class="fas fa-notes-medical input-icon"></i>
                                <select id="has_condition" name="has_condition" onchange="toggleConditionDescription()">
                                    <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                                    <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="condition_description_group" style="display: none;">
                            <label for="condition_description">وصف الحالة الصحية</label>
                            <div class="input-wrapper">
                                <i class="fas fa-comment-medical input-icon"></i>
                                <textarea id="condition_description" name="condition_description" rows="4" placeholder="اكتب وصف الحالة الصحية..." oninput="validateConditionText()" onfocus="resetBorderAndError('condition_description')">{{ old('condition_description') }}</textarea>
                            </div>
                            <div class="error-message" id="condition_description_error"></div>
                        </div>
                    </div>

                    <!-- قسم معلومات السكن -->
                    <div class="form-section" id="housing-info">
                        <h3><i class="fas fa-home"></i> معلومات السكن</h3>
                        <div class="row">
                            <div class="form-group">
                                <label for="city">المحافظة الأصلية</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                    <select id="city" name="city" required oninput="validateCity()" onfocus="resetBorderAndError('city')">
                                        <option value="">اختر المحافظة الأصلية</option>
                                        @foreach($cities as $id => $name)
                                            <option {{ old('city') == $name ? 'selected' : '' }} value="{{ $name }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="city_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="housing_damage_status">حالة السكن السابق</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-house-damage input-icon"></i>
                                    <select id="housing_damage_status" name="housing_damage_status" required oninput="validateHousingDamageStatus()" onfocus="resetBorderAndError('housing_damage_status')">
                                        <option value="">اختر حالة السكن السابق</option>
                                        @foreach($chooses['housing_damage_status'] ?? [] as $choice)
                                            <option {{ old('housing_damage_status') == $choice->slug ? 'selected' : '' }} value="{{ $choice->slug }}">{{ $choice->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="housing_damage_status_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="current_city">المحافظة التي تسكن فيها حالياً</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-marked-alt input-icon"></i>
                                    <select id="current_city" name="current_city" required oninput="validateCurrentCity()" onfocus="resetBorderAndError('current_city')">
                                        <option value="">اختر المحافظة التي تسكن فيها حالياً</option>
                                        @foreach($cities as $id => $name)
                                            <option {{ old('current_city') == $name ? 'selected' : '' }} value="{{ $name }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="current_city_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="housing_type">نوع السكن الذي تعيش فيه حالياً</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-building input-icon"></i>
                                    <select id="housing_type" name="housing_type" required oninput="validateHousingType()" onfocus="resetBorderAndError('housing_type')">
                                        <option value="">اختر نوع السكن الذي تعيش فيه حالياً</option>
                                        @foreach($chooses['housing_type'] ?? [] as $choice)
                                            <option {{ old('housing_type') == $choice->slug ? 'selected' : '' }} value="{{ $choice->slug }}">{{ $choice->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="housing_type_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="neighborhood">الحي السكني الذي تتواجد فيه حالياً</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-signs input-icon"></i>
                                    <select id="neighborhood" name="neighborhood" required oninput="validateNeighborhood()" onfocus="resetBorderAndError('neighborhood')">
                                        <option value="">اختر الحي السكني الذي تتواجد فيه حالياً</option>
                                    </select>
                                </div>
                                <div class="error-message" id="neighborhood_error"></div>
                            </div>

                            <div class="form-group area-responsible-container" id="areaResponsibleField" style="display:none;">
                                <label for="area_responsible_id" class="form-label">مسؤول المنطقة</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user-tie input-icon"></i>
                                    <select id="area_responsible_id" name="area_responsible_id" oninput="validateAreaResponsible()" onfocus="resetBorderAndError('area_responsible_id')">
                                        <option value="">اختر المسؤول</option>
                                    </select>
                                </div>
                                <div class="error-message" id="area_responsible_id_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group" id="blockField" style="display:none !important;">
                                <label for="block_id" class="form-label">المندوب</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user-shield input-icon"></i>
                                    <select id="block_id" name="block_id" oninput="resetBorderAndError('block_id')" onfocus="resetBorderAndError('block_id')">
                                        <option value="">اختر المندوب</option>
                                    </select>
                                </div>
                                <div class="error-message" id="block_id_error"></div>
                            </div>

                        <div class="form-group">
                            <label for="landmark">أقرب معلم</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-pin input-icon"></i>
                                <input type="text" id="landmark" name="landmark" placeholder="أقرب معلم" value="{{ old('landmark') }}" oninput="validateArabicInput('landmark')" onfocus="resetBorderAndError('landmark')">
                            </div>
                            <div class="error-message" id="landmark_error"></div>
                        </div>
                    </div>

                    <!-- زر الإرسال -->
                    <div class="buttons-container">
                        <a href="{{ route('persons.intro') }}" class="link-btn">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                        <button type="submit" class="submit-btn">
                            <span>
                                <i class="fas fa-paper-plane"></i>
                                تسجيل بيانات أفراد الأسرة
                            </span>
                        </button>
                    </div>
                </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                let errorHtml = '<ul style="text-align: right; direction: rtl;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'يرجى تصحيح الأخطاء التالية',
                    html: errorHtml,
                    confirmButtonColor: '#FF6F00',
                    confirmButtonText: 'حسناً'
                });
            @endif
        });
    </script>
            </div>
        </div>
         <!-- القسم الجانبي -->
        <div class="sidebar">
            <h2>أقسام النموذج</h2>

            <div class="info-item" onclick="scrollToSection('personal-info')">
                <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                <p>الاسم، الهوية، الجنس، تاريخ الميلاد، رقم الجوال</p>
            </div>

            <div class="info-item" onclick="scrollToSection('social-info')">
                <h3><i class="fas fa-briefcase"></i> الحالة الاجتماعية</h3>
                <p>الحالة الاجتماعية وحالة العمل</p>
            </div>

            <div class="info-item" onclick="scrollToSection('health-info')">
                <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                <p>معلومات عن الحالة الصحية والأمراض المزمنة</p>
            </div>

            <div class="info-item" onclick="scrollToSection('housing-info')">
                <h3><i class="fas fa-home"></i> معلومات السكن</h3>
                <p>المحافظة، نوع السكن، الحي السكني، أقرب معلم</p>
            </div>
        </div>

    <script>
        const neighborhoodsData = @json($neighborhoodsGroupedByCity);
        const responsiblesData = @json($responsiblesGroupedByNeighborhood);
        const blocksData = @json($blocksGroupedByResponsible);

        // دالة التنقل للأقسام
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                section.style.background = 'rgba(255, 111, 0, 0.08)';
                setTimeout(() => {
                    section.style.background = 'rgba(255, 111, 0, 0.02)';
                }, 1000);
                document.querySelectorAll('.info-item').forEach(item => item.classList.remove('active'));
                event.currentTarget.classList.add('active');
            }
        }

        // تتبع القسم النشط عند التمرير
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.form-section');
            const sidebarItems = document.querySelectorAll('.info-item');

            let current = '';

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });

            sidebarItems.forEach((item, index) => {
                item.classList.remove('active');
                const sectionIds = ['personal-info', 'social-info', 'health-info', 'housing-info'];
                if (current === sectionIds[index]) {
                    item.classList.add('active');
                }
            });
        });

        function validateArabicInput(inputId) {
            const inputField = document.getElementById(inputId);
            const errorMessage = document.getElementById(`${inputId}_error`);
            const value = inputField.value.trim();
            const arabicRegex = /^[\u0621-\u064A\s0-9]+$/; // Allow numbers in landmark etc

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                inputField.style.borderColor = 'red';
                return false;
            } else if (!arabicRegex.test(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'لغة الكتابة المسموح بها العربية فقط.';
                inputField.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = '';
                return true;
            }
        }

        function validatedob() {
            const inputField = document.getElementById("dob");
            const errorMessage = document.getElementById("dob_error");
            const value = inputField.value.trim();

            if (!value) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                inputField.style.borderColor = 'red';
                return false;
            }

            const birthDate = new Date(value);
            const today = new Date();

            if (birthDate > today) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'تاريخ الميلاد لا يمكن أن يكون في المستقبل.';
                inputField.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = '';
                return true;
            }
        }

        function validateGender() {
            const inputField = document.getElementById("gender");
            const errorMessage = document.getElementById("gender_error");
            const value = inputField.value;

            if (!value) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'يرجى اختيار الجنس.';
                inputField.style.borderColor = 'red';
            } else if (value === "unspecified") {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'لا يمكنك اختيار "غير محدد".';
                inputField.style.borderColor = 'red';
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = '';
                return true;
            }
        }

        function resetBorderAndError(id) {
            document.getElementById(id).style.border = "";
            const errorEl = document.getElementById(id + "_error");
            if (errorEl) {
                errorEl.textContent = "";
                errorEl.style.display = "none";
            }
        }

        function validatePhoneInput() {
            const phoneInput = document.getElementById('phone');
            const errorMessage = document.getElementById('phone_error');
            let value = phoneInput.value.trim();

            const cleanValue = value.replace(/-/g, '');
            // Must start with 059 or 056 and be 10 digits total
            const phoneRegex = /^(059|056)\d{7}$/;

            if (cleanValue === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                phoneInput.style.borderColor = 'red';
                return false;
            } else if (!phoneRegex.test(cleanValue)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'رقم الجوال يجب أن يبدأ بـ 056 أو 059 ويتكون من 10 أرقام.';
                phoneInput.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                phoneInput.style.borderColor = '';
                return true;
            }
        }

        function resetPhoneError() {
            const phoneInput = document.getElementById('phone');
            const errorMessage = document.getElementById('phone_error');
            phoneInput.style.borderColor = '';
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
        }

        document.getElementById('form').addEventListener('submit', function (e) {
            e.preventDefault();

            // تشغيل كافة التحققات
            const isFirstNameValid = validateArabicInput('first_name');
            const isFatherNameValid = validateArabicInput('father_name');
            const isGrandfatherNameValid = validateArabicInput('grandfather_name');
            const isFamilyNameValid = validateArabicInput('family_name');
            const isGenderValid = validateGender();
            const isDobValid = validatedob();
            const isPhoneValid = validatePhoneInput();
            const isSocialStatusValid = validateSocialStatus();
            const isEmploymentStatusValid = validateEmploymentStatus();
            const isCityValid = validateCity();
            const isCurrentCityValid = validateCurrentCity();
            const isHousingTypeValid = validateHousingType();
            const isNeighborhoodValid = validateNeighborhood();
            const isAreaResponsibleValid = validateAreaResponsible();
            const isHousingDamageValid = validateHousingDamageStatus();
            const isLandmarkValid = validateArabicInput('landmark');

            if (!isFirstNameValid || !isFatherNameValid || !isGrandfatherNameValid || !isFamilyNameValid ||
                !isGenderValid || !isDobValid || !isPhoneValid || !isSocialStatusValid ||
                !isEmploymentStatusValid || !isCityValid || !isCurrentCityValid ||
                !isHousingTypeValid || !isNeighborhoodValid || !isAreaResponsibleValid ||
                !isHousingDamageValid || !isLandmarkValid) {

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الإدخال',
                    text: 'يرجى التأكد من ملء جميع الحقول بشكل صحيح وحل الأخطاء الظاهرة.',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            const phoneInput = document.getElementById('phone');
            phoneInput.value = phoneInput.value.replace(/-/g, '');
            this.submit();
        });

        function validateSocialStatus() {
            const socialStatusInput = document.getElementById('social_status');
            const errorMessage = document.getElementById('social_status_error');
            const gender = document.getElementById('gender').value;
            const value = socialStatusInput.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                socialStatusInput.style.borderColor = 'red';
                return false;
            }

            // شروط الحالة الاجتماعية بناءً على الجنس
            if ((gender === 'أنثى') && ['متزوج/ة', 'متعدد الزوجات'].includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'يمنع التسجيل ببيانات الزوجة يرجى التسجيل ببيانات الزوج';
                socialStatusInput.style.borderColor = 'red';
                return false;
            }

            if ((gender === 'ذكر') && ['أعزب/انسة'].includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'ممنوع التسجيل للذكر الغير متزوج';
                socialStatusInput.style.borderColor = 'red';
                return false;
            }

            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            socialStatusInput.style.borderColor = '';
            return true;
        }

        function validateEmploymentStatus() {
            const employmentStatusInput = document.getElementById('employment_status');
            const errorMessage = document.getElementById('employment_status_error');
            const value = employmentStatusInput.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                employmentStatusInput.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                employmentStatusInput.style.borderColor = '';
                return true;
            }
        }

        function toggleConditionDescription() {
            const hasCondition = document.getElementById("has_condition").value;
            const conditionDescriptionGroup = document.getElementById("condition_description_group");

            if (hasCondition === "1") {
                conditionDescriptionGroup.style.display = "block";
            } else {
                conditionDescriptionGroup.style.display = "none";
                document.getElementById("condition_description").value = "";
                resetBorderAndError('condition_description');
            }
        }

        function validateConditionText() {
            const inputField = document.getElementById("condition_description");
            const errorMessage = document.getElementById("condition_description_error");
            const value = inputField.value.trim();
            const hasCondition = document.getElementById("has_condition").value;

            if (hasCondition === "1" && value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'يجب إدخال وصف الحالة الصحية.';
                inputField.style.borderColor = 'red';
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = '';
                return true;
            }
        }

        function validateCity() {
            const cityInput = document.getElementById('city');
            const errorMessage = document.getElementById('city_error');
            const value = cityInput.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                cityInput.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                cityInput.style.borderColor = '';
                return true;
            }
        }

        function validateHousingDamageStatus() {
            const input = document.getElementById('housing_damage_status');
            const errorMessage = document.getElementById('housing_damage_status_error');
            const value = input.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                input.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                input.style.borderColor = '';
                return true;
            }
        }

        function validateCurrentCity() {
            const input = document.getElementById('current_city');
            const errorMessage = document.getElementById('current_city_error');
            const value = input.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                input.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                input.style.borderColor = '';
                return true;
            }
        }

        function validateHousingType() {
            const input = document.getElementById('housing_type');
            const errorMessage = document.getElementById('housing_type_error');
            const value = input.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                input.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                input.style.borderColor = '';
                return true;
            }
        }

        function validateNeighborhood() {
            const input = document.getElementById('neighborhood');
            const errorMessage = document.getElementById('neighborhood_error');
            const value = input.value.trim();

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                input.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                input.style.borderColor = '';
                return true;
            }
        }

        function validateAreaResponsible() {
            const input = document.getElementById('area_responsible_id');
            const errorMessage = document.getElementById('area_responsible_id_error');
            const value = input.value.trim();
            const hasOptions = input.options.length > 1; // More than "اختر المسئول"

            if (hasOptions && value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'يرجى اختيار مسؤول المنطقة.';
                input.style.borderColor = 'red';
                return false;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                input.style.borderColor = '';
                return true;
            }
        }

        // City -> Neighborhood
        document.getElementById('current_city').addEventListener('change', function () {
            const selectedCity = this.value;
            const neighborhoodSelect = document.getElementById('neighborhood');
            const areaResponsibleSelect = document.getElementById('area_responsible_id');
            const blockSelect = document.getElementById('block_id');

            neighborhoodSelect.innerHTML = '<option value="">اختر الحي السكني</option>';
            areaResponsibleSelect.innerHTML = '<option value="">اختر المسؤول</option>';
            blockSelect.innerHTML = '<option value="">اختر المندوب</option>';

            document.getElementById('areaResponsibleField').style.display = 'none';
            document.getElementById('blockField').style.display = 'none';

            if (selectedCity && neighborhoodsData[selectedCity]) {
                const neighborhoods = neighborhoodsData[selectedCity];
                for (const [id, name] of Object.entries(neighborhoods)) {
                    const option = document.createElement('option');
                    option.value = name; // Storing Name in DB
                    option.textContent = name;
                    neighborhoodSelect.appendChild(option);
                }
            }
        });

        // Neighborhood -> Area Responsible
        document.getElementById('neighborhood').addEventListener('change', function() {
            const selectedNeighborhood = this.value;
            const areaResponsibleSelect = document.getElementById('area_responsible_id');
            const areaResponsibleField = document.getElementById('areaResponsibleField');
            const blockSelect = document.getElementById('block_id');
            const blockField = document.getElementById('blockField');

            areaResponsibleSelect.innerHTML = '<option value="">اختر المسؤول</option>';
            blockSelect.innerHTML = '<option value="">اختر المندوب</option>';
            blockField.style.display = 'none';

            if (selectedNeighborhood && responsiblesData[selectedNeighborhood]) {
                const responsibles = responsiblesData[selectedNeighborhood];
                const keys = Object.keys(responsibles);
                if (keys.length > 0) {
                    areaResponsibleField.style.display = 'block';
                    for (const [id, name] of Object.entries(responsibles)) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = name;
                        areaResponsibleSelect.appendChild(option);
                    }
                } else {
                    areaResponsibleField.style.display = 'none';
                }
            } else {
                areaResponsibleField.style.display = 'none';
            }
        });

        // Area Responsible -> Block
        document.getElementById('area_responsible_id').addEventListener('change', function() {
            const selectedResponsible = this.value;
            const blockSelect = document.getElementById('block_id');
            const blockField = document.getElementById('blockField');

            blockSelect.innerHTML = '<option value="">اختر المندوب</option>';

            if (selectedResponsible && blocksData[selectedResponsible]) {
                const blocks = blocksData[selectedResponsible];
                const keys = Object.keys(blocks);
                if (keys.length > 0) {
                    // blockField.style.display = 'block'; // Hidden as per user request
                    for (const [id, name] of Object.entries(blocks)) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = name;
                        blockSelect.appendChild(option);
                    }
                } else {
                    blockField.style.display = 'none';
                }
            } else {
                blockField.style.display = 'none';
            }
        });
    </script>
</body>
</html>
