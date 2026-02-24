<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جمعية الفجر الشبابي الفلسطيني</title>

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
            background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
        }

        /* ========== زينة رمضان ========== */

        /* الفوانيس المعلقة المرتبة */
        .lanterns-top {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 180px;
            z-index: 5;
            pointer-events: none;
        }

        /* السلك الحامل للفوانيس */
        .lantern-wire {
            position: absolute;
            top: 8px;
            left: 0;
            width: 100%;
            height: 30px;
        }

        .lantern {
            position: absolute;
            top: 20px;
            animation: lanternSwing 5s ease-in-out infinite;
            transform-origin: top center;
        }

        .lantern svg {
            filter: drop-shadow(0 8px 15px rgba(255, 111, 0, 0.2));
        }

        @keyframes lanternSwing {
            0%, 100% { transform: rotate(-4deg); }
            50% { transform: rotate(4deg); }
        }

        /* توزيع الفوانيس بشكل مرتب ومتناظر */
        .lantern-1 { left: 5%; animation-delay: 0s; }
        .lantern-2 { left: 20%; animation-delay: 0.7s; }
        .lantern-3 { left: 35%; animation-delay: 1.4s; }
        .lantern-4 { left: 50%; transform: translateX(-50%); animation-delay: 2.1s; }
        .lantern-5 { left: 65%; animation-delay: 1.4s; animation-direction: reverse; }
        .lantern-6 { left: 80%; animation-delay: 0.7s; animation-direction: reverse; }
        .lantern-7 { left: 95%; animation-delay: 0s; animation-direction: reverse; }

        /* الهلال الكبير في الأسفل */
        .big-moon-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 220px;
            z-index: 2;
            pointer-events: none;
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .big-moon {
            position: relative;
            bottom: 30px;
            animation: moonFloat 6s ease-in-out infinite;
        }

        @keyframes moonFloat {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        /* زينة الأضواء المعلقة على الهلال */
        .hanging-lights {
            position: absolute;
            bottom: 180px;
            left: 0;
            width: 100%;
            height: 120px;
            z-index: 1;
            pointer-events: none;
        }

        .light-chain {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .light-bulb-item {
            position: absolute;
            width: 14px;
            height: 20px;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: bulbGlow 2s ease-in-out infinite;
        }

        .light-bulb-item::after {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 10px;
            background: #8D6E63;
            border-radius: 2px 2px 0 0;
        }

        @keyframes bulbGlow {
            0%, 100% { opacity: 0.6; filter: brightness(0.9); }
            50% { opacity: 1; filter: brightness(1.2); }
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
            box-shadow: 0 0 20px rgba(255, 111, 0, 0.2);
        }

        .bubble:nth-child(1) { width: 90px; height: 90px; left: 5%; animation-duration: 8s; animation-delay: 0s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.3), rgba(255, 111, 0, 0.1)); }
        .bubble:nth-child(2) { width: 70px; height: 70px; left: 15%; animation-duration: 9s; animation-delay: 1s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.15)); }
        .bubble:nth-child(3) { width: 110px; height: 110px; left: 25%; animation-duration: 7s; animation-delay: 0.5s; background: radial-gradient(circle at 30% 30%, rgba(230, 81, 0, 0.25), rgba(230, 81, 0, 0.08)); }
        .bubble:nth-child(4) { width: 80px; height: 80px; left: 35%; animation-duration: 8.5s; animation-delay: 1.5s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.12)); }
        .bubble:nth-child(5) { width: 100px; height: 100px; left: 45%; animation-duration: 7.5s; animation-delay: 0.3s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.28), rgba(255, 111, 0, 0.1)); }
        .bubble:nth-child(6) { width: 85px; height: 85px; left: 55%; animation-duration: 8.2s; animation-delay: 0.8s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.48), rgba(255, 255, 255, 0.15)); }
        .bubble:nth-child(7) { width: 65px; height: 65px; left: 65%; animation-duration: 9.5s; animation-delay: 1.2s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.32), rgba(255, 111, 0, 0.12)); }
        .bubble:nth-child(8) { width: 95px; height: 95px; left: 75%; animation-duration: 7.8s; animation-delay: 0.2s; background: radial-gradient(circle at 30% 30%, rgba(230, 81, 0, 0.22), rgba(230, 81, 0, 0.08)); }
        .bubble:nth-child(9) { width: 75px; height: 75px; left: 85%; animation-duration: 8.8s; animation-delay: 1.8s; background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.18)); }
        .bubble:nth-child(10) { width: 105px; height: 105px; left: 95%; animation-duration: 7.3s; animation-delay: 0.6s; background: radial-gradient(circle at 30% 30%, rgba(255, 111, 0, 0.26), rgba(255, 111, 0, 0.1)); }

        @keyframes rise {
            0% { bottom: -150px; transform: translateX(0) rotate(0deg) scale(0.8); opacity: 0; }
            5% { opacity: 0.7; }
            95% { opacity: 0.7; }
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,50 Q300,0 600,50 T1200,50 L1200,120 L0,120 Z" fill="rgba(255,111,0,0.15)"/></svg>');
            background-size: 50% 100%;
            animation: wave 15s linear infinite;
        }

        .wave:nth-child(2) {
            bottom: 15px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,70 Q300,20 600,70 T1200,70 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.35)"/></svg>');
            background-size: 50% 100%;
            animation: wave 12s linear infinite reverse;
        }

        .wave:nth-child(3) {
            bottom: 30px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,40 Q300,10 600,40 T1200,40 L1200,120 L0,120 Z" fill="rgba(230,81,0,0.12)"/></svg>');
            background-size: 50% 100%;
            animation: wave 18s linear infinite;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            margin-top: 150px;
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
        .form-container {
            max-width: 500px;
            margin: 0 auto;
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

        .status-message i {
            margin-left: 6px;
        }

        /* الأزرار */
        .action-btn {
            width: 100%;
            padding: 17px;
            font-size: 17px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
        }

        .btn-primary::before {
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

        .btn-primary:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #FF6F00;
            border: 2px solid #FF6F00;
            box-shadow: 0 3px 10px rgba(255, 111, 0, 0.2);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.1), rgba(230, 81, 0, 0.08));
            border-color: #E65100;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 111, 0, 0.3);
        }

        .action-btn i {
            margin-left: 8px;
            position: relative;
            z-index: 1;
        }

        .action-btn span {
            position: relative;
            z-index: 1;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            color: #999;
            font-size: 14px;
            font-weight: 600;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 42%;
            height: 2px;
            background: linear-gradient(to left, #FF6F00, transparent);
        }

        .divider::before { right: 0; }
        .divider::after { left: 0; background: linear-gradient(to right, #FF6F00, transparent); }

        /* Responsive */
        @media (max-width: 968px) {
            .main-row { grid-template-columns: 1fr; }
            .sidebar { order: 2; }
            .main-content { order: 1; }
            .page-container { margin-top: 130px; }
            .big-moon-container { display: none; }
            .hanging-lights { display: none; }
        }

        @media (max-width: 768px) {
            body { padding: 15px; }
            .top-bar { padding: 18px 20px; flex-direction: column; text-align: center; }
            .top-bar h1 { font-size: 20px; }
            .sidebar { padding: 25px; }
            .main-content { padding: 30px 25px; }
            .welcome-box { padding: 28px; }
            .welcome-box h2 { font-size: 22px; }
            .page-container { margin-top: 110px; }
            .lanterns-top { height: 140px; }
        }

        @media (max-width: 480px) {
            .top-bar { padding: 15px; }
            .logo-small { width: 50px; height: 50px; }
            .top-bar h1 { font-size: 18px; }
            .sidebar { padding: 20px; }
            .main-content { padding: 25px 20px; }
            .welcome-box { padding: 22px; }
            .welcome-box h2 { font-size: 20px; }
            .welcome-box p { font-size: 15px; }
            input[type="number"] { padding: 14px 50px 14px 16px; font-size: 15px; }
            .action-btn { padding: 15px; font-size: 16px; }
        }
    </style>
</head>
<body>
    <!-- فوانيس معلقة مرتبة -->
    <div class="lanterns-top">
        <!-- السلك -->
        <svg class="lantern-wire" viewBox="0 0 1440 30" fill="none" preserveAspectRatio="none">
            <path d="M0 15 Q180 5 360 15 T720 15 T1080 15 T1440 15" stroke="#D4A574" stroke-width="3" fill="none"/>
        </svg>

        <!-- الفانوس 1 -->
        <div class="lantern lantern-1">
            <svg width="40" height="60" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="15" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="18" rx="8" ry="3" fill="#D4A574"/>
                <path d="M36 24 Q40 32 40 40 L60 40 Q60 32 64 24 Z" fill="#00897B"/>
                <path d="M40 40 Q32 75 40 110 L60 110 Q68 75 60 40 Z" fill="url(#g1)"/>
                <ellipse cx="50" cy="75" rx="8" ry="14" fill="#FFF8E1" opacity="0.9"/>
                <path d="M40 110 Q36 118 40 125 L60 125 Q64 118 60 110 Z" fill="#00897B"/>
                <defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#26A69A"/><stop offset="100%" style="stop-color:#00897B"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 2 -->
        <div class="lantern lantern-2">
            <svg width="50" height="75" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="12" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="15" rx="9" ry="3.5" fill="#D4A574"/>
                <path d="M34 21 Q38 30 38 38 L62 38 Q62 30 66 21 Z" fill="#F9A825"/>
                <path d="M38 38 Q28 75 38 112 L62 112 Q72 75 62 38 Z" fill="url(#g2)"/>
                <ellipse cx="50" cy="75" rx="9" ry="15" fill="#FFF8E1" opacity="0.9"/>
                <path d="M38 112 Q34 120 38 127 L62 127 Q66 120 62 112 Z" fill="#F9A825"/>
                <defs><linearGradient id="g2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FBC02D"/><stop offset="100%" style="stop-color:#F9A825"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 3 -->
        <div class="lantern lantern-3">
            <svg width="60" height="90" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="10" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="13" rx="10" ry="4" fill="#D4A574"/>
                <path d="M32 19 Q36 28 36 36 L64 36 Q64 28 68 19 Z" fill="#E65100"/>
                <path d="M36 36 Q26 75 36 114 L64 114 Q74 75 64 36 Z" fill="url(#g3)"/>
                <ellipse cx="50" cy="75" rx="10" ry="16" fill="#FFF8E1" opacity="0.9"/>
                <ellipse cx="50" cy="75" rx="6" ry="11" fill="#FFECB3"/>
                <circle cx="50" cy="75" r="4" fill="#FFE082" opacity="0.7">
                    <animate attributeName="opacity" values="0.3;0.8;0.3" dur="2s" repeatCount="indefinite"/>
                </circle>
                <path d="M36 114 Q32 122 36 130 L64 130 Q68 122 64 114 Z" fill="#E65100"/>
                <defs><linearGradient id="g3" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FF6F00"/><stop offset="100%" style="stop-color:#E65100"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 4 - الوسط الكبير -->
        <div class="lantern lantern-4">
            <svg width="75" height="110" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="8" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="11" rx="12" ry="5" fill="#D4A574"/>
                <path d="M28 18 Q34 28 34 38 L66 38 Q66 28 72 18 Z" fill="#E65100"/>
                <path d="M34 38 Q22 75 34 112 L66 112 Q78 75 66 38 Z" fill="url(#g4)"/>
                <ellipse cx="50" cy="75" rx="12" ry="20" fill="#FFF8E1" opacity="0.95"/>
                <ellipse cx="50" cy="75" rx="8" ry="15" fill="#FFECB3"/>
                <ellipse cx="50" cy="75" rx="4" ry="8" fill="#FFE082">
                    <animate attributeName="opacity" values="0.5;1;0.5" dur="2.5s" repeatCount="indefinite"/>
                </ellipse>
                <path d="M34 112 Q28 122 34 132 L66 132 Q72 122 66 112 Z" fill="#E65100"/>
                <ellipse cx="50" cy="135" rx="7" ry="3" fill="#D4A574"/>
                <circle cx="50" cy="143" r="4" fill="#FF6F00">
                    <animate attributeName="r" values="3;5;3" dur="2s" repeatCount="indefinite"/>
                </circle>
                <circle cx="40" cy="50" r="2.5" fill="#FFD54F"/>
                <circle cx="60" cy="50" r="2.5" fill="#FFD54F"/>
                <circle cx="38" cy="100" r="2.5" fill="#FFD54F"/>
                <circle cx="62" cy="100" r="2.5" fill="#FFD54F"/>
                <defs><linearGradient id="g4" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FF6F00"/><stop offset="50%" style="stop-color:#FF8F00"/><stop offset="100%" style="stop-color:#E65100"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 5 -->
        <div class="lantern lantern-5">
            <svg width="60" height="90" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="10" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="13" rx="10" ry="4" fill="#D4A574"/>
                <path d="M32 19 Q36 28 36 36 L64 36 Q64 28 68 19 Z" fill="#00897B"/>
                <path d="M36 36 Q26 75 36 114 L64 114 Q74 75 64 36 Z" fill="url(#g5)"/>
                <ellipse cx="50" cy="75" rx="10" ry="16" fill="#FFF8E1" opacity="0.9"/>
                <path d="M36 114 Q32 122 36 130 L64 130 Q68 122 64 114 Z" fill="#00897B"/>
                <defs><linearGradient id="g5" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#26A69A"/><stop offset="100%" style="stop-color:#00897B"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 6 -->
        <div class="lantern lantern-6">
            <svg width="50" height="75" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="12" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="15" rx="9" ry="3.5" fill="#D4A574"/>
                <path d="M34 21 Q38 30 38 38 L62 38 Q62 30 66 21 Z" fill="#E65100"/>
                <path d="M38 38 Q28 75 38 112 L62 112 Q72 75 62 38 Z" fill="url(#g6)"/>
                <ellipse cx="50" cy="75" rx="9" ry="15" fill="#FFF8E1" opacity="0.9"/>
                <path d="M38 112 Q34 120 38 127 L62 127 Q66 120 62 112 Z" fill="#E65100"/>
                <defs><linearGradient id="g6" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FF6F00"/><stop offset="100%" style="stop-color:#E65100"/></linearGradient></defs>
            </svg>
        </div>

        <!-- الفانوس 7 -->
        <div class="lantern lantern-7">
            <svg width="40" height="60" viewBox="0 0 100 150" fill="none">
                <line x1="50" y1="0" x2="50" y2="15" stroke="#D4A574" stroke-width="2"/>
                <ellipse cx="50" cy="18" rx="8" ry="3" fill="#D4A574"/>
                <path d="M36 24 Q40 32 40 40 L60 40 Q60 32 64 24 Z" fill="#F9A825"/>
                <path d="M40 40 Q32 75 40 110 L60 110 Q68 75 60 40 Z" fill="url(#g7)"/>
                <ellipse cx="50" cy="75" rx="8" ry="14" fill="#FFF8E1" opacity="0.9"/>
                <path d="M40 110 Q36 118 40 125 L60 125 Q64 118 60 110 Z" fill="#F9A825"/>
                <defs><linearGradient id="g7" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FBC02D"/><stop offset="100%" style="stop-color:#F9A825"/></linearGradient></defs>
            </svg>
        </div>
    </div>

    <!-- زينة الأضواء المعلقة -->
    <div class="hanging-lights" id="hangingLights"></div>

    <!-- الهلال الكبير -->
    <div class="big-moon-container">
        <div class="big-moon">
            <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- الهلال -->
                <circle cx="100" cy="100" r="80" fill="url(#moonGradient)"/>
                <circle cx="130" cy="80" r="65" fill="#fff8f0"/>
                <!-- توهج -->
                <circle cx="100" cy="100" r="90" fill="url(#moonGlow)" opacity="0.4"/>
                <!-- نجمة على الهلال -->
                <path d="M60 90 L63 98 L72 98 L65 103 L68 112 L60 107 L52 112 L55 103 L48 98 L57 98 Z" fill="#FFD54F">
                    <animate attributeName="opacity" values="0.7;1;0.7" dur="2s" repeatCount="indefinite"/>
                </path>
                <!-- نجمة صغيرة -->
                <path d="M75 70 L77 75 L82 75 L78 78 L80 83 L75 80 L70 83 L72 78 L68 75 L73 75 Z" fill="#FFD54F">
                    <animate attributeName="opacity" values="1;0.6;1" dur="1.5s" repeatCount="indefinite"/>
                </path>
                <defs>
                    <linearGradient id="moonGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#FFE082"/>
                        <stop offset="50%" style="stop-color:#FFD54F"/>
                        <stop offset="100%" style="stop-color:#FFC107"/>
                    </linearGradient>
                    <radialGradient id="moonGlow">
                        <stop offset="0%" stop-color="#FFD54F"/>
                        <stop offset="100%" stop-color="transparent"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>
    </div>

    <!-- الفقاعات -->
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
    </div>

    <!-- الموجات -->
    <div class="wave-container">
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
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
                    <h2>مرحباً بكم في نظام التسجيل الإلكتروني</h2>
                    <p>يمكنكم التسجيل وتحديث بياناتكم بسهولة وأمان</p>
                </div>

                <!-- النموذج -->
                <div class="form-container">
                    <div class="form-step">
                        <div class="step-number">1</div>
                        <h3>أدخل رقم الهوية للبدء</h3>

                        <form action="{{ url('/set-session') }}" method="POST" onsubmit="return validateIdNumber()">
                            @csrf

                            <div class="input-container">
                                <label>رقم الهوية الفلسطيني</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input
                                        type="number"
                                        id="id_num"
                                        name="id_num"
                                        placeholder="أدخل 9 أرقام"
                                        required
                                        oninput="validateIdOnInput()"
                                        maxlength="9"
                                        autocomplete="off">
                                </div>
                                <div id="error_msg" class="status-message error">
                                    <i class="fas fa-times-circle"></i> رقم الهوية غير صالح
                                </div>
                                <div id="success_msg" class="status-message success">
                                    <i class="fas fa-check-circle"></i> رقم الهوية صحيح
                                </div>
                            </div>

                            <button type="submit" class="action-btn btn-primary">
                                <span>
                                    <i class="fas fa-arrow-left"></i>
                                    المتابعة
                                </span>
                            </button>
                        </form>

                        <div class="divider">أو</div>

                        <button class="action-btn btn-secondary" onclick="window.location.href='{{ route('loginView') }}'">
                            <i class="fas fa-sign-in-alt"></i>
                            تسجيل الدخول
                        </button>

                        <button class="action-btn btn-secondary" onclick="window.location.href='{{ route('complaint') }}'">
                            <i class="fas fa-comment-dots"></i>
                            تقديم شكوى
                        </button>
                    </div>
                </div>
            </div>

            <!-- القسم الجانبي -->
            <div class="sidebar">
                <h2>عن الجمعية</h2>

                <div class="info-item">
                    <h3><i class="fas fa-info-circle"></i> نبذة</h3>
                    <p>جمعية أهلية خيرية مرخصة عام 2002م، تعمل لخدمة المجتمع الفلسطيني في قطاع غزة.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-heart"></i> خطة الطوارئ</h3>
                    <p>نعمل منذ 09/10/2023م ضمن خطة طوارئ لتقديم المساعدات للنازحين.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-laptop"></i> النظام الإلكتروني</h3>
                    <p>نظام تسجيل حديث للوصول السريع وتقديم الخدمات بكفاءة عالية.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // إنشاء زينة الأضواء المعلقة
        function createHangingLights() {
            const container = document.getElementById('hangingLights');
            const colors = ['#FF6B6B', '#FFE66D', '#4ECDC4', '#FF8A65', '#B388FF', '#FFD54F', '#81C784'];
            const numLights = 20;

            for (let i = 0; i < numLights; i++) {
                const bulb = document.createElement('div');
                bulb.className = 'light-bulb-item';
                bulb.style.left = (i * 5 + 2.5) + '%';
                bulb.style.background = colors[i % colors.length];
                bulb.style.boxShadow = `0 0 10px ${colors[i % colors.length]}, 0 0 20px ${colors[i % colors.length]}`;
                bulb.style.animationDelay = (i * 0.15) + 's';

                // ارتفاع متفاوت للسلك
                const baseTop = 10;
                const variation = Math.sin(i * 0.6) * 20;
                bulb.style.top = (baseTop + variation + 50) + 'px';

                container.appendChild(bulb);
            }
        }

        createHangingLights();

        function luhnCheck(num) {
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
        }

        function validateIdOnInput() {
            const idNum = document.getElementById('id_num').value;
            const errorMessage = document.getElementById('error_msg');
            const successMessage = document.getElementById('success_msg');
            const inputField = document.getElementById('id_num');

            if (idNum.length > 9) {
                document.getElementById('id_num').value = idNum.slice(0, 9);
            }

            if (idNum.length === 9 && !luhnCheck(idNum)) {
                inputField.style.borderColor = '#c62828';
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
            } else if (idNum.length === 9 && luhnCheck(idNum)) {
                inputField.style.borderColor = '#2e7d32';
                errorMessage.style.display = 'none';
                successMessage.style.display = 'block';
            } else {
                inputField.style.borderColor = '#e0e0e0';
                errorMessage.style.display = 'none';
                successMessage.style.display = 'none';
            }
        }

        function validateIdNumber() {
            const idNum = document.getElementById('id_num').value;

            if (idNum.length === 9 && !luhnCheck(idNum)) {
                Swal.fire({
                    icon: 'error',
                    title: 'رقم الهوية غير صالح',
                    text: 'الرجاء التأكد من إدخال رقم هوية صحيح.',
                    confirmButtonColor: '#FF6F00',
                    confirmButtonText: 'إغلاق'
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
