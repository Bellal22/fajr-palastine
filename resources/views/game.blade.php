<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عجلة الحظ - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

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

        .floating-circle:nth-child(1) {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(255, 111, 0, 0.25) 0%, transparent 70%);
            top: -100px; right: -100px;
            animation-duration: 12s;
        }

        .floating-circle:nth-child(2) {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
            bottom: -80px; left: -80px;
            animation-duration: 15s;
            animation-delay: 2s;
        }

        @keyframes float-rotate {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            50% { transform: translate(-30px, 60px) rotate(180deg) scale(0.9); }
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

        .particle:nth-child(1) { background: #FF6F00; left: 8%; animation-duration: 7s; }
        .particle:nth-child(2) { background: rgba(255, 255, 255, 0.8); left: 18%; animation-duration: 8s; }
        .particle:nth-child(3) { background: #E65100; left: 28%; animation-duration: 6.5s; }

        @keyframes particle-float {
            0% { bottom: -10px; opacity: 0; transform: translateX(0) scale(0.5); }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% { bottom: 110%; opacity: 0; transform: translateX(80px) scale(1.5); }
        }

        .page-container {
            max-width: 1200px;
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
        }

        .sidebar h2 {
            color: #333;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6F00;
            text-align: right;
        }

        .info-item {
            margin-bottom: 20px;
            padding: 18px;
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.08), rgba(230, 81, 0, 0.05));
            border-radius: 12px;
            border-right: 4px solid #FF6F00;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(255, 111, 0, 0.1);
            text-align: right;
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

        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
        }

        .input-container {
            margin-bottom: 20px;
        }

        .input-container label {
            display: block;
            color: #555;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: right;
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
            font-size: 20px;
        }

        input[type="number"] {
            width: 100%;
            padding: 16px 55px 16px 18px;
            font-size: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            background: white;
            text-align: right;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 5px rgba(255, 111, 0, 0.15);
            transform: translateY(-2px);
        }

        .status-message {
            margin-top: 12px;
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 14px;
            display: none;
            text-align: right;
            animation: slideInRight 0.4s ease;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .status-message.error {
            background: #ffebee;
            color: #c62828;
            border-right: 4px solid #c62828;
        }

        .status-message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }

        .status-message i { margin-left: 6px; }

        /* النموذج */
        .form-container {
            max-width: 500px;
            margin: 0 auto 35px;
        }

        .form-step {
            text-align: center;
            margin-bottom: 30px;
        }

        .step-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            border-radius: 50%;
            line-height: 50px;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(255, 111, 0, 0.4);
            animation: pulse 2.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(255, 111, 0, 0.4); }
            50% { transform: scale(1.08); box-shadow: 0 6px 25px rgba(255, 111, 0, 0.6); }
        }

        .form-step h3 {
            color: #333;
            font-size: 19px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            color: #999;
            font-size: 14px;
            font-weight: 600;
            position: relative;
        }

        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 35%;
            height: 2px;
            background: linear-gradient(to left, #FF6F00, transparent);
        }

        .divider::before { right: 0; }
        .divider::after { left: 0; background: linear-gradient(to right, #FF6F00, transparent); }

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
            animation: slide-pattern 25s linear infinite;
        }

        @keyframes slide-pattern {
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
            color: #fff !important;
        }

        .welcome-box p {
            font-size: 16px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            color: #fff !important;
            margin-bottom: 0;
        }

        /* Wheel Styles */
        :root {
            --wheel-size: 400px;
        }

        .game-view {
            position: relative;
            max-width: 500px;
            margin: 0 auto 40px;
        }

        .wheel {
            width: var(--wheel-size);
            height: var(--wheel-size);
            border-radius: 50%;
            border: 10px solid #fff;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            transition: transform 5s cubic-bezier(0.15, 0, 0.15, 1);
            background: #f8f9fa;
            margin: 0 auto;
        }

        .pointer {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: #e74c3c;
            clip-path: polygon(100% 0%, 50% 100%, 0% 0%);
            z-index: 10;
        }

        .spin-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: #fff;
            border: 5px solid #FF6F00;
            border-radius: 50%;
            z-index: 100;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: 0.2s;
        }

        .spin-btn:hover { transform: translate(-50%, -50%) scale(1.1); }

        #result-card {
            background: linear-gradient(135deg, #FF6F00 0%, #E65100 100%);
            border-radius: 20px;
            color: white;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.4);
            display: none;
            animation: slideIn 0.5s ease;
            text-align: center;
            direction: rtl;
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .code-display {
            background: rgba(255,255,255,0.2);
            border: 2px dashed #fff;
            border-radius: 10px;
            padding: 15px;
            font-size: 24px;
            font-weight: 700;
            margin: 15px 0;
            letter-spacing: 2px;
        }

        #hard-luck-card {
            background: linear-gradient(135deg, #FF6F00 0%, #E65100 100%);
            border-radius: 20px;
            color: white;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 8px 25px rgba(230, 81, 0, 0.4);
            display: none;
            animation: slideIn 0.5s ease;
            text-align: center;
            direction: rtl;
        }

        /* نمط النقش في الخلفية للبطاقات */
        #result-card::before, #hard-luck-card::before {
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
            animation: slide-pattern 25s linear infinite;
            pointer-events: none;
        }

        #result-card, #hard-luck-card {
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 992px) {
            .main-row { grid-template-columns: 1fr; }
            .sidebar { order: 2; margin-top: 25px; }
            :root { --wheel-size: 320px; }
        }

        @media (max-width: 768px) {
            body { padding: 15px; }
            .main-content { padding: 30px 20px; }
            .welcome-box { padding: 25px; }
            :root { --wheel-size: 280px; }
        }
    </style>
