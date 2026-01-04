<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بروفايل المو اطن - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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

        .wave:nth-child(3) {
            bottom: 30px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,40 Q300,10 600,40 T1200,40 L1200,120 L0,120 Z" fill="rgba(230,81,0,0.15)"/></svg>');
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
        .floating-circle:nth-child(3) { width: 280px; height: 280px; background: radial-gradient(circle, rgba(230, 81, 0, 0.22) 0%, transparent 70%); top: 40%; left: -50px; animation-duration: 18s; animation-delay: 1s; }

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
            justify-content: space-between;
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
            flex: 1;
        }

        .top-bar span {
            color: #FF6F00;
        }

        /* الصف الرئيسي */
        .main-row {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
        }

        /* القسم الجانبي */
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .sidebar h2 {
            color: #333;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6F00;
        }

        .nav-item {
            margin-bottom: 10px;
            padding: 15px;
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.08), rgba(230, 81, 0, 0.05));
            border-radius: 12px;
            border-right: 4px solid #FF6F00;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(255, 111, 0, 0.1);
            cursor: pointer;
        }

        .nav-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 20px rgba(255, 111, 0, 0.2);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(255, 111, 0, 0.15), rgba(230, 81, 0, 0.1));
            border-right: 6px solid #FF6F00;
        }

        .nav-item h3 {
            color: #E65100;
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item i {
            color: #FF6F00;
            font-size: 16px;
        }

        .sub-nav {
            margin-top: 10px;
            padding-right: 20px;
            display: none;
        }

        .sub-nav.active {
            display: block;
        }

        .sub-nav-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            color: #666;
        }

        .sub-nav-item:hover {
            background: rgba(255, 111, 0, 0.1);
            color: #FF6F00;
        }

        .sub-nav-item.active {
            background: rgba(255, 111, 0, 0.15);
            color: #FF6F00;
            font-weight: 600;
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
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.4);
        }

        .welcome-box h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .welcome-box p {
            font-size: 15px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        /* أقسام المحت وى */
        .content-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 111, 0, 0.2);
        }

        .section-header h3 {
            color: #FF6F00;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-btn, .add-btn {
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .edit-btn:hover, .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
        }

        /* معلومات البروفايل */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item-display {
            background: rgba(255, 111, 0, 0.05);
            padding: 15px;
            border-radius: 10px;
            border-right: 3px solid #FF6F00;
        }

        .info-item-display label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .info-item-display label i {
            color: #FF6F00;
            font-size: 14px;
        }

        .info-item-display .value {
            color: #333;
            font-size: 15px;
            font-weight: 500;
        }

        /* الجداول */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .data-table thead {
            background: linear-gradient(135deg, #FF6F00, #E65100);
        }

        .data-table th {
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
        }

        .data-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .data-table tbody tr:hover {
            background: rgba(255, 111, 0, 0.05);
        }

        .action-icon {
            color: #FF6F00;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-icon:hover {
            transform: scale(1.2);
            color: #E65100;
        }

        .delete-icon {
            color: #d32f2f;
        }

        .delete-icon:hover {
            color: #b71c1c;
        }

        /* البوب أبز */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup.hidden {
            display: none;
        }

        .popup-content {
            position: relative;
            width: 90%;
            max-width: 1000px;
            max-height: 85vh;
            overflow-y: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .popup-content h1 {
            color: #FF6F00;
            font-size: 22px;
            margin-bottom: 25px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 111, 0, 0.2);
        }

        .close {
            position: absolute;
            top: 15px;
            left: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #666;
            transition: all 0.2s;
        }

        .close:hover {
            color: #FF6F00;
            transform: rotate(90deg);
        }

        /* الفورمز */
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-group label i {
            color: #FF6F00;
            font-size: 16px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #FF6F00;
            font-size: 16px;
            pointer-events: none;
        }

        input[type="number"],
        input[type="text"],
        input[type="date"],
        input[type="tel"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 14px 45px 14px 15px;
            font-size: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            background: white;
        }

        input:disabled, select:disabled, textarea:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            padding-top: 12px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #FF6F00;
            box-shadow: 0 0 0 4px rgba(255, 111, 0, 0.1);
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .save-btn {
            width: 100%;
            padding: 15px;
            font-size: 17px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.3);
            margin-top: 20px;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(255, 111, 0, 0.4);
        }

        .error-message {
            color: #d32f2f;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            color: #2e7d32;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .password-requirements {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            line-height: 1.6;
        }

        .password-requirements span {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 3px 0;
        }

        .password-requirements span i {
            display: none;
        }

        .password-requirements span.valid {
            color: #2e7d32;
        }

        .password-requirements span.valid i {
            display: inline;
        }

        .password-requirements span.invalid {
            color: #d32f2f;
        }

        .password-requirements span.invalid i {
            display: inline;
        }

        .toggle-password {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
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
                padding: 15px;
                flex-direction: column;
                text-align: center;
            }

            .top-bar h1 {
                font-size: 18px;
            }

            .logo-small {
                width: 50px;
                height: 50px;
            }

            .sidebar {
                padding: 20px;
            }

            .main-content {
                padding: 25px 20px;
            }

            .welcome-box {
                padding: 20px;
            }

            .welcome-box h2 {
                font-size: 18px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .row {
                grid-template-columns: 1fr;
            }

            .popup-content {
                padding: 20px;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
