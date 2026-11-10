@php
    $isDashboard = request()->is('dashboard*') || request()->is('*/dashboard*');
    $title = '404 - الصفحة غير موجودة';
    $errorCode = '404';
    $errorTitle = 'المورد المطلوب غير موجود';
    $errorMessage = 'نعتذر، ولكن الصفحة التي تبحث عنها غير موجودة أو تم نقلها.';
@endphp

@if($isDashboard)
<!DOCTYPE html>
<html dir="{{ Locales::getDir() }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if(Locales::getDir() == 'rtl')
        <link rel="stylesheet" href="{{ asset(mix('/css/adminlte3.rtl.css')) }}">
    @else
        <link rel="stylesheet" href="{{ asset(mix('/css/adminlte3.css')) }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: #fff;
            padding: 45px 50px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            text-align: center;
            max-width: 650px;
            width: 100%;
        }
        .logo-img {
            transition: transform 0.5s ease;
        }
        .logo-img:hover {
            transform: rotate(360deg) scale(1.1);
        }
    </style>
</head>
<body class="hold-transition">
    <div class="error-card">
        <!-- Logo & Name -->
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 15px; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; flex-wrap: nowrap;">
            <img src="{{ asset('background/image.jpg') }}" alt="الشعار" class="logo-img" style="width: 75px; height: 75px; border-radius: 50%; border: 3px solid #007bff; object-fit: cover; flex-shrink: 0;">
            <h2 style="font-weight: 700; color: #333; font-size: 20px; margin: 0; white-space: nowrap; line-height: 1.2;">
                جمعية <span style="color: #007bff;">الفجر الشبابي الفلسطيني</span>
            </h2>
        </div>

        <div class="mb-3 d-flex justify-content-center">
            <svg viewBox="0 0 200 200" width="130" height="130" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 160h160" stroke="#ddd" stroke-width="4" stroke-linecap="round"/>
                <path d="M100 160v-60" stroke="#ccc" stroke-width="6" stroke-linecap="round"/>
                <path d="M50 60h100v40H50z" fill="#007bff" rx="8"/>
                <path d="M90 70h20M90 80h20" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
                <circle cx="140" cy="120" r="22" fill="#fff" stroke="#0056b3" stroke-width="4"/>
                <path d="M156 136l20 20" stroke="#0056b3" stroke-width="6" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="display-1 text-danger font-weight-bold" style="font-size: 65px; margin-bottom: 15px; line-height: 1;">{{ $errorCode }}</h1>
        <h4 class="mb-3 font-weight-bold" style="color: #333;">{{ $errorTitle }}</h4>
        <p class="text-muted mb-4" style="font-size: 16px;">{{ $errorMessage }}</p>
        
        <div class="d-flex justify-content-center" style="gap: 15px;">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للخلف
            </a>
            <a href="{{ route('dashboard.home') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </div>
    </div>
</body>
</html>
@else
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
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

        @keyframes float-rotate {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(50px, -50px) rotate(90deg) scale(1.15); }
            50% { transform: translate(-30px, 60px) rotate(180deg) scale(0.9); }
            75% { transform: translate(60px, 30px) rotate(270deg) scale(1.1); }
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(255, 111, 0, 0.1);
            text-align: center;
            max-width: 650px;
            width: 100%;
            position: relative;
            z-index: 10;
            border-top: 5px solid #FF6F00;
        }

        .error-code {
            font-size: 100px;
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(135deg, #FF6F00, #E65100);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .error-title {
            color: #333;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .error-message {
            color: #666;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 35px;
        }

        .btn-row {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn-custom {
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 111, 0, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 111, 0, 0.4);
        }

        .btn-secondary-custom {
            background: white;
            color: #666;
            border: 2px solid #ddd;
        }

        .btn-secondary-custom:hover {
            background: #f9f9f9;
            border-color: #ccc;
            transform: translateY(-2px);
        }
        .logo-img {
            transition: transform 0.5s ease;
        }
        .logo-img:hover {
            transform: rotate(360deg) scale(1.1);
        }
    </style>
</head>
<body>
    <!-- الفقاعات -->
    <div class="bubbles">
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

    <!-- الدوائر الكبيرة -->
    <div class="floating-circles">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <div class="error-card">
        <!-- Logo & Name -->
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 15px; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; flex-wrap: nowrap;">
            <img src="{{ asset('background/image.jpg') }}" alt="الشعار" class="logo-img" style="width: 75px; height: 75px; border-radius: 50%; border: 3px solid #FF6F00; object-fit: cover; flex-shrink: 0;">
            <h2 style="font-weight: 700; color: #333; font-size: 20px; margin: 0; white-space: nowrap; line-height: 1.2;">
                جمعية <span style="color: #FF6F00;">الفجر الشبابي الفلسطيني</span>
            </h2>
        </div>

        <!-- Premium SVG Illustration: Not Found (404) -->
        <div class="mb-3 d-flex justify-content-center" style="animation: float 3s infinite ease-in-out;">
            <svg viewBox="0 0 200 200" width="130" height="130" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 160h160" stroke="#FF6F00" stroke-width="4" stroke-linecap="round"/>
                <path d="M100 160v-60" stroke="#8d6e63" stroke-width="6" stroke-linecap="round"/>
                <path d="M50 60h100v40H50z" fill="#FF6F00" rx="8"/>
                <path d="M90 70h20M90 80h20" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
                <circle cx="140" cy="120" r="22" fill="#fff" stroke="#E65100" stroke-width="4"/>
                <path d="M156 136l20 20" stroke="#E65100" stroke-width="6" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="error-code">{{ $errorCode }}</div>
        <h2 class="error-title">{{ $errorTitle }}</h2>
        <p class="error-message">{{ $errorMessage }}</p>

        <div class="btn-row">
            <a onclick="window.history.back()" class="btn btn-custom btn-secondary-custom">
                <i class="fas fa-arrow-right"></i> العودة للخلف
            </a>
            <a href="/" class="btn btn-custom btn-primary-custom">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>
</body>
</html>
@endif