</head>
<body>
    <div class="bubbles">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="wave-container">
        <div class="wave"></div><div class="wave"></div>
    </div>

    <div class="floating-circles">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="page-container">
        <div class="top-bar">
            <img src="{{asset('background/image.jpg')}}" alt="الشعار" class="logo-small">
            <h1>جمعية <span>الفجر الشبابي الفلسطيني</span></h1>
        </div>

        <div class="main-row">
            <div class="main-content">
                <div class="welcome-box">
                    <h2>🎡 عجلة الحظ الكبرى</h2>
                    <p>جرب حظك الآن واربح طرود هدايا قيمة من جمعية الفجر!</p>
                </div>

                <div class="form-container">
                    <div class="form-step">
                        <div class="step-number">1</div>
                        <h3>أدخل رقم الهوية للبدء</h3>

                        <div class="input-container">
                            <label>رقم الهوية الفلسطيني</label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input 
                                    type="number" 
                                    id="id_number" 
                                    placeholder="أدخل 9 أرقام" 
                                    maxlength="9"
                                    oninput="validateIdOnInput()"
                                    autocomplete="off">
                            </div>
                            <div id="error_msg" class="status-message error">
                                <i class="fas fa-times-circle"></i> رقم الهوية غير صالح
                            </div>
                            <div id="success_msg" class="status-message success">
                                <i class="fas fa-check-circle"></i> رقم الهوية صحيح
                            </div>
                        </div>

                        <div class="divider">ثم</div>
                    </div>
                </div>

                <div class="game-view">
                    <div class="pointer"></div>
                    <div id="wheel" class="wheel" style="background: conic-gradient(
                        @foreach($segments as $index => $segment)
                            {{ $index % 2 == 0 ? '#FFD8B1' : '#FFB366' }} 
                            calc({{ $index }} * 360deg / {{ $segments->count() }}) 
                            calc(({{ $index }} + 1) * 360deg / {{ $segments->count() }}),
                        @endforeach
                        #f8f9fa 0
                    );">
                    </div>
                    <button id="spin-btn" class="spin-btn">إبدأ!</button>
                </div>

                <div id="result-card">
                    <h3 class="mb-2">🎉 مبروووك! لقد ربحت 🎊</h3>
                    <h2 id="prize-name" class="mb-3" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">---</h2>
                    <div class="code-display" id="winning-code">-------</div>
                    <p>احتفظ بهذا الكود جيداً، وتوجه إلى أقرب فرع للاستلام.</p>
                </div>

                <div id="hard-luck-card">
                    <div class="mb-3 display-4">😢</div>
                    <h3 class="mb-3">حظ أوفر في المرة القادمة</h3>
                    <p class="mb-0 text-white-50">لم يحالفك الحظ هذه المرة، نشكر مشاركتك!</p>
                </div>
            </div>

            <div class="sidebar">
                <h2>كيفية المشاركة؟</h2>

                <div class="info-item">
                    <h3><i class="fas fa-id-card ml-2"></i> أدخل رقم هويتك</h3>
                    <p>يجب إدخال رقم هويتك الصحيح لنتمكن من توثيق جائزتك باسمك.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-play-circle ml-2"></i> اضغط "إبدأ"</h3>
                    <p>اضغط على زر الانطلاق في منتصف العجلة لتبدأ الدوران.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-gift ml-2"></i> انتظر النتيجة</h3>
                    <p>ستتوقف العجلة تلقائياً لتكشف عن جائزتك المميزة.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-check-double ml-2"></i> مراجعة المقر</h3>
                    <p>سيتم مطابقة الكود من قبل موظفينا لتسليمك جائزتك.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');
            const resultCard = document.getElementById('result-card');
            const prizeName = document.getElementById('prize-name');
            const winningCode = document.getElementById('winning-code');
            const idInput = document.getElementById('id_number');

            let isSpinning = false;
            let rotation = 0;

            // Luhn Check Algorithm
            window.luhnCheck = function(num) {
                const digits = num.toString().split('').map(Number);
                let checksum = 0;
                const numDigits = digits.length;
                const parity = numDigits % 2;

                for (let i = 0; i < numDigits; i++) {
                    let digit = digits[i];
                    if (i % 2 === parity) {
                        digit *= 2;
                        if (digit > 9) digit -= 9;
                    }
                    checksum += digit;
                }
                return checksum % 10 === 0;
            };

            window.validateIdOnInput = function() {
                const idNum = idInput.value;
                const errorMessage = document.getElementById('error_msg');
                const successMessage = document.getElementById('success_msg');

                if (idNum.length > 9) {
                    idInput.value = idNum.slice(0, 9);
                }

                if (idInput.value.length === 9 && !luhnCheck(idInput.value)) {
                    idInput.style.borderColor = '#c62828';
                    errorMessage.style.display = 'block';
                    successMessage.style.display = 'none';
                } else if (idInput.value.length === 9 && luhnCheck(idInput.value)) {
                    idInput.style.borderColor = '#2e7d32';
                    errorMessage.style.display = 'none';
                    successMessage.style.display = 'block';
                } else {
                    idInput.style.borderColor = '#e0e0e0';
                    errorMessage.style.display = 'none';
                    successMessage.style.display = 'none';
                }
            };

            spinBtn.addEventListener('click', function() {
                if (isSpinning) return;

                const idNumber = idInput.value.trim();
                if (!idNumber || idNumber.length !== 9 || !luhnCheck(idNumber)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'رقم الهوية غير صالح',
                        text: 'يرجى إدخال رقم هوية صحيح (9 أرقام) للمشاركة.',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    });
                    idInput.focus();
                    return;
                }

                isSpinning = true;
                spinBtn.disabled = true;
                idInput.disabled = true;
                resultCard.style.display = 'none';
                document.getElementById('hard-luck-card').style.display = 'none';

                fetch('{{ route("game.spin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_number: idNumber
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const extraSpins = 5 + Math.floor(Math.random() * 5);
                        const segmentCount = {{ $segments->count() }};
                        
                        // Backend tells us exactly which segment index (0-based)
                        // Note: Wheel segments usually start at 12 o'clock and go clockwise
                        // But CSS conic-gradient starts at 12 o'clock.
                        // To stop AT the segment, we need to rotate execution.
                        // Calculate angle for the center of the target segment
                        const segmentAngle = 360 / segmentCount;
                        const targetIndex = data.prize_index;
                        
                        // We want the POINTER (top) to point to the segment.
                        // If segment start is at angle S, end at E. Center is (S+E)/2.
                        // Rotation must bring that center to 0 deg (top).
                        // So rotate = -centerAngle.
                        // But we want to spin forward (positive).
                        // targetAngle to subtract = (targetIndex * segmentAngle) + (segmentAngle / 2)
                        
                        const stopAngle = (targetIndex * segmentAngle) + (segmentAngle / 2);
                        const targetRotation = (extraSpins * 360) - stopAngle;
                        
                        // Adjust existing rotation to be cumulative
                        // Make sure we spin enough forward
                        const currentMod = rotation % 360;
                        rotation += (extraSpins * 360) - currentMod - stopAngle;
                        
                        // Ensure positive spinning
                         if (rotation < 0) rotation += 3600;

                        wheel.style.transform = `rotate(${rotation}deg)`;

                        setTimeout(() => {
                            isSpinning = false;
                            spinBtn.disabled = false;
                            idInput.disabled = false;
                            
                            if (data.is_win) {
                                // WIN Logic
                                prizeName.textContent = data.prize;
                                winningCode.textContent = data.code;
                                resultCard.style.display = 'block';
                                resultCard.scrollIntoView({ behavior: 'smooth' });

                                // Fireworks
                                var duration = 3 * 1000;
                                var animationEnd = Date.now() + duration;
                                var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
                                function random(min, max) { return Math.random() * (max - min) + min; }
                                var interval = setInterval(function() {
                                  var timeLeft = animationEnd - Date.now();
                                  if (timeLeft <= 0) return clearInterval(interval);
                                  var particleCount = 50 * (timeLeft / duration);
                                  confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.1, 0.3), y: Math.random() - 0.2 } }));
                                  confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.7, 0.9), y: Math.random() - 0.2 } }));
                                }, 250);
                            } else {
                                // HARD LUCK Logic
                                const hardLuckCard = document.getElementById('hard-luck-card');
                                hardLuckCard.style.display = 'block';
                                hardLuckCard.scrollIntoView({ behavior: 'smooth' });
                            }
                        }, 5000); // 5 seconds match CSS transition (assumed 5s based on previous code)
                    } else {
                        isSpinning = false;
                        spinBtn.disabled = false;
                        idInput.disabled = false;

                        if (data.redirect) {
                            Swal.fire({
                                icon: 'info',
                                text: data.message,
                                confirmButtonColor: '#FF6F00',
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'تنبيه',
                                text: data.message,
                                confirmButtonColor: '#FF6F00',
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'عذراً، حدث خطأ في الاتصال.',
                        confirmButtonColor: '#d33',
                    });
                    isSpinning = false;
                    spinBtn.disabled = false;
                    idInput.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
