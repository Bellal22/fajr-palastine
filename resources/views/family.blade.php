<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نموذج تسجيل أفراد الأسرة - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .input-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .input-group label {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .input-group input {
            width: 120px;
            padding: 12px;
            font-size: 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            text-align: center;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 5px rgba(255, 111, 0, 0.15);
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn i {
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.5);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: white;
            color: #666;
            border: 2px solid #ddd;
        }

        .btn-secondary:hover {
            background: #f5f5f5;
            border-color: #999;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
        }

        table th {
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
        }

        table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        table tbody tr:hover {
            background-color: rgba(255, 111, 0, 0.05);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .edit-btn, .delete-btn {
            font-size: 18px;
            padding: 8px 12px;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 8px;
        }

        .edit-btn:hover {
            background: rgba(255, 111, 0, 0.1);
        }

        .delete-btn:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .edit-btn i {
            color: #FF6F00;
        }

        .delete-btn i {
            color: #000;
        }

        /* نافذة منبثقة */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* تنسيق النوافذ المنبثقة */
        #form-popup, #edit-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            padding: 35px;
            width: 90%;
            max-width: 900px;
            max-height: 85vh;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            overflow-y: auto;
        }

        #form-popup h1,
        #edit-popup h1 {
            color: #FF6F00;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6F00;
            font-weight: 700;
        }

        #form-popup h1 i,
        #edit-popup h1 i {
            margin-left: 10px;
        }

        /* أقسام الفورم */
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }

        .section-title {
            color: #FF6F00;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FFE5CC;
        }

        .section-title i {
            margin-left: 8px;
        }

        /* صفوف الحقول */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        /* مجموعة الحقل */
        .form-group {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }

        .required {
            color: #ff0000;
            font-weight: bold;
            margin-right: 3px;
        }

        /* الحقول */
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #fff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 4px rgba(255, 111, 0, 0.1);
            background-color: #fffbf5;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
            line-height: 1.6;
        }

        /* رسائل الخطأ والنجاح */
        .error-message {
            color: #ff0000;
            font-size: 13px;
            margin-top: 5px;
            display: none;
            font-weight: 500;
        }

        .error-message i {
            margin-left: 5px;
        }

        .success-message {
            color: #35b735;
            font-size: 13px;
            margin-top: 5px;
            display: none;
            font-weight: 500;
        }

        .success-message i {
            margin-left: 5px;
        }

        /* الأزرار */
        .popup-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
            padding-top: 25px;
            border-top: 2px solid #e0e0e0;
        }

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
                padding: 25px 20px;
            }

            .welcome-box {
                padding: 25px;
            }

            .welcome-box h2 {
                font-size: 20px;
            }

            .welcome-box p {
                font-size: 14px;
            }

            table {
                font-size: 13px;
            }

            table th, table td {
                padding: 8px;
            }

            #form-popup, #edit-popup {
                padding: 25px 20px;
                width: 95%;
                max-height: 90vh;
            }

            #form-popup h1,
            #edit-popup h1 {
                font-size: 20px;
            }

            .form-section {
                padding: 15px;
            }

            .section-title {
                font-size: 16px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .popup-buttons {
                flex-direction: column;
                gap: 12px;
            }

            .btn {
                width: 100%;
                justify-content: center;
                padding: 14px 20px;
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

    <div id="overlay"></div>

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
                    <h2>نموذج تسجيل أفراد الأسرة</h2>
                    <p>أهلاً بك في نظام تسجيل أفراد الأسرة</p>
                </div>

                @if ($errors->any())
                    <div style="background-color: #ffebee; color: #c62828; padding: 15px; border-radius: 12px; margin-bottom: 20px; border-right: 4px solid #c62828;">
                        <ul style="margin: 0; padding: 0 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- إدخال عدد الأفراد -->
                <div class="input-group">
                    <label for="num_of_people">عدد الأفراد:</label>
                    <input type="number" id="num_of_people" placeholder="0" required>
                    <button type="button" id="open-form-btn" class="btn btn-primary" disabled>
                        <i class="fas fa-plus"></i> إضافة فرد جديد
                    </button>
                </div>

                <!-- جدول الأفراد -->
                <table id="family-table">
                    <thead>
                        <tr>
                            <th>رقم الهوية</th>
                            <th>الاسم الأول</th>
                            <th>اسم الأب</th>
                            <th>اسم الجد</th>
                            <th>اسم العائلة</th>
                            <th>تاريخ الميلاد</th>
                            <th>صلة القرابة</th>
                            <th>رقم الجوال</th>
                            <th>حالة صحية</th>
                            <th>وصف الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="default-row" style="display: none;">
                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    </tbody>
                </table>

                <!-- زر إرسال البيانات -->
                <div style="text-align: center; margin-top: 30px;">
                    <button id="send-btn" type="button" onclick="submitForm()" class="btn btn-primary" style="padding: 15px 40px; font-size: 18px;">
                        <i class="fas fa-paper-plane"></i> إرسال البيانات
                    </button>
                </div>
            </div>

            <!-- القسم الجانبي -->
            <div class="sidebar">
                <h2>إرشادات التسجيل</h2>

                <div class="info-item">
                    <h3><i class="fas fa-users"></i> عدد الأفراد</h3>
                    <p>قم بإدخال عدد أفراد أسرتك ثم قم بالضغط على زر إضافة فرد جديد لتقوم بإدخال بيانات الأفراد كاملة</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-heart"></i> الاستفادة الكاملة</h3>
                    <p>احرص عزيزي المواطن على تعبئة كافة بيانات أفراد أسرتك لضمان الاستفادة الكاملة من المشاريع الإغاثية القائمة 🤗</p>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة إضافة فرد -->
    <div id="form-popup">
        <h1><i class="fas fa-user-plus"></i> إضافة بيانات فرد</h1>

        <!-- الأسماء -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-user"></i> البيانات الشخصية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">الاسم الأول <span class="required">*</span></label>
                    <input type="text" id="firstname" name="firstname" placeholder="أدخل الاسم الأول" required>
                </div>
                <div class="form-group">
                    <label for="fathername">اسم الأب <span class="required">*</span></label>
                    <input type="text" id="fathername" name="fathername" placeholder="أدخل اسم الأب" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="grandfathername">اسم الجد <span class="required">*</span></label>
                    <input type="text" id="grandfathername" name="grandfathername" placeholder="أدخل اسم الجد" required>
                </div>
                <div class="form-group">
                    <label for="familyname">اسم العائلة <span class="required">*</span></label>
                    <input type="text" id="familyname" name="familyname" placeholder="أدخل اسم العائلة" required>
                </div>
            </div>
        </div>

        <!-- صلة القرابة والجوال -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-users"></i> العلاقة الأسرية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="relationship">صلة القرابة <span class="required">*</span></label>
                    <select id="relationship" name="relationship" required>
                        <option value="">-- اختر صلة القرابة --</option>
                        @foreach($relationships as $key => $relationship)
                            <option value="{{$key}}">{{$relationship}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="phone-group" style="display: none;">
                    <label for="phone">رقم الجوال <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="05xxxxxxxx" maxlength="10">
                    <span id="phoneerror" class="error-message"><i class="fas fa-exclamation-circle"></i> رقم غير صحيح</span>
                </div>
            </div>
        </div>

        <!-- الهوية والميلاد -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-id-card"></i> بيانات الهوية والميلاد</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="idnum">رقم الهوية <span class="required">*</span></label>
                    <input type="number" id="idnum" name="idnum" placeholder="9 أرقام" required oninput="validateIdOnInput('idnum')" maxlength="9">
                    <span id="idnum_error" class="error-message"><i class="fas fa-exclamation-circle"></i> رقم الهوية غير صالح</span>
                    <span id="idnum_success" class="success-message"><i class="fas fa-check-circle"></i> رقم صحيح</span>
                </div>
                <div class="form-group">
                    <label for="dob">تاريخ الميلاد <span class="required">*</span></label>
                    <input type="date" id="dob" name="dob" required>
                    <span id="dob_error" class="error-message"></span>
                </div>
            </div>
        </div>

        <!-- الحالة الصحية -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-heartbeat"></i> الحالة الصحية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hascondition">هل يعاني من حالة صحية؟ <span class="required">*</span></label>
                    <select id="hascondition" name="hascondition">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="condition-description-group" style="display: none;">
                <label for="conditiondescription">وصف الحالة الصحية <span class="required">*</span></label>
                <textarea id="conditiondescription" name="conditiondescription" rows="4" placeholder="اكتب تفاصيل الحالة الصحية أو الإعاقة أو إصابة الحرب..."></textarea>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="popup-buttons">
            <button type="button" id="add-person-btn" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> إضافة الفرد
            </button>
            <button type="button" id="close-popup-btn" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </button>
        </div>
    </div>

    <!-- نافذة تعديل فرد -->
    <div id="edit-popup">
        <h1><i class="fas fa-user-edit"></i> تعديل بيانات فرد</h1>

        <!-- الأسماء -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-user"></i> البيانات الشخصية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editfirstname">الاسم الأول <span class="required">*</span></label>
                    <input type="text" id="editfirstname" name="editfirstname" placeholder="أدخل الاسم الأول" required>
                </div>
                <div class="form-group">
                    <label for="editfathername">اسم الأب <span class="required">*</span></label>
                    <input type="text" id="editfathername" name="editfathername" placeholder="أدخل اسم الأب" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editgrandfathername">اسم الجد <span class="required">*</span></label>
                    <input type="text" id="editgrandfathername" name="editgrandfathername" placeholder="أدخل اسم الجد" required>
                </div>
                <div class="form-group">
                    <label for="editfamilyname">اسم العائلة <span class="required">*</span></label>
                    <input type="text" id="editfamilyname" name="editfamilyname" placeholder="أدخل اسم العائلة" required>
                </div>
            </div>
        </div>

                <!-- صلة القرابة والجوال -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-users"></i> العلاقة الأسرية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editrelationship">صلة القرابة <span class="required">*</span></label>
                    <select id="editrelationship" name="editrelationship" required>
                        <option value="">-- اختر صلة القرابة --</option>
                        @foreach($relationships as $key => $relationship)
                            <option value="{{$key}}">{{$relationship}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="editphone-group" style="display: none;">
                    <label for="editphone">رقم الجوال <span class="required">*</span></label>
                    <input type="tel" id="editphone" name="editphone" placeholder="05xxxxxxxx" maxlength="10">
                    <span id="editphoneerror" class="error-message"><i class="fas fa-exclamation-circle"></i> رقم غير صحيح</span>
                </div>
            </div>
        </div>

        <!-- الهوية والميلاد -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-id-card"></i> بيانات الهوية والميلاد</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editidnum">رقم الهوية <span class="required">*</span></label>
                    <input type="number" id="editidnum" name="editidnum" placeholder="9 أرقام" required oninput="validateIdOnInput('editidnum')" maxlength="9">
                    <span id="editidnum_error" class="error-message"><i class="fas fa-exclamation-circle"></i> رقم الهوية غير صالح</span>
                    <span id="editidnum_success" class="success-message"><i class="fas fa-check-circle"></i> رقم صحيح</span>
                </div>
                <div class="form-group">
                    <label for="editdob">تاريخ الميلاد <span class="required">*</span></label>
                    <input type="date" id="editdob" name="editdob" required>
                    <span id="editdob_error" class="error-message"></span>
                </div>
            </div>
        </div>

        <!-- الحالة الصحية -->
        <div class="form-section">
            <div class="section-title"><i class="fas fa-heartbeat"></i> الحالة الصحية</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edithascondition">هل يعاني من حالة صحية؟ <span class="required">*</span></label>
                    <select id="edithascondition" name="edithascondition">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="editcondition-description-group" style="display: none;">
                <label for="editconditiondescription">وصف الحالة الصحية <span class="required">*</span></label>
                <textarea id="editconditiondescription" name="editconditiondescription" rows="4" placeholder="اكتب تفاصيل الحالة الصحية أو الإعاقة أو إصابة الحرب..."></textarea>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="popup-buttons">
            <button id="save-edits" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <button type="button" id="close-edit-popup-btn" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </button>
        </div>
    </div>

    <script>
        function calculateAge(dobStr) {
            const dob = new Date(dobStr);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            return age;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const relationshipTranslations = {
            'father':'أب', 'mother':'أم', 'brother':'أخ', 'sister':'أخت',
            'husband':'زوج', 'wife':'زوجة', 'son':'ابن', 'daughter':'ابنة', 'others':'اخرون'
        };

        let maxPeople = 0, addedPeople = 0, peopleList = [];

        const relationship = document.getElementById('relationship');
        const dob = document.getElementById('dob');
        const dobError = document.getElementById('dob_error');
        const editRelationship = document.getElementById('editrelationship');
        const editDob = document.getElementById('editdob');
        const editDobError = document.getElementById('editdob_error');

        dob.disabled = true;
        editDob.disabled = true;

        function enableDobField(dobField, errorField) {
            dobField.disabled = false;
            errorField.style.display = 'none';
        }

        function handleRelationshipChange(selectElement, phoneGroupId, phoneInputId, dobField, errorField) {
            const phoneGroup = document.getElementById(phoneGroupId);
            const phoneInput = document.getElementById(phoneInputId);
            enableDobField(dobField, errorField);
            if (selectElement.value === 'wife') {
                phoneGroup.style.display = 'block';
                phoneInput.required = true;
            } else {
                phoneGroup.style.display = 'none';
                phoneInput.required = false;
                phoneInput.value = '';
            }
        }

        relationship.addEventListener('change', function () {
            handleRelationshipChange(this, 'phone-group', 'phone', dob, dobError);
        });

        editRelationship.addEventListener('change', function () {
            handleRelationshipChange(this, 'editphone-group', 'editphone', editDob, editDobError);
        });

        function validatePhone(phoneField) {
            const phoneInput = document.getElementById(phoneField);
            const phoneError = document.getElementById(phoneField + 'error');
            const phonePattern = /^05[0-9]{8}$/;
            if (phoneInput.value && !phonePattern.test(phoneInput.value)) {
                phoneError.style.display = 'inline';
                phoneInput.style.borderColor = '#ff0000';
                return false;
            } else {
                phoneError.style.display = 'none';
                phoneInput.style.borderColor = '';
                return true;
            }
        }

        document.getElementById('phone').addEventListener('input', () => validatePhone('phone'));
        document.getElementById('editphone').addEventListener('input', () => validatePhone('editphone'));

        function isSpouse(rel) {
            return ['زوج', 'زوجة', 'wife', 'husband'].includes(rel);
        }

        function validateSpouseAge(rel, dobValue, errorElement) {
            if (isSpouse(rel)) {
                if (!dobValue) {
                    errorElement.textContent = 'يرجى إدخال تاريخ الميلاد للزوج/الزوجة.';
                    errorElement.style.display = 'block';
                    return false;
                }
                const age = calculateAge(dobValue);
                if (age < 16) {
                    errorElement.textContent = 'عمر الزوج/الزوجة يجب أن يكون 16 سنة فأكثر.';
                    errorElement.style.display = 'block';
                    return false;
                }
            }
            return true;
        }

        function validateIdOnInput(fieldId) {
            const idField = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');
            const successSpan = document.getElementById(fieldId + '_success');
            if (idField.value.length === 9) {
                errorSpan.style.display = 'none';
                successSpan.style.display = 'block';
                idField.style.borderColor = '#35b735';
            } else {
                successSpan.style.display = 'none';
                if (idField.value.length > 0) {
                    errorSpan.style.display = 'block';
                    idField.style.borderColor = '#ff0000';
                } else {
                    errorSpan.style.display = 'none';
                    idField.style.borderColor = '';
                }
            }
        }

        function validateIdNumber(fieldId) {
            return document.getElementById(fieldId).value.length === 9;
        }

        function validateRequiredFields() {
            if (!$('#firstname').val() || !$('#fathername').val() || !$('#grandfathername').val() ||
                !$('#familyname').val() || !$('#relationship').val() || !$('#idnum').val() || !$('#dob').val()) {
                showAlert('يرجى ملء جميع الحقول المطلوبة!', 'error');
                return false;
            }
            return true;
        }

        function validateEditRequiredFields() {
            if (!$('#editfirstname').val() || !$('#editfathername').val() || !$('#editgrandfathername').val() ||
                !$('#editfamilyname').val() || !$('#editrelationship').val() || !$('#editidnum').val() || !$('#editdob').val()) {
                showAlert('يرجى ملء جميع الحقول المطلوبة!', 'error');
                return false;
            }
            return true;
        }

        function showAlert(message, type) {
            let icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'warning';
            Swal.fire({
                icon: icon,
                title: message,
                confirmButtonColor: '#FF6F00',
                confirmButtonText: 'حسناً'
            });
        }

        function renderTable() {
            const tableBody = $('#family-table tbody');
            tableBody.empty();

            const firstPersonData = {!! json_encode(session('first_person_data')) !!};
            if (firstPersonData) {
                const formattedDob = firstPersonData.dob ? new Date(firstPersonData.dob).toLocaleDateString('ar-EN') : 'غير محدد';
                const translatedRelationship = relationshipTranslations[firstPersonData.relationship] || firstPersonData.relationship;
                const phoneDisplay = firstPersonData.phone ? firstPersonData.phone : '-';
                tableBody.append(`
                    <tr id="first-person-row">
                        <td>${firstPersonData.id_num}</td>
                        <td>${firstPersonData.first_name}</td>
                        <td>${firstPersonData.father_name}</td>
                        <td>${firstPersonData.grandfather_name}</td>
                        <td>${firstPersonData.family_name}</td>
                        <td>${formattedDob}</td>
                        <td>${translatedRelationship}</td>
                        <td>${phoneDisplay}</td>
                        <td>${firstPersonData.has_condition == 1 ? 'نعم' : 'لا'}</td>
                        <td>${firstPersonData.condition_description || '-'}</td>
                        <td></td>
                    </tr>`);
            }

            if (peopleList && peopleList.length > 0) {
                peopleList.forEach((person, index) => {
                    const formattedDob = person.dob ? new Date(person.dob).toLocaleDateString('ar-EN') : '';
                    const translatedRelationship = relationshipTranslations[person.relationship] || person.relationship;
                    const phoneDisplay = person.relationship === 'wife' && person.phone ? person.phone : '-';
                    tableBody.append(`
                        <tr>
                            <td>${person.id_num}</td>
                            <td>${person.first_name}</td>
                            <td>${person.father_name}</td>
                            <td>${person.grandfather_name}</td>
                            <td>${person.family_name}</td>
                            <td>${formattedDob}</td>
                            <td>${translatedRelationship}</td>
                            <td>${phoneDisplay}</td>
                            <td>${person.has_condition === 1 ? 'نعم' : 'لا'}</td>
                            <td>${person.condition_description ?? '-'}</td>
                            <td class="action-buttons">
                                <button class="edit-btn" data-index="${index}"><i class="fas fa-edit"></i></button>
                                <button class="delete-btn" data-index="${index}"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                });
            } else if (!firstPersonData) {
                tableBody.html('<tr><td colspan="11">لا يوجد أفراد مضافين.</td></tr>');
            }
        }

        function editPerson(index) {
            const person = peopleList[index];
            $('#editfirstname').val(person.first_name);
            $('#editfathername').val(person.father_name);
            $('#editgrandfathername').val(person.grandfather_name);
            $('#editfamilyname').val(person.family_name);
            $('#editidnum').val(person.id_num);
            $('#editdob').val(person.dob);
            $('#editrelationship').val(person.relationship);
            $('#edithascondition').val(person.has_condition);
            $('#editconditiondescription').val(person.condition_description || '');

            if (person.relationship === 'wife') {
                $('#editphone-group').show();
                $('#editphone').val(person.phone || '');
            }

            if (person.has_condition == 1) {
                $('#editcondition-description-group').show();
            }

            $('#save-edits').data('index', index);
            $('#edit-popup').fadeIn();
            $('#overlay').fadeIn();
        }

        function deletePerson(index) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف بيانات هذا الشخص!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF6F00',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    peopleList.splice(index, 1);
                    addedPeople--;
                    renderTable();
                    $('#open-form-btn').prop('disabled', false);
                    showAlert('تم الحذف بنجاح!', 'success');
                }
            });
        }

        $(document).ready(function() {
            renderTable();
            $('#open-form-btn').prop('disabled', !$('#num_of_people').val());

            $('#num_of_people').on('input', function() {
                maxPeople = parseInt($(this).val()) || 0;
                $('#open-form-btn').prop('disabled', maxPeople === 0);
            });

            $('#open-form-btn').click(function () {
                if (maxPeople === 0) {
                    showAlert('يرجى تعبئة عدد الأفراد أولاً!', 'warning');
                    return;
                }
                if (addedPeople >= maxPeople) {
                    showAlert('لقد تجاوزت عدد أفراد أسرتك!', 'error');
                    return;
                }
                $('#form-popup').fadeIn();
                $('#overlay').fadeIn();
            });

            $('#close-popup-btn, #overlay').click(function () {
                $('#form-popup').fadeOut();
                $('#overlay').fadeOut();
            });

            $('#close-edit-popup-btn').click(function () {
                $('#edit-popup').fadeOut();
                $('#overlay').fadeOut();
            });

            $('#hascondition').change(function () {
                $('#condition-description-group').toggle($(this).val() === '1');
            });

            $('#edithascondition').change(function () {
                $('#editcondition-description-group').toggle($(this).val() === '1');
            });

            $(document).on('click', '.edit-btn', function() {
                editPerson($(this).data('index'));
            });

            $(document).on('click', '.delete-btn', function() {
                deletePerson($(this).data('index'));
            });

            $('#add-person-btn').click(function() {
                dobError.style.display = 'none';
                dobError.textContent = '';

                if (!validateRequiredFields()) return;

                const idnum = $('#idnum').val();
                const relationshipVal = $('#relationship').val();
                const dobVal = $('#dob').val();
                const phone = $('#phone').val();

                if (relationshipVal === 'wife') {
                    if (!phone) {
                        showAlert('يرجى إدخال رقم جوال الزوجة!', 'error');
                        return;
                    }
                    if (!validatePhone('phone')) {
                        showAlert('رقم الجوال غير صحيح!', 'error');
                        return;
                    }
                }

                if (!validateSpouseAge(relationshipVal, dobVal, dobError)) return;
                if (!validateIdNumber('idnum')) {
                    showAlert('رقم الهوية يجب أن يكون 9 أرقام!', 'error');
                    return;
                }

                if (peopleList.some(person => person.id_num === idnum)) {
                    showAlert('رقم هوية مكرر!', 'error');
                    return;
                }

                peopleList.push({
                    id_num: idnum,
                    first_name: $('#firstname').val(),
                    father_name: $('#fathername').val(),
                    grandfather_name: $('#grandfathername').val(),
                    family_name: $('#familyname').val(),
                    dob: dobVal,
                    relationship: relationshipVal,
                    has_condition: $('#hascondition').val() === '1' ? 1 : 0,
                    condition_description: $('#hascondition').val() === '1' ? $('#conditiondescription').val() : null,
                    phone: relationshipVal === 'wife' ? phone : null
                });

                addedPeople++;
                renderTable();

                if (addedPeople >= maxPeople) {
                    $('#open-form-btn').prop('disabled', true);
                }

                $('#form-popup input, #form-popup select, #form-popup textarea').val('');
                $('#phone-group, #condition-description-group').hide();
                $('#form-popup, #overlay').fadeOut();
                showAlert('تمت الإضافة بنجاح!', 'success');
            });

            $('#save-edits').off('click').on('click', function(e) {
                e.preventDefault();
                editDobError.style.display = 'none';
                editDobError.textContent = '';

                if (!validateEditRequiredFields()) return;

                const rel = $('#editrelationship').val();
                const dobVal = $('#editdob').val();
                const phone = $('#editphone').val();
                const currentIndex = $(this).data('index');
                const idnum = $('#editidnum').val();

                if (rel === 'wife' && !validatePhone('editphone')) {
                    showAlert('رقم الجوال غير صحيح!', 'error');
                    return;
                }

                if (!validateSpouseAge(rel, dobVal, editDobError)) return;
                if (!validateIdNumber('editidnum')) {
                    showAlert('رقم الهوية يجب أن يكون 9 أرقام!', 'error');
                    return;
                }

                if (peopleList.some((person, idx) => person.id_num === idnum && idx !== currentIndex)) {
                    showAlert('رقم هوية مكرر!', 'error');
                    return;
                }

                if (currentIndex !== undefined) {
                    peopleList[currentIndex] = {
                        ...peopleList[currentIndex],
                        first_name: $('#editfirstname').val(),
                        father_name: $('#editfathername').val(),
                        grandfather_name: $('#editgrandfathername').val(),
                        family_name: $('#editfamilyname').val(),
                        id_num: idnum,
                        dob: dobVal,
                        relationship: rel,
                        has_condition: $('#edithascondition').val() == '1' ? 1 : 0,
                        condition_description: $('#editconditiondescription').val(),
                        phone: rel === 'wife' ? phone : null
                    };

                    $('#edit-popup, #overlay').fadeOut();
                    showAlert('تم التعديل بنجاح!', 'success');
                    renderTable();
                }
            });
        });

        function submitForm() {
            const submitBtn = document.getElementById('send-btn');
            submitBtn.disabled = true;

            Swal.fire({
                title: 'جاري تسجيل بياناتك...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let person = @json($person);

            if (['single', 'divorced', 'widowed'].includes(person.social_status)) {
                const forbiddenRelationships = ['husband', 'wife'];
                if (peopleList.some(p => forbiddenRelationships.includes(p.relationship))) {
                    Swal.close();
                    showAlert('لا يمكن تسجيل أفراد الأسرة ذات علاقات زوج/زوجة إذا كانت الحالة الاجتماعية أعزب/ة أو مطلق/ة أو أرمل/ة.', 'error');
                    submitBtn.disabled = false;
                    return;
                }
            }

            if (peopleList.length === 0 && !(['single', 'divorced', 'widowed'].includes(person.social_status))) {
                Swal.close();
                showAlert('لا توجد بيانات لإرسالها!', 'warning');
                submitBtn.disabled = false;
                return;
            }

            const wivesCount = peopleList.filter(p => p.relationship === 'wife').length;
            if (person.social_status === 'married' && wivesCount !== 1) {
                Swal.close();
                showAlert('الشخص المتزوج يجب أن يكون لديه زوجة واحدة فقط في قائمة الأفراد.', 'error');
                submitBtn.disabled = false;
                return;
            } else if (person.social_status === 'polygamous' && (wivesCount < 2 || wivesCount > 4)) {
                Swal.close();
                showAlert('الشخص المتعدد يجب أن يكون لديه من 2 إلى 4 زوجات في قائمة الأفراد.', 'error');
                submitBtn.disabled = false;
                return;
            }

            $.ajax({
                url: '/store-people-session',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                contentType: 'application/json',
                data: JSON.stringify({ peopleList: peopleList }),
                success: function(sessionResponse) {
                    if (!sessionResponse.success) {
                        Swal.close();
                        showAlert(sessionResponse.message || 'حدث خطأ أثناء تجهيز بيانات الأسرة.', 'error');
                        submitBtn.disabled = false;
                        return;
                    }

                    $.ajax({
                        url: sessionResponse.postRedirect || '/store-family',
                        type: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(storeResponse) {
                            Swal.close();
                            if (!storeResponse.success) {
                                showAlert(storeResponse.message || 'حدث خطأ أثناء التسجيل!', 'error');
                                submitBtn.disabled = false;
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم التسجيل بنجاح!',
                                    text: storeResponse.message,
                                    confirmButtonColor: '#FF6F00'
                                }).then(() => {
                                    window.location.href = storeResponse.redirect || '/';
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            showAlert('حدث خطأ في الخادم!', 'error');
                            submitBtn.disabled = false;
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    showAlert('حدث خطأ في الخادم!', 'error');
                    submitBtn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
