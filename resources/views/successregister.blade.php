<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التسجيل بنجاح - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

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

        /* المحتوى الرئيسي */
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
        }

        /* صندوق الترحيب */
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
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .welcome-box p {
            font-size: 18px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .welcome-box .success-icon {
            font-size: 70px;
            margin-bottom: 15px;
            display: inline-block;
            animation: checkmark 0.8s ease-out;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* صندوق الكود */
        .code-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 2px solid #FFE5CC;
        }

        .code-section h3 {
            color: #FF6F00;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .code-section h3 i {
            margin-left: 8px;
        }

        .code-display {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            padding: 20px;
            border-radius: 12px;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 4px;
            box-shadow: 0 6px 20px rgba(255, 111, 0, 0.3);
            margin-bottom: 15px;
        }

        .code-note {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }

        .code-note i {
            color: #FF6F00;
            margin-left: 5px;
        }

        /* زر العودة */
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.5);
        }

        .back-btn i {
            font-size: 20px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
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
                padding: 25px;
            }

            .welcome-box h2 {
                font-size: 24px;
            }

            .welcome-box p {
                font-size: 16px;
            }

            .welcome-box .success-icon {
                font-size: 55px;
            }

            .code-display {
                font-size: 28px;
                letter-spacing: 2px;
            }

            .back-btn {
                padding: 12px 30px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 25px 20px;
            }

            .welcome-box h2 {
                font-size: 20px;
            }

            .welcome-box .success-icon {
                font-size: 45px;
            }

            .code-display {
                font-size: 24px;
            }

            .back-btn {
                width: 100%;
                justify-content: center;
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
                    <div class="success-icon">✓</div>
                    <h2>تم التسجيل بنجاح!</h2>
                    <p>تم تسجيل بياناتك للاستفادة من جمعية الفجر الشبابي الفلسطيني بنجاح</p>
                </div>

                <!-- صندوق الكود -->
                <div class="code-section">
                    <h3><i class="fas fa-key"></i> كود التسجيل الخاص بك</h3>
                    <div class="code-display">{{ $passkey }}</div>
                    <p class="code-note">
                        <i class="fas fa-bookmark"></i>
                        يرجى الاحتفاظ بهذا الكود لمراجعة بياناتك لاحقاً 🤗
                    </p>
                </div>

                <!-- زر العودة -->
                <div class="action-buttons">
                    <a href="{{ route('persons.intro') }}" class="back-btn">
                        <i class="fas fa-home"></i>
                        العودة إلى الصفحة الرئيسية
                    </a>
                </div>
            </div>

            <!-- القسم الجانبي -->
            <div class="sidebar">
                <h2>معلومات مهمة</h2>

                <div class="info-item">
                    <h3><i class="fas fa-key"></i> احتفظ بالكود</h3>
                    <p>قم بحفظ كود التسجيل الخاص بك في مكان آمن، ستحتاجه لمراجعة أو تعديل بياناتك لاحقاً</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-clock"></i> وقت المعالجة</h3>
                    <p>سيتم مراجعة طلبك خلال 48 ساعة من التسجيل، وسنتواصل معك في حال الحاجة لأي معلومات إضافية</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-phone"></i> تواصل معنا</h3>
                    <p>في حال واجهتك أي مشكلة أو كان لديك أي استفسار، لا تتردد في التواصل معنا عبر قنوات الاتصال المتاحة</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-heart"></i> شكراً لثقتك</h3>
                    <p>نشكرك على تسجيلك في جمعية الفجر الشبابي الفلسطيني، ونتطلع لخدمتك بأفضل طريقة ممكنة ❤️</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
