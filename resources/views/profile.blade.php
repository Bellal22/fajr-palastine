<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بروفايل المواطن - جمعية الفجر الشبابي الفلسطيني</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>

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
        .particle:nth-child(6) { background: #E65100; left: 58%; animation-delay: 2.5s; animation-duration: 7s; }
        .particle:nth-child(7) { background: #FF6F00; left: 68%; animation-delay: 0.8s; animation-duration: 6.8s; }

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
            margin: 0;
        }

        .top-bar span {
            color: #FF6F00;
        }

        /* الصف الرئيسي */
        .main-row {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 25px;
            direction: rtl;
        }

        /* القسم الجانبي - Sidebar */
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(255, 111, 0, 0.12);
            height: fit-content;
            position: sticky;
            top: 20px;
            order: 2;
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
            padding: 10px 15px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            border-radius: 8px;
            margin-top: 5px;
        }

        .nav-description {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
            display: block;
            font-weight: normal;
            opacity: 0.8;
            line-height: 1.4;
        }

        .nav-item.active .nav-description,
        .sub-nav-item.active .nav-description {
            color: #E65100;
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
            order: 1;
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
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            margin: 0;
        }

        /* أقسام المحتوى */
        .content-section {
            display: block; /* جعل الأقسام ظاهرة دائماً */
            margin-bottom: 50px; /* مسافة بين الأقسام */
            scroll-margin-top: 100px; /* مسافة من الأعلى عند التمرير */
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
            margin: 0;
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
            text-decoration: none;
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
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease-out;
            opacity: 1;
            visibility: visible;
        }

        .popup.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .popup-content {
            position: relative;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            transform: scale(1);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .popup.hidden .popup-content {
            transform: scale(0.9);
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

        /* أقسام الفورم داخل البوب أب */
        .popup-form-section {
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            position: relative;
        }

        .popup-form-section h3 {
            color: #FF6F00;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid rgba(255, 111, 0, 0.1);
            padding-bottom: 10px;
        }

        .popup-form-section h3 i {
            background: rgba(255, 111, 0, 0.1);
            padding: 8px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .name-row {
            grid-template-columns: repeat(4, 1fr) !important;
        }

        .sub-nav-item i {
            margin-left: 8px;
            font-size: 14px;
            width: 20px;
            text-align: center;
            display: inline-block;
        }

        .sub-nav-item a {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sub-nav-item a i {
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .name-row {
                grid-template-columns: 1fr !important;
            }
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
        }

        .success-message {
            color: #2e7d32;
            font-size: 13px;
            margin-top: 5px;
        }

        .password-requirements {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .password-requirements span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-requirements span i {
            display: none;
        }

        .password-requirements span.valid {
            color: #2e7d32;
        }

        .password-requirements span.valid i {
            display: inline;
            color: #2e7d32;
        }

        .password-requirements span.invalid {
            color: #d32f2f;
        }

        .password-requirements span.invalid i {
            display: inline;
            color: #d32f2f;
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

        .area-responsible-container {
            display: flex;
            flex-direction: column;
        }

        #areaResponsibleField[style*="display:none"] .error-message,
        #edit_areaResponsibleField[style*="display:none"] .error-message {
            display: block !important;
            visibility: hidden;
        }

        #areaResponsibleField[style*="display:none"] label,
        #areaResponsibleField[style*="display:none"] select,
        #edit_areaResponsibleField[style*="display:none"] label,
        #edit_areaResponsibleField[style*="display:none"] select {
            display: none !important;
        }

        .overlay-class {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .hidden {
            display: none;
        }

        button {
            font-family: 'Cairo', sans-serif;
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
                max-height: 90vh;
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
    </div>

    <div class="page-container">
        <!-- الهيدر العلوي -->
        <div class="top-bar">
            <img src="{{asset('background/image.jpg')}}" alt="الشعار" class="logo-small">
            <h1>جمعية الفجر الشبابي الفلسطيني</h1>
        </div>

        <!-- الصف الرئيسي -->
        <div class="main-row">
            <!-- السايد بار -->
           <div class="sidebar">
                <h2>القائمة</h2>

                <!-- معلومات رب الأسرة -->
                <div class="nav-item active" onclick="toggleNav(this, 'family-head-nav')">
                    <h3><i class="fas fa-user-tie"></i> معلومات رب الأسرة</h3>
                    <span class="nav-description">البيانات الأساسية وتفاصيل السكن والعمل</span>
                    <div class="sub-nav active" id="family-head-nav">
                        <div class="sub-nav-item active" onclick="showSection('personal-info', event)">
                            <i class="fas fa-id-card text-primary"></i> البيانات الشخصية
                            <span class="nav-description">الاسم ورقم الهوية ومعلومات التواصل</span>
                        </div>
                        <div class="sub-nav-item" onclick="showSection('social-work-info', event)">
                            <i class="fas fa-briefcase text-success"></i> البيانات الاجتماعية والعمل
                            <span class="nav-description">الحالة الاجتماعية وتفاصيل الوظيفة</span>
                        </div>
                        <div class="sub-nav-item" onclick="showSection('health-info', event)">
                            <i class="fas fa-heartbeat text-danger"></i> الحالة الصحية
                            <span class="nav-description">الأمراض المزمنة والحالة الطبية</span>
                        </div>
                        <div class="sub-nav-item" onclick="showSection('housing-info', event)">
                            <i class="fas fa-home text-info"></i> بيانات السكن
                            <span class="nav-description">العنوان الحالي وتفاصيل المنطقة</span>
                        </div>
                    </div>
                </div>

                <!-- معلومات أفراد الأسرة -->
                <div class="nav-item" onclick="toggleNav(this); showSection('family-members', event)">
                    <h3><i class="fas fa-users"></i> معلومات أفراد الأسرة</h3>
                    <span class="nav-description">إدارة بيانات الزوجة والأبناء</span>
                </div>

                <!-- الشكاوي -->
                <div class="nav-item" onclick="toggleNav(this); showSection('complaints', event)">
                    <h3><i class="fas fa-comments"></i> الشكاوي</h3>
                    <span class="nav-description">إرسال ومتابعة الشكاوي والاقتراحات</span>
                </div>

                <!-- الإعدادات -->
                <div class="nav-item" onclick="toggleNav(this, 'settings-nav')">
                    <h3><i class="fas fa-cog"></i> الإعدادات</h3>
                    <span class="nav-description">تغيير كلمة المرور وإدارة الحساب</span>
                    <div class="sub-nav" id="settings-nav">
                        <div class="sub-nav-item" onclick="openPasswordPopup(event)">
                            <i class="fas fa-key text-warning"></i> تغيير كلمة المرور
                            <span class="nav-description">تحديث بيانات الدخول الخاصة بك</span>
                        </div>
                        <div class="sub-nav-item">
                            <a href="{{ route('logout') }}" style="color: inherit; text-decoration: none; display: block;">
                                <i class="fas fa-sign-out-alt text-danger"></i> تسجيل الخروج
                                <span class="nav-description">إنهاء الجلسة الحالية بشكل آمن</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المحتوى الرئيسي -->
            <div class="main-content">
                <!-- صندوق الترحيب -->
                <div class="welcome-box">
                    <h2>مرحباً، {{ $person->first_name }} {{ $person->family_name }}</h2>
                </div>

                @if($person->is_frozen)
                    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <div class="py-1"><i class="fas fa-exclamation-triangle mr-3 text-xl"></i></div>
                            <div>
                                <p class="font-bold">تنبيه: البيانات مجمّدة</p>
                                <p class="text-sm">تم اعتماد بياناتك وتجميد التعديل على بيانات السكن تجنباً لفقدان حقك في الإدراج على بيانات المستفيدين. يرجى مراجعة الإدارة بهذا الخصوص.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- البيانات الشخصية -->
                <div class="content-section active" id="personal-info">
                    <div class="section-header">
                        <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                        <button class="edit-btn" onclick="openPopup()">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>

                    <div class="info-grid">
                        <div style="grid-column: 1 / -1; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;" class="name-display-row">
                            <div class="info-item-display">
                                <label><i class="fas fa-user"></i> الاسم الأول</label>
                                <div class="value">{{ $person->first_name }}</div>
                            </div>
                            <div class="info-item-display">
                                <label><i class="fas fa-user"></i> اسم الأب</label>
                                <div class="value">{{ $person->father_name }}</div>
                            </div>
                            <div class="info-item-display">
                                <label><i class="fas fa-user"></i> اسم الجد</label>
                                <div class="value">{{ $person->grandfather_name }}</div>
                            </div>
                            <div class="info-item-display">
                                <label><i class="fas fa-users"></i> اسم العائلة</label>
                                <div class="value">{{ $person->family_name }}</div>
                            </div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-id-card"></i> رقم الهوية</label>
                            <div class="value">{{ $person->id_num }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-calendar"></i> تاريخ الميلاد</label>
                            <div class="value">{{ $person->dob ? $person->dob->format('d/m/Y') : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-phone"></i> رقم الجوال</label>
                            <div class="value">{{ $person->phone }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-venus-mars"></i> الجنس</label>
                            <div class="value">{{ $person->gender ? __($person->gender) : 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>

                <!-- البيانات الاجتماعية والعمل -->
                <div class="content-section" id="social-work-info">
                    <div class="section-header">
                        <h3><i class="fas fa-briefcase"></i> البيانات الاجتماعية والعمل</h3>
                        <button class="edit-btn" onclick="openPopup()">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>

                    <div class="info-grid">
                        <div class="info-item-display">
                            <label><i class="fas fa-heart"></i> الحالة الاجتماعية</label>
                            <div class="value">{{ $person->social_status ? __($person->social_status) : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-users"></i> عدد أفراد الأسرة</label>
                            <div class="value">{{ $person->relatives_count }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-briefcase"></i> حالة العمل</label>
                            <div class="value">{{ $person->employment_status }}</div>
                        </div>
                    </div>
                </div>

                <!-- الحالة الصحية -->
                <div class="content-section" id="health-info">
                    <div class="section-header">
                        <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                        <button class="edit-btn" onclick="openPopup()">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>

                    <div class="info-grid">
                        <div class="info-item-display">
                            <label><i class="fas fa-notes-medical"></i> هل يعاني من حالة صحية؟</label>
                            <div class="value">{{ $person->has_condition ? 'نعم' : 'لا' }}</div>
                        </div>
                        @if ($person->has_condition)
                        <div class="info-item-display" style="grid-column: 1 / -1;">
                            <label><i class="fas fa-comment-medical"></i> وصف الحالة الصحية</label>
                            <div class="value">{{ $person->condition_description }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- بيانات السكن -->
                <div class="content-section" id="housing-info">
                    <div class="section-header">
                        <h3><i class="fas fa-home"></i> بيانات السكن</h3>
                        <button class="edit-btn" onclick="openPopup()">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>

                    <div class="info-grid">
                        <div class="info-item-display">
                            <label><i class="fas fa-map-marker-alt"></i> المحافظة الأصلية</label>
                            <div class="value">{{ $person->city ? __($person->city) : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-house-damage"></i> حالة السكن السابق</label>
                            <div class="value">{{ $person->housing_damage_status ? __($person->housing_damage_status) : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-map-marked-alt"></i> المحافظة الحالية</label>
                            <div class="value">{{ $person->current_city ? __($person->current_city) : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-building"></i> نوع السكن الحالي</label>
                            <div class="value">{{ $person->housing_type ? __($person->housing_type) : 'غير محدد' }}</div>
                        </div>
                        <div class="info-item-display">
                            <label><i class="fas fa-map-signs"></i> الحي السكني الحالي</label>
                            <div class="value">{{ $person->neighborhood ? __($person->neighborhood) : 'غير محدد' }}</div>
                        </div>
                        @if($person->areaResponsible)
                        <div class="info-item-display">
                            <label><i class="fas fa-user-tie"></i> مسؤول المنطقة</label>
                            <div class="value">{{ $person->areaResponsible->name }}</div>
                        </div>
                        @endif
                        <div class="info-item-display">
                            <label><i class="fas fa-landmark"></i> أقرب معلم</label>
                            <div class="value">{{ $person->landmark ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>

                <!-- بيانات أفراد الأسرة -->
                <div class="content-section" id="family-members">
                    <div class="section-header">
                        <h3><i class="fas fa-users"></i> بيانات أفراد الأسرة</h3>
                        <button class="add-btn" id="open-form">
                            <i class="fas fa-plus"></i> إضافة فرد  جديد
                        </button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>رقم الهوية</th>
                                <th>الاسم الأول</th>
                                <th>اسم الأب</th>
                                <th>اسم الجد</th>
                                <th>اسم العائلة</th>
                                <th>صلة القرابة</th>
                                <th>تاريخ الميلاد</th>
                                <th>رقم الجوال</th>
                                <th>حالة صحية</th>
                                <th>وصف الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($person->familyMembers as $familyMember)
                            <tr>
                                <td>{{ $familyMember->id_num }}</td>
                                <td>{{ $familyMember->first_name }}</td>
                                <td>{{ $familyMember->father_name }}</td>
                                <td>{{ $familyMember->grandfather_name }}</td>
                                <td>{{ $familyMember->family_name }}</td>
                                <td>{{ __($familyMember->relationship) }}</td>
                                <td>{{ $familyMember->dob ? \Carbon\Carbon::parse($familyMember->dob)->format('d/m/Y') : 'غير محدد' }}</td>
                                <td>{{ $familyMember->phone ?? '-' }}</td>
                                <td>{{ $familyMember->has_condition == 1 ? 'نعم' : 'لا' }}</td>
                                <td>{{ $familyMember->condition_description ?? 'لا يوجد' }}</td>
                                <td>
                                    <a href="#" class="action-icon" onclick="editFamilyMember({{ $familyMember->id }})">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="#" class="action-icon delete-icon" onclick="deletePerson({{ $familyMember->id }})">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- الشكاوي -->
                <div class="content-section" id="complaints">
                    <div class="section-header">
                        <h3><i class="fas fa-comments"></i> قائمة الشكاوى</h3>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>رقم الهوية</th>
                                <th>عنوان الشكوى</th>
                                <th>نص الشكوى</th>
                                <th>حالة الشكوى</th>
                                <th>الرد</th>
                                <th>الموظف المسؤول</th>
                                <th>تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->id_num }}</td>
                                <td>{{ $complaint->complaint_title }}</td>
                                <td>{{ Str::limit($complaint->complaint_text, 50) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => '#ffc107',
                                            'in_progress' => '#17a2b8',
                                            'resolved' => '#28a745',
                                            'rejected' => '#dc3545',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_progress' => 'قيد المعالجة',
                                            'resolved' => 'تم الحل',
                                            'rejected' => 'مرفوضة',
                                        ];
                                        $status = $complaint->status ?? 'pending';
                                    @endphp
                                    <span style="
                                        background-color: {{ $statusColors[$status] }};
                                        color: white;
                                        padding: 5px 10px;
                                        border-radius: 4px;
                                        font-size: 12px;
                                        font-weight: bold;
                                        display: inline-block;
                                    ">
                                        {{ $statusLabels[$status] }}
                                    </span>
                                </td>
                                <td>
                                    @if($complaint->response)
                                        {{ Str::limit($complaint->response, 40) }}
                                        @if($complaint->responded_at)
                                            <br>
                                            <small style="color: #999;">
                                                {{ $complaint->responded_at->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span style="color: #999;">لم يتم الرد بعد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($complaint->responder)
                                        <strong>{{ $complaint->responder->name }}</strong>
                                        @if($complaint->responded_at)
                                            <br>
                                            <small style="color: #999;">{{ $complaint->responded_at->format('d/m/Y H:i') }}</small>
                                        @endif
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $complaint->created_at ? $complaint->created_at->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- بوب أب تعديل كلمة المرور -->
    <div class="popup hidden" id="password-popup">
        <div class="popup-content">
            <span class="close" onclick="closepasswordpopup()">&times;</span>
            <h1>تعديل كلمة المرور</h1>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <input type="hidden" id="id_num" value="{{ $person->id_num }}">

            <!-- كلمة المرور القديمة -->
            <div class="form-group">
                <label for="old-password"><i class="fas fa-lock"></i> كلمة المرور القديمة</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="old-password" name="passkey" required>
                    <i class="fa fa-eye toggle-password" data-target="old-password"></i>
                </div>
            </div>

            <!-- كلمة المرور الجديدة -->
            <div class="form-group">
                <label for="new-password"><i class="fas fa-key"></i> كلمة المرور الجديدة</label>
                <div class="input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" id="new-password" name="new_passkey" required>
                    <i class="fa fa-eye toggle-password" data-target="new-password"></i>
                </div>
                <div class="password-requirements">
                    <span id="length-check">9-15 حرفًا <i>✔</i></span>
                    <span id="uppercase-check">حرف كبير A-Z <i>✔</i></span>
                    <span id="lowercase-check">حرف صغير a-z <i>✔</i></span>
                    <span id="number-check">رقم واحد على الأقل <i>✔</i></span>
                    <span id="symbol-check">رمز خاص (!@#$%^&*) <i>✔</i></span>
                </div>
            </div>

            <!-- تأكيد كلمة المرور الجديدة -->
            <div class="form-group">
                <label for="confirm-password"><i class="fas fa-check-circle"></i> تأكيد كلمة المرور الجديدة</label>
                <div class="input-wrapper">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password" id="confirm-password" name="confirm_passkey" required>
                    <i class="fa fa-eye toggle-password" data-target="confirm-password"></i>
                </div>
                <div class="password-requirements">
                    <span id="match-check">تطابق كلمة المرور <i>✔</i></span>
                </div>
            </div>

            <button class="save-btn" onclick="saveChangesPassword()">حفظ التغييرات</button>
            <div class="error-message hidden" id="password-error"></div>
        </div>
    </div>

        <!-- تعديل البيانات الشخصية -->
        <div id="editPopup" class="popup hidden">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <div class="popup-form-section">
                    <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                    <div class="row name-row">
                        <div class="form-group">
                            <label for="edit_first_name">الاسم الأول</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="edit_first_name" name="first_name" placeholder="الاسم الأول" value="{{ $person->first_name }}" oninput="validateArabicInput('edit_first_name')" onfocus="resetBorderAndError('edit_first_name')" required>
                            </div>
                            <div class="error-message" id="edit_first_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_father_name">اسم الأب</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="edit_father_name" name="father_name" value="{{ $person->father_name }}" placeholder="اسم الأب" oninput="validateArabicInput('edit_father_name')" onfocus="resetBorderAndError('edit_father_name')" required>
                            </div>
                            <div class="error-message" id="edit_father_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_grandfather_name">اسم الجد</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="edit_grandfather_name" name="grandfather_name" value="{{ $person->grandfather_name }}" placeholder="اسم الجد" oninput="validateArabicInput('edit_grandfather_name')" onfocus="resetBorderAndError('grandfather_name')" required>
                            </div>
                            <div class="error-message" id="edit_grandfather_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_family_name">اسم العائلة</label>
                            <div class="input-wrapper">
                                <i class="fas fa-users input-icon"></i>
                                <input type="text" id="edit_family_name" name="family_name" value="{{ $person->family_name }}" placeholder="اسم العائلة" oninput="validateArabicInput('edit_family_name')" onfocus="resetBorderAndError('edit_family_name')" required>
                            </div>
                            <div class="error-message" id="edit_family_name_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_id_num">رقم الهوية <small>(405240862)</small></label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="number" id="edit_id_num" name="id_num" value="{{ $person->id_num }}" data-original="{{ $person->id_num }}" required oninput="validateIdOnInput()" maxlength="9">
                            </div>
                            <span id="edit_id_num_error" class="error-message">رقم الهوية غير صالح.</span>
                            <span id="edit_id_num_success" class="success-message" style="display:none;">رقم الهوية صحيح.</span>
                        </div>

                        <div class="form-group">
                            <label for="edit_gender">الجنس</label>
                            <div class="input-wrapper">
                                <i class="fas fa-venus-mars input-icon"></i>
                                <select id="edit_gender" name="gender" required oninput="validateEditGender()" onfocus="resetBorderAndError('edit_gender')">
                                    <option value="">اختر الجنس</option>
                                    @foreach(['ذكر' => 'ذكر', 'أنثى' => 'أنثى', 'غير محدد' => 'غير محدد'] as $key => $gender)
                                        <option value="{{ $key }}" {{ $person->gender == $key ? 'selected' : '' }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_gender_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_dob">تاريخ الميلاد <small>(dd/mm/yyyy)</small></label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar input-icon"></i>
                                <input type="date" id="edit_dob" name="dob" value="{{ $person->dob ? \Carbon\Carbon::parse($person->dob)->format('Y-m-d') : '' }}" oninput="validatedob()" onfocus="resetBorderAndError('edit_dob')" required>
                            </div>
                            <div class="error-message" id="edit_dob_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_phone">رقم الجوال المعتمد</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text" class="text-left" dir="ltr" placeholder="059-123-1234" id="edit_phone" name="phone" value="{{ $person->phone }}" oninput="validatePhoneInput()" onfocus="resetBorderAndError('edit_phone')" required>
                            </div>
                            <div class="error-message" id="edit_phone_error"></div>
                        </div>
                    </div>
                </div>


                <div class="popup-form-section">
                    <h3><i class="fas fa-briefcase"></i> الحالة الاجتماعية والوظيفية</h3>
                    <div class="row">
                        <div class="form-group">
                            <label for="edit_social_status">الحالة الاجتماعية</label>
                            <div class="input-wrapper">
                                <i class="fas fa-heart input-icon"></i>
                                <select id="edit_social_status" name="social_status" required oninput="validateSocialStatus()" onfocus="resetBorderAndError('edit_social_status')">
                                    <option value="">اختر الحالة</option>
                                    @foreach($social_statuses as $key => $status)
                                        <option value="{{ $key }}" {{ $person->social_status == $key ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_social_status_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_relatives_count">عدد أفراد الأسرة</label>
                            <div class="input-wrapper">
                                <i class="fas fa-users input-icon"></i>
                                <input type="number" id="edit_relatives_count" name="relatives_count" value="{{ $person->relatives_count }}" placeholder="عدد أفراد الأسرة" min="0" required onfocus="resetBorderAndError('edit_relatives_count')">
                            </div>
                            <div class="error-message" id="edit_relatives_count_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_employment_status">حالة العمل</label>
                            <div class="input-wrapper">
                                <i class="fas fa-briefcase input-icon"></i>
                                <select id="edit_employment_status" name="employment_status" required oninput="validateEmploymentStatus()" onfocus="resetBorderAndError('edit_employment_status')">
                                    <option value="لا يعمل" {{ old('employment_status', $person->employment_status) == 'لا يعمل' ? 'selected' : '' }}>لا يعمل</option>
                                    <option value="موظف" {{ old('employment_status', $person->employment_status) == 'موظف' ? 'selected' : '' }}>موظف</option>
                                    <option value="عامل" {{ old('employment_status', $person->employment_status) == 'عامل' ? 'selected' : '' }}>عامل</option>
                                </select>
                            </div>
                            <div class="error-message" id="edit_employment_status_error"></div>
                        </div>
                    </div>
                </div>

                <div class="popup-form-section">
                    <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                    <div class="row">
                        <div class="form-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label for="edit_has_condition">هل لديك حالة صحية مرض مزمن حالة خالصة إصابة حرب ؟</label>
                            <select id="edit_has_condition" name="has_condition" onchange="toggleConditionDescription()">
                                <option value="0" {{ old('has_condition', $person->has_condition) == '0' ? 'selected' : '' }}>لا</option>
                                <option value="1" {{ old('has_condition', $person->has_condition) == '1' ? 'selected' : '' }}>نعم</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="edit_condition_description_group" style="display: none;">
                        <label for="edit_condition_description">وصف الحالة الصحية</label>
                        <textarea id="edit_condition_description" name="condition_description" rows="4" oninput="validateConditionText()" onfocus="resetBorderAndError('edit_condition_description')">{{ $person->condition_description }}</textarea>
                        <div class="error-message" id="edit_condition_description_error"></div>
                    </div>
                </div>

                <div class="popup-form-section">
                    <h3><i class="fas fa-home"></i> معلومات السكن</h3>
                    <div class="row">
                        <div class="form-group">
                            <label for="edit_city">المحافظة الأصلية</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <select id="edit_city" name="city" required oninput="validateCity()" onfocus="resetBorderAndError('edit_city')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">اختر المحافظة الأصلية</option>
                                    @foreach($cities as $key => $city)
                                        <option value="{{ $key }}" {{ $person->city == $key ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_city_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_housing_damage_status">حالة السكن السابق</label>
                            <div class="input-wrapper">
                                <i class="fas fa-house-damage input-icon"></i>
                                <select id="edit_housing_damage_status" name="housing_damage_status" required oninput="validateHousingDamageStatus()" onfocus="resetBorderAndError('edit_housing_damage_status')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">اختر حالة السكن السابق</option>
                                    @foreach($housing_damage_statuses as $key => $status)
                                        <option value="{{ $key }}" {{ $person->housing_damage_status == $key ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_housing_damage_status_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_current_city">المحافظة التي تسكن فيها حالياً</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marked-alt input-icon"></i>
                                <select id="edit_current_city" name="current_city" required oninput="validateCurrentCity()" onfocus="resetBorderAndError('edit_current_city')" onchange="updateNeighborhoods(this.value, '{{ $person->neighborhood }}')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">خان يونس</option> {{-- افتراض القيمة الأولى كما طلب، لكن سأبقي القائمة --}}
                                    @foreach($current_cities as $key => $city)
                                        <option value="{{ $key }}" {{ $person->current_city == $key ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_current_city_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_housing_type">نوع السكن الذي تعيش فيه حالياً</label>
                            <div class="input-wrapper">
                                <i class="fas fa-building input-icon"></i>
                                <select id="edit_housing_type" name="housing_type" required oninput="validateHousingType()" onfocus="resetBorderAndError('edit_housing_type')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">اختر نوع السكن الذي تعيش فيه حالياً</option>
                                    @foreach($housing_types as $key => $type)
                                        <option value="{{ $key }}" {{ $person->housing_type == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_housing_type_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_neighborhood">الحي السكني الذي تتواجد فيه حالياً</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-signs input-icon"></i>
                                <select id="edit_neighborhood" name="neighborhood" required oninput="validateNeighborhood()" onfocus="resetBorderAndError('edit_neighborhood')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">اختر الحي</option>
                                    @foreach($neighborhoods as $key => $neighborhood)
                                        <option value="{{ $key }}" {{ $person->neighborhood == $key ? 'selected' : '' }}>{{ $neighborhood }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_neighborhood_error"></div>
                        </div>

                        <div class="form-group area-responsible-container" id="edit_areaResponsibleField" style="display:none;">
                            <label for="edit_area_responsible_id">مسؤول المنطقة</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user-tie input-icon"></i>
                                <select class="form-control" name="area_responsible_id" id="edit_area_responsible_id" oninput="validateAreaResponsible()" onfocus="resetBorderAndError('edit_area_responsible_id')" {{ $person->is_frozen ? 'disabled' : '' }}>
                                    <option value="">اختر المسؤول</option>
                                    @foreach (\App\Models\AreaResponsible::all() as $responsible)
                                        <option value="{{ $responsible->id }}" {{ $person->area_responsible_id == $responsible->id ? 'selected' : '' }}>{{ $responsible->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="edit_area_responsible_id_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit_landmark">أقرب معلم</label>
                            <div class="input-wrapper">
                                <i class="fas fa-landmark input-icon"></i>
                                <input type="text" id="edit_landmark" name="landmark" placeholder="أقرب معلم" value="{{ $person->landmark }}" oninput="validateArabicInput('edit_landmark')" onfocus="resetBorderAndError('edit_landmark')" {{ $person->is_frozen ? 'readonly' : '' }}>
                            </div>
                            <div class="error-message" id="edit_landmark_error"></div>
                        </div>
                    </div>
                </div>

                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!-- زر حفظ التعديلات -->
                <button class="save-btn" onclick="saveChangesParent()">حفظ التغييرات</button>
            </div>
        </div>

        <!-- بيانات أفراد الأسرة -->
        {{--هاد الفورم لإضافة فرد جديد على العائلة مفروض يضيف على الداتا بيز --}}
        {{-- <h1 style="text-align: right">بيانات أفراد الأسرة:
            <a href="#" id="open-form" class="add-icon">
                <i class="fas fa-plus"></i>
            </a>
        </h1> --}}

        <!-- إضافة فرد جديد -->
        <div id="form-popup" class="popup hidden">
            <div class="popup-content">
                <span class="close" id="closse-popup">&times;</span>
                <div id="overlay" class="overlay-class hidden"></div>
                <h1>إضافة بيانات فرد</h1>
                <input type="hidden" id="familyMemberId" name="id">

                <div class="popup-form-section">
                    <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                    <div class="row name-row">
                        <div class="form-group">
                            <label for="first_namef">الاسم الأول <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="first_namef" name="first_namef" placeholder="الاسم الأول" oninput="validateArabicInput('first_namef')" onfocus="resetBorderAndError('first_namef')" required>
                            </div>
                            <div class="error-message" id="first_namef_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="father_namef">اسم الأب <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="father_namef" name="father_namef" placeholder="اسم الأب" oninput="validateArabicInput('father_namef')" onfocus="resetBorderAndError('father_namef')" required>
                            </div>
                            <div class="error-message" id="father_namef_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="grandfather_namef">اسم الجد <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="grandfather_namef" name="grandfather_namef" placeholder="اسم الجد" oninput="validateArabicInput('grandfather_namef')" onfocus="resetBorderAndError('grandfather_namef')" required>
                            </div>
                            <div class="error-message" id="grandfather_namef_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="family_namef">اسم العائلة <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-users input-icon"></i>
                                <input type="text" id="family_namef" name="family_namef" placeholder="اسم العائلة" oninput="validateArabicInput('family_namef')" onfocus="resetBorderAndError('family_namef')" required>
                            </div>
                            <div class="error-message" id="family_namef_error"></div>
                        </div>
                    </div>

                    <div class="row">
                         <div class="form-group">
                            <label for="relationshipf">صلة القرابة <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user-friends input-icon"></i>
                                <select id="relationshipf" name="relationshipf" required onchange="handleRelationshipChangeForAdd()">
                                    @foreach($relationships as $key => $relationship)
                                        <option value="{{$key}}">{{$relationship}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="id_numf">رقم الهوية</label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="number" id="id_numf" name="id_numf" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInputid()" maxlength="9">
                            </div>
                            <span id="id_numf_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                            <span id="id_numf_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
                        </div>

                        <div class="form-group">
                            <label for="dobf">تاريخ الميلاد</label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar input-icon"></i>
                                <input type="date" id="dobf" name="dobf" oninput="validatedob()" onfocus="resetBorderAndError('dobf')" required>
                            </div>
                            <div class="error-message" id="dobf_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group" id="phone_group" style="display: none;">
                            <label for="phone">رقم الجوال <span style="color: red;">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" placeholder="مثال: 0599123456" maxlength="10" pattern="[0-9]{10}">
                            </div>
                            <span id="phoneerror" class="error-message" style="display:none; color: #ff0000;">رقم الجوال غير صحيح</span>
                        </div>
                    </div>
                </div>

                <div class="popup-form-section">
                    <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <label for="has_condition">هل لديك حالة صحية؟</label>
                        <select id="has_conditionf" name="has_conditionf" onchange="toggleConditionText()" style="width: auto; min-width: 100px;">
                            <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                            <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                        </select>
                    </div>

                    <div class="form-group" id="condition_description_group" style="display: none;">
                        <label for="condition_description">وصف الحالة الصحية</label>
                        <textarea id="condition_descriptionf" name="condition_descriptionf" rows="4" oninput="validateConditionText()" onfocus="resetBorderAndError('condition_descriptionf')"></textarea>
                        <div class="error-message" id="condition_descriptionf_error"></div>
                    </div>
                </div>

                <button class="save-btn" type="button" id="add-person-btn">حفظ التغييرات</button>
            </div>
        </div>

        <!-- جدول بيانات أفراد الأسرة -->
        {{-- <table class="family-table">
            <thead>
                <tr>
                    <th class="border px-4 py-2">رقم الهوية</th>
                    <th class="border px-4 py-2"> الاسم الأول</th>
                    <th class="border px-4 py-2"> اسم الأب</th>
                    <th class="border px-4 py-2"> اسم الجد</th>
                    <th class="border px-4 py-2"> اسم العائلة</th>
                    <th class="border px-4 py-2">صلة القرابة</th>
                    <th class="border px-4 py-2">تاريخ الميلاد</th>
                    <!-- التعديل الجديد: إضافة عمود رقم الجوال -->
                    <th class="border px-4 py-2">رقم الجوال</th>
                    <th class="border px-4 py-2">حالة الصحية سليم؟</th>
                    <th class="border px-4 py-2">وصف الحالة</th>
                    <th class="border px-4 py-2">العملية</th>
                </tr>
            </thead>
            <tbody>
                @foreach($person->familyMembers as $familyMember)
                    <tr>
                        <td>{{ $familyMember->id_num }}</td>
                        <td>{{ $familyMember->first_name }}</td>
                        <td>{{ $familyMember->father_name }}</td>
                        <td>{{ $familyMember->grandfather_name }}</td>
                        <td>{{ $familyMember->family_name }}</td>
                        <td>{{ __($familyMember->relationship) }}</td>
                        <td>{{ $familyMember->dob ? \Carbon\Carbon::parse($familyMember->dob)->format('d/m/Y') : 'غير محدد' }}</td>
                        <!-- التعديل الجديد: عرض رقم الجوال إذا كان موجوداً -->
                        <td>{{ $familyMember->phone ?? '-' }}</td>
                        <td>{{ $familyMember->has_condition == 1 ? 'نعم' : 'لا' }}</td>
                        <td>{{ $familyMember->condition_description ?? 'لا يوجد' }}</td>
                        <!-- عمود أيقونة التعديل -->
                        <td>
                            <!-- أيقونة التعديل -->
                            <a href="#" class="edit-icon" onclick="editFamilyMember({{ $familyMember->id }})">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- أيقونة الحذف -->
                            <a href="#" class="delete-icon" onclick="deletePerson({{ $familyMember->id }})">
                                <i class="fa fa-trash"></i> <!-- أيقونة الحذف -->
                            </a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table> --}}

        <!-- تعديل بيانات فرد الأسرة -->
        <div id="editFamilyMemberModal" class="popup hidden">
            <div class="popup-content">
                <span class="close" onclick="closeModal1()">&times;</span>
                <h1>تعديل بيانات فرد الأسرة</h1>
                <form id="editFamilyMemberForm">
                    <input type="hidden" id="familyMemberId" name="id">

                    <div class="popup-form-section">
                        <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
                        <div class="row name-row">
                            <div class="form-group">
                                <label for="edit_f_first_name">الاسم الأول <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="edit_f_first_name" name="first_name" placeholder="الاسم الأول" required oninput="validateArabicInput('edit_f_first_name')" onfocus="resetBorderAndError('edit_f_first_name')">
                                </div>
                                <div class="error-message" id="edit_f_first_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="edit_f_father_name">اسم الأب <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="edit_f_father_name" name="father_name" placeholder="اسم الأب" required oninput="validateArabicInput('edit_f_father_name')" onfocus="resetBorderAndError('edit_f_father_name')">
                                </div>
                                <div class="error-message" id="edit_f_father_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="edit_f_grandfather_name">اسم الجد <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="edit_f_grandfather_name" name="grandfather_name" placeholder="اسم الجد" required oninput="validateArabicInput('edit_f_grandfather_name')" onfocus="resetBorderAndError('edit_f_grandfather_name')">
                                </div>
                                <div class="error-message" id="edit_f_grandfather_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="edit_f_family_name">اسم العائلة <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-users input-icon"></i>
                                    <input type="text" id="edit_f_family_name" name="family_name" placeholder="اسم العائلة" required oninput="validateArabicInput('edit_f_family_name')" onfocus="resetBorderAndError('edit_f_family_name')">
                                </div>
                                <div class="error-message" id="edit_f_family_name_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="edit_f_relationship">صلة القرابة <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user-friends input-icon"></i>
                                    <select id="edit_f_relationship" name="relationship" required onchange="handleEditRelationshipChange()">
                                        @foreach($relationships as $key => $relationship)
                                            <option value="{{$key}}">{{$relationship}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_f_id_num">رقم الهوية</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="number" id="edit_f_id_num" name="id_num" required oninput="validateIdOnInputid()" maxlength="9">
                                </div>
                                <span id="edit_f_id_num_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                                <span id="edit_f_id_num_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
                            </div>

                            <div class="form-group">
                                <label for="edit_f_dob">تاريخ الميلاد</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar input-icon"></i>
                                    <input type="date" id="edit_f_dob" name="dob" required>
                                </div>
                                <div class="error-message" id="edit_f_dob_error"></div>
                            </div>

                            <div class="form-group" id="edit_f_phone_group" style="display: none;">
                                <label for="edit_f_phone">رقم الجوال <span style="color: red;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="tel" id="edit_f_phone" name="phone" placeholder="مثال: 0599123456" maxlength="10" pattern="[0-9]{10}">
                                </div>
                                <span id="edit_f_phone_error" class="error-message" style="display:none; color: #ff0000;">رقم الجوال غير صحيح</span>
                            </div>
                        </div>
                    </div>

                    <div class="popup-form-section">
                        <h3><i class="fas fa-heartbeat"></i> الحالة الصحية</h3>
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <label for="edit_f_has_condition">هل لديك حالة صحية؟</label>
                            <select id="edit_f_has_condition" name="has_condition" onchange="toggleConditionText1()" style="width: auto; min-width: 100px;">
                                <option value="0">لا</option>
                                <option value="1">نعم</option>
                            </select>
                        </div>

                        <div class="form-group" id="edit_f_condition_description_group" style="display: none;">
                            <label for="edit_f_condition_description">وصف الحالة الصحية</label>
                            <textarea id="edit_f_condition_description" name="condition_description" rows="4"></textarea>
                            <div class="error-message" id="edit_f_condition_description_error"></div>
                        </div>
                    </div>

                    <button class="save-btn" type="button" onclick="saveChangesChild()">حفظ التعديلات</button>
                </form>
            </div>
        </div>

        {{-- <h1 style="text-align: right">قائمة الشكاوى: --}}
            {{-- <a href="{{ route('complaints.create') }}" id="open-complaint-form" class="add-icon">
                <i class="fas fa-plus"></i>
            </a> --}}
        {{-- </h1> --}}

        {{-- <table class="complaints-table">
            <thead>
                <tr>
                    <th class="border px-4 py-2">رقم الهوية</th>
                    <th class="border px-4 py-2">عنوان الشكوى</th>
                    <th class="border px-4 py-2">نص الشكوى</th>
                    <th class="border px-4 py-2">حالة الشكوى</th>
                    <th class="border px-4 py-2">الرد</th>
                    <th class="border px-4 py-2">الموظف المسؤول</th>
                    <th class="border px-4 py-2">تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                    <tr>
                        <td class="border px-4 py-2">{{ $complaint->id_num }}</td>
                        <td class="border px-4 py-2">{{ $complaint->complaint_title }}</td>
                        <td class="border px-4 py-2">{{ Str::limit($complaint->complaint_text, 50) }}</td>
                        <td class="border px-4 py-2">
                            @php
                                $statusColors = [
                                    'pending' => '#ffc107',
                                    'in_progress' => '#17a2b8',
                                    'resolved' => '#28a745',
                                    'rejected' => '#dc3545',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد الانتظار',
                                    'in_progress' => 'قيد المعالجة',
                                    'resolved' => 'تم الحل',
                                    'rejected' => 'مرفوضة',
                                ];
                                $status = $complaint->status ?? 'pending';
                            @endphp
                            <span style="
                                background-color: {{ $statusColors[$status] }};
                                color: white;
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: bold;
                                display: inline-block;
                            ">
                                {{ $statusLabels[$status] }}
                            </span>
                        </td>

                        <td class="border px-4 py-2">
                            @if($complaint->response)
                                {{ Str::limit($complaint->response, 40) }}
                                @if($complaint->responded_at)
                                    <br>
                                    <small class="text-muted">
                                        {{ $complaint->responded_at->format('d/m/Y') }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">لم يتم الرد بعد</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            @if($complaint->responder)
                                <span class="font-weight-bold">{{ $complaint->responder->name }}</span>
                                @if($complaint->responded_at)
                                    <br>
                                    <small class="text-muted">{{ $complaint->responded_at->format('d/m/Y H:i') }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            {{ $complaint->created_at ? $complaint->created_at->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}

    </div>

    <script>
        // ========================================
        // وظائف مساعدة (Helpers)
        // ========================================

        // دالة موحدة للتحقق مما إذا كانت صلة القرابة تتطلب رقم جوال
        function isWife(val) {
            if (!val) return false;
            const normalized = val.toString().trim().toLowerCase();
            const wifeValues = ['wife', 'spouse', 'زوجة', 'الزوجة'];
            const res = wifeValues.includes(normalized);
            console.log("🕵️ التحقق من صلة القرابة:", normalized, "النتيجة:", res);
            return res;
        }

        // دالة موحدة للتحكم في ظهور حقل الجوال بناءً على صلة القرابة
        function togglePhoneVisibility(relationshipSelectId, phoneGroupId, phoneInputId, phoneErrorId) {
            const relationshipSelect = document.getElementById(relationshipSelectId);
            const phoneGroup = document.getElementById(phoneGroupId);
            const phoneInput = document.getElementById(phoneInputId);
            const phoneError = document.getElementById(phoneErrorId);

            if (!relationshipSelect || !phoneGroup || !phoneInput) return;

            if (isWife(relationshipSelect.value)) {
                phoneGroup.style.display = 'block';
                phoneInput.required = true;
            } else {
                phoneGroup.style.display = 'none';
                phoneInput.required = false;
                phoneInput.value = '';
                if (phoneError) {
                    phoneError.style.display = 'none';
                    phoneInput.style.borderColor = '';
                }
            }

            // تفعيل التحقق إذا كان الحقل معروضاً وبه قيمة
            if (phoneInput.required && phoneInput.value) {
                validatePhone(phoneInputId);
            }
        }

        // دالة للتحقق من صحة رقم الجوال
        function validatePhone(phoneFieldId) {
            const phoneInput = document.getElementById(phoneFieldId);
            const phoneValue = phoneInput.value;
            const phoneError = document.getElementById(phoneFieldId + '_error');

            if (!phoneInput) return true;

            // التحقق من أن الرقم 10 خانات ويبدأ بـ 05
            const phonePattern = /^05[0-9]{8}$/;

            if (phoneValue && !phonePattern.test(phoneValue)) {
                if (phoneError) phoneError.style.display = 'inline';
                phoneInput.style.borderColor = '#ff0000';
                return false;
            } else {
                if (phoneError) phoneError.style.display = 'none';
                phoneInput.style.borderColor = '';
                return true;
            }
        }

        // ========================================
        // منطق الإضافة (Add)
        // ========================================

        function handleRelationshipChangeForAdd() {
            togglePhoneVisibility('relationshipf', 'phone_group', 'phone', 'phone_error');
        }

        // ========================================
        // منطق التعديل (Edit)
        // ========================================

        function handleEditRelationshipChange() {
            togglePhoneVisibility('edit_f_relationship', 'edit_f_phone_group', 'edit_f_phone', 'edit_f_phone_error');
        }

        // حفظ نسخة من كافة خيارات مسؤول المنطقة الأصلية عند تحميل الصفحة (بحقل edit_area_responsible_id)
        const areaResponsibleSelect = document.getElementById('edit_area_responsible_id');
        const areaResponsibleField = document.getElementById('edit_areaResponsibleField');
        const allOptions = Array.from(areaResponsibleSelect.options).map(option => ({
            value: option.value,
            text: option.text
        }));

        // متغير لتخزين القيمة الأصلية لمسؤول المنطقة
        let originalAreaResponsibleValue = areaResponsibleSelect.value;

        document.getElementById('edit_neighborhood').addEventListener('change', function() {
            const neighborhood = this.value;
            // حفظ القيمة الحالية قبل التغيير
            const currentValue = areaResponsibleSelect.value;

            function showOptions(ids, addPlaceholder = false, defaultValue = null) {
                areaResponsibleSelect.innerHTML = '';

                if (addPlaceholder) {
                    const placeholderOption = document.createElement('option');
                    placeholderOption.value = '';
                    placeholderOption.text = 'اختر مسؤول المنطقة';
                    placeholderOption.disabled = true;
                    placeholderOption.selected = true;
                    areaResponsibleSelect.appendChild(placeholderOption);
                }

                ids.forEach(id => {
                    const opt = allOptions.find(o => o.value === id.toString());
                    if (opt) {
                        const optionElement = document.createElement('option');
                        optionElement.value = opt.value;
                        optionElement.text = opt.text;
                        // تعيين تحديد القيمة الافتراضية أو القيمة المحفوظة
                        if (defaultValue && opt.value === defaultValue.toString()) {
                            optionElement.selected = true;
                        } else if (currentValue && opt.value === currentValue.toString()) {
                            optionElement.selected = true;
                        }
                        areaResponsibleSelect.appendChild(optionElement);
                    }
                });

                // إذا كانت القيمة المحفوظة موجودة في القائمة الجديدة، استخدمها
                if (currentValue && ids.includes(parseInt(currentValue))) {
                    areaResponsibleSelect.value = currentValue;
                } else if (!addPlaceholder && !defaultValue) {
                    areaResponsibleSelect.value = '';
                }
            }

            // تحديث عرض خيارات مسؤول المنطقة بناءً على الحي المختار
            if (neighborhood === 'alMawasi') {
                areaResponsibleField.style.display = 'flex';
                let excluded = ['29', '30', '31', '32', '33', '34','35'];
                const filtered = allOptions.filter(o => !excluded.includes(o.value));
                areaResponsibleSelect.innerHTML = '';
                filtered.forEach(opt => {
                    const optionElement = document.createElement('option');
                    optionElement.value = opt.value;
                    optionElement.text = opt.text;
                    // الاحتفاظ بالقيمة المحفوظة
                    if (currentValue && opt.value === currentValue) {
                        optionElement.selected = true;
                    }
                    areaResponsibleSelect.appendChild(optionElement);
                });
                if (currentValue && filtered.some(o => o.value === currentValue)) {
                    areaResponsibleSelect.value = currentValue;
                } else {
                    areaResponsibleSelect.value = '';
                }

            } else if (neighborhood === 'hamidCity') {
                areaResponsibleField.style.display = 'flex';
                showOptions(['29'], false, '29');

            } else if (['downtown', 'alQalaaSouth', 'alBatanAlSameen', 'qizanAbuRashwan'].includes(neighborhood)) {
                areaResponsibleField.style.display = 'flex';
                showOptions(['31', '32', '30'], true, null);

            } else if (['westernLine', 'easternLine', 'alMahatta', 'alKatiba', 'northJalalStreet'].includes(neighborhood)) {
                areaResponsibleField.style.display = 'flex';
                showOptions(['34'], false, '34');

            } else if (['alMaskar', 'alMashroo'].includes(neighborhood)) {
                areaResponsibleField.style.display = 'flex';
                showOptions(['33'], false, '33');

            } else {
                areaResponsibleField.style.display = 'none';
                areaResponsibleSelect.innerHTML = '';
                areaResponsibleSelect.value = '';
                document.getElementById('area_responsible_id_error').style.display = 'none';
            }
        });

        function openPopup() {
            document.getElementById("editPopup").classList.remove("hidden");

            let current_city = "{{ $person->current_city }}"
            let neighborhood = "{{ $person->neighborhood }}"
            let areaResponsibleId = "{{ $person->area_responsible_id ?? '' }}"

            // تحديث قائمة الأحياء بناءً على المحافظة وتحديد الحي المخزن
            updateNeighborhoods(current_city, neighborhood);

            // حفظ القيمة الأصلية لمسؤول المنطقة
            originalAreaResponsibleValue = areaResponsibleId;

            // تعيين القيمة الأصلية بعد تحميل الخيارات
            setTimeout(function() {
                if (areaResponsibleId) {
                    const areaResponsibleSelect = document.getElementById('edit_area_responsible_id');
                    // التحقق من أن القيمة موجودة في الخيارات المتاحة
                    const optionExists = Array.from(areaResponsibleSelect.options).some(opt => opt.value === areaResponsibleId);
                    if (optionExists) {
                        areaResponsibleSelect.value = areaResponsibleId;
                    }
                }

                // تشغيل حدث التغيير للحي لضبط الخيارات المناسبة
                document.getElementById('edit_neighborhood').dispatchEvent(new Event('change'));
            }, 100);
        }

        window.onload = function () {
            const currentCitySelect = document.getElementById('edit_current_city');
            const selectedCity = currentCitySelect.value;
            const selectedNeighborhood = '{{ $person->neighborhood }}';
            const originalCity = '{{ $person->current_city }}';
            const areaResponsibleId = '{{ $person->area_responsible_id ?? '' }}';

            updateNeighborhoods(selectedCity, selectedNeighborhood, originalCity);

            // تعيين قيمة مسؤول المنطقة بعد تحميل الخيارات
            setTimeout(function() {
                const areaResponsibleSelect = document.getElementById('edit_area_responsible_id');
                if (areaResponsibleId && areaResponsibleSelect) {
                    const optionExists = Array.from(areaResponsibleSelect.options).some(opt => opt.value === areaResponsibleId);
                    if (optionExists) {
                        areaResponsibleSelect.value = areaResponsibleId;
                        originalAreaResponsibleValue = areaResponsibleId;
                    }
                }

                // تشغيل حدث التغيير للحي لضبط العرض الصحيح
                const editNeighborhood = document.getElementById('edit_neighborhood');
                if (editNeighborhood) {
                    editNeighborhood.dispatchEvent(new Event('change'));
                }
            }, 100);

            currentCitySelect.onchange = function () {
                const cityValue = this.value;
                const neighborhoodValue = '{{ $person->neighborhood }}';
                const originalCityValue = '{{ $person->current_city }}';
                updateNeighborhoods(cityValue, neighborhoodValue, originalCityValue);
            };
        };

        $(document).ready(function() {
            console.log("$(document).ready() تم التنفيذ (الكتلة الرئيسية - مُعدلة 2)");

            // دالة للتحقق من وجود عنصر في DOM
            function elementExists(selector) {
                const exists = $(selector).length > 0;
                console.log(`التحقق من وجود ${selector}: ${exists}`);
                return exists;
            }

            // تهيئة قائمة الإعدادات
            function setupSettingsToggle() {
                const settingsToggle = $('#settings-toggle');
                const settingsDropdown = $('#settings-dropdown');

                if (elementExists('#settings-toggle') && elementExists('#settings-dropdown')) {
                    settingsToggle.on('click', function(event) {
                        event.stopPropagation();
                        console.log("تم النقر على settings-toggle");
                        settingsDropdown.toggle();
                    });

                    $(document).on('click', function(event) {
                        if (!settingsToggle.is(event.target) && !settingsDropdown.is(event.target) && settingsDropdown.is(':visible')) {
                            settingsDropdown.hide();
                        }
                    });
                } else {
                    console.error("لم يتم العثور على العنصر '#settings-toggle' أو '#settings-dropdown'!");
                }
            }

            // تهيئة زر تغيير كلمة المرور
            function setupChangePasswordButton() {
                const changePasswordBtn = $('#change-password-btn');
                const passwordPopup = $('#password-popup');
                const passwordForm = $('#password-form');

                if (elementExists('#change-password-btn') && elementExists('#password-popup')) {
                    changePasswordBtn.on('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        console.log("تم النقر على change-password-btn");
                        passwordPopup.removeClass('hidden');
                        $('#settings-dropdown').hide();
                        if (passwordForm) {
                            passwordForm.show();
                        }
                    });
                } else {
                    console.error("لم يتم العثور على العنصر '#change-password-btn' أو '#password-popup'!");
                }
            }

            // تهيئة إظهار/إخفاء كلمة المرور (هذا الجزء تم استبداله)
            let togglePasswordSetupDone = false;
            function setupTogglePassword() {
                if (togglePasswordSetupDone) {
                    console.log("setupTogglePassword() تم استدعاؤها بالفعل، سيتم التخطي.");
                    return;
                }
                togglePasswordSetupDone = true;

                const togglePasswordButtons = $('.toggle-password');
                console.log("تم العثور على عناصر .toggle-password:", togglePasswordButtons.length);

                if (togglePasswordButtons.length > 0) {
                    togglePasswordButtons.each(function() {
                        const button = $(this);
                        const target = button.data('target');
                        const input = $('#' + target);

                        console.log(`تهيئة زر تبديل لكلمة المرور لـ ${target}`);

                        button.on('click', function() {
                            console.log(`تم النقر على زر تبديل، data-target: ${target}`);
                            if (input.length) {
                                const type = input.attr('type') === 'password' ? 'text' : 'password';
                                console.log(`النوع الحالي: ${input.attr('type')}, النوع الجديد: ${type}`);
                                input.attr('type', type);
                                button.toggleClass('fa-eye fa-eye-slash');
                            } else {
                                console.error(`لم يتم العثور على حقل الإدخال المرتبط بـ ${target}`);
                            }
                        });
                    });
                } else {
                    console.error("لم يتم العثور على أي عناصر بالصنف '.toggle-password'!");
                }
            }

            // تهيئة وظائف أخرى
            function setupOtherFunctions() {
                window.validatePassword = function() {
                    console.log("دالة validatePassword() تم استدعاؤها");
                };

                window.checkPasswordMatch = function() {
                    console.log("دالة checkPasswordMatch() تم استدعاؤها");
                };
            }

            // تهيئة جميع الوظائف
            setupSettingsToggle();
            setupChangePasswordButton();
            // setupTogglePassword(); // تم استبدالها
            setupOtherFunctions();

            // وظائف إضافية (بوب أب)
            window.closepasswordpopup = function() {
                $('#password-popup').addClass('hidden');
            };

            window.closePopup = function() {
                $('#editPopup').addClass('hidden');
            };

            window.openPopup = function() {
                $('#editPopup').removeClass('hidden');
                // resetValidationStyles();
            };

            $('#open-form').click(function(event) {
                event.preventDefault();
                $('#form-popup').removeClass('hidden');
            });

            // إغلاق بوب أب إضافة فرد جديد
            $('#closse-popup').click(function() {
                $('#form-popup').addClass('hidden');
            });

            // إضافة فرد جديد إلى قاعدة البيانات
            $('#add-person-btn').click(function() {
                const id_num = $('#id_numf').val().trim();
                const first_name = $('#first_namef').val().trim();
                const father_name = $('#father_namef').val().trim();
                const grandfather_name = $('#grandfather_namef').val().trim();
                const family_name = $('#family_namef').val().trim();
                const dob = $('#dobf').val().trim();
                const relationship = $('#relationshipf').val().trim();
                const has_condition = $('#has_conditionf').val();
                const condition_description = has_condition == '1' ? $('#condition_descriptionf').val().trim() : null;

                // التعديل الجديد: التحقق من رقم الجوال للزوجة
                const isWifeMember = isWife(relationship);
                const phone = isWifeMember ? $('#phone').val().trim() : null;

                // التحقق من الحقول المطلوبة
                if (!id_num || !first_name || !father_name || !grandfather_name || !family_name || !dob || !relationship) {
                    showAlert('يرجى ملء جميع الحقول المطلوبة!', 'error');
                    return;
                }

                if (isWifeMember) {
                    if (!phone) {
                        showAlert('يرجى إدخال رقم جوال للزوجة!', 'error');
                        return;
                    }
                    if (!validatePhone('phone')) {
                        showAlert('يرجى إدخال رقم جوال صحيح للزوجة!', 'error');
                        return;
                    }
                }

                // إرسال البيانات إلى السيرفر مع دعم عرض الأخطاء المفصلة
                $.ajax({
                    url: '{{ route("persons.addFamily") }}',
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id_num: id_num,
                        first_name: first_name,
                        father_name: father_name,
                        grandfather_name: grandfather_name,
                        family_name: family_name,
                        dob: dob,
                        relationship: relationship,
                        has_condition: has_condition,
                        condition_description: condition_description,
                        // التعديل الجديد: إضافة رقم الجوال
                        phone: phone
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('تمت إضافة الفرد بنجاح', 'success');
                            // تفريغ الحقول بعد الإضافة
                            $('#form-popup').find('input, select, textarea').val('');
                            $('#form-popup').hide();

                            // تحديث الصفحة بعد 1.5 ثانية
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            // التعامل مع الأخطاء المفصلة
                            if (response.rejected_id && response.reason) {
                                showAlert(
                                    `رقم الهوية المرفوض: <strong>${response.rejected_id}</strong><br>` +
                                    `سبب الرفض: <strong>${response.reason}</strong>`,
                                    'error'
                                );
                            } else {
                                showAlert(response.message || 'حدث خطأ أثناء الإضافة!', 'error');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ خطأ في الإرسال:', xhr.responseText);

                        let response = xhr.responseJSON || {};

                        // معالجة رسائل الرفض من قائمة الحظر
                        if (response.rejected_id && response.reason) {
                            showAlert(
                                `رقم الهوية المرفوض: <strong>${response.rejected_id}</strong><br>` +
                                `سبب الرفض: <strong>${response.reason}</strong>`,
                                'error'
                            );
                        }
                        // معالجة أخطاء التحقق من البيانات (Validation Errors)
                        else if (response.errors) {
                            let errorMessages = [];
                            for (let field in response.errors) {
                                errorMessages.push(response.errors[field].join('<br>'));
                            }
                            showAlert(
                                '<strong>أخطاء في البيانات:</strong><br>' + errorMessages.join('<br>'),
                                'error'
                            );
                        }
                        // معالجة رسالة الخطأ العامة
                        else if (response.message) {
                            showAlert(response.message, 'error');
                        }
                        // رسالة افتراضية في حالة عدم وجود تفاصيل
                        else {
                            showAlert('حدث خطأ أثناء الإرسال! يرجى المحاولة مرة أخرى.', 'error');
                        }
                    }
                });
            });


            // دالة تأكيد الحذف
            window.confirmDelete = function(id) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لا يمكنك التراجع عن هذا الإجراء!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، حذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إرسال طلب AJAX لحذف العنصر
                        $.ajax({
                            url: '/person/' + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // أخذ توكن CSRF من الـ meta tag
                            },
                            success: function(response) {
                                Swal.fire(
                                    'تم الحذف!',
                                    'تم حذف الفرد بنجاح.',
                                    'success'
                                );
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'خطأ!',
                                    'يرجة تعديل الحالة الاجتماعية لتتمكن من القيام بعملية الحذف',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }
        });

        // تطبيق خوارزمية Luhn للتحقق من صحة الرقم
        function luhnCheckid(num) {
            const digits = num.split('').map(Number);
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

        // التحقق من رقم الهوية أثناء الكتابة
        function validateIdOnInputid() {
            const idNum = document.getElementById('id_numf').value;
            const errorMessage = document.getElementById('id_numf_error');
            const successMessage = document.getElementById('id_numf_success');
            const inputField = document.getElementById('id_numf');

            // منع المستخدم من إدخال أكثر من 9 أرقام
            if (idNum.length > 9) {
                document.getElementById('id_numf').value = idNum.slice(0, 9);  // اقتصاص الأرقام الزائدة
            }

            // التحقق إذا كان الرقم غير صالح أو صحيح
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                inputField.style.borderColor = '#ff0000';  // جعل الحافة حمراء
                inputField.style.outlineColor = '#ff0000';  // تحديد اللون الأحمر للـ outline
                errorMessage.style.display = 'inline';  // عرض رسالة الخطأ
                successMessage.style.display = 'none';  // إخفاء رسالة النجاح
            } else if (idNum.length === 9 && luhnCheck(idNum)) {
                inputField.style.borderColor = '#35b735';  // جعل الحافة خضراء
                inputField.style.outlineColor = '#35b735';  // تحديد اللون الأخضر للـ outline
                errorMessage.style.display = 'none';  // إخفاء رسالة الخطأ
                successMessage.style.display = 'inline';  // عرض رسالة النجاح
            } else {
                inputField.style.borderColor = '';  // إعادة تعيين اللون إذا لم يكتمل الإدخال
                inputField.style.outlineColor = '';  // إعادة تعيين اللون إذا لم يكتمل الإدخال
                errorMessage.style.display = 'none';  // إخفاء رسالة الخطأ
                successMessage.style.display = 'none';  // إخفاء رسالة النجاح
            }
        }

        // التحقق من رقم الهوية عند إرسال النموذج
        function validateIdNumber() {
            const idNum = document.getElementById('id_numf').value;
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                Swal.fire({
                    icon: 'error',
                    title: 'رقم الهوية غير صالح',
                    text: 'الرجاء التأكد من إدخال رقم هوية صحيح.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'إغلاق'
                });
                return false; // منع إرسال النموذج
            }
            return true; // السماح بإرسال النموذج
        }

        // تفعيل وإلغاء تفعيل رؤية كلمات المرور
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                let passInput = document.getElementById(this.getAttribute('data-target'));
                if (passInput.type === "password") {
                    passInput.type = "text";
                    this.classList.replace("fa-eye", "fa-eye-slash");
                } else {
                    passInput.type = "password";
                    this.classList.replace("fa-eye-slash", "fa-eye");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.getElementById("new-password");
            const confirmPasswordField = document.getElementById("confirm-password");

            // عناصر الشروط
            const lengthCheck = document.getElementById("length-check");
            const uppercaseCheck = document.getElementById("uppercase-check");
            const lowercaseCheck = document.getElementById("lowercase-check");
            const numberCheck = document.getElementById("number-check");
            const symbolCheck = document.getElementById("symbol-check");
            const matchCheck = document.getElementById("match-check");

            // تحديث التحقق مع الكتابة
            passwordField.addEventListener("input", validatePassword);
            confirmPasswordField.addEventListener("input", checkPasswordMatch);

            function validatePassword() {
                const password = passwordField.value;

                checkCondition(password.length >= 9 && password.length <= 15, lengthCheck);
                checkCondition(/[A-Z]/.test(password), uppercaseCheck);
                checkCondition(/[a-z]/.test(password), lowercaseCheck);
                checkCondition(/[0-9]/.test(password), numberCheck);
                checkCondition(/[\W_]/.test(password), symbolCheck);

                // التحقق من تطابق كلمة المرور
                checkPasswordMatch();
            }

            function checkPasswordMatch() {
                const password = passwordField.value;
                const confirmPassword = confirmPasswordField.value;
                const isMatch = password === confirmPassword && confirmPassword.length > 0;

                checkCondition(isMatch, matchCheck);
            }

            function checkCondition(condition, element) {
                const icon = element.querySelector("i");

                if (condition) {
                    element.classList.add("valid");
                    element.classList.remove("invalid");
                    icon.style.display = "inline"; // إظهار علامة الصح
                    icon.textContent = "✔";
                } else {
                    element.classList.add("invalid");
                    element.classList.remove("valid");
                    icon.style.display = "inline"; // إظهار علامة الخطأ فقط عند المخالفة
                    icon.textContent = "✖";
                }
            }

            // منع الإرسال إذا لم تتحقق جميع الشروط
            document.querySelector("form").addEventListener("submit", function (event) {
                if (
                    !lengthCheck.classList.contains("valid") ||
                    !uppercaseCheck.classList.contains("valid") ||
                    !lowercaseCheck.classList.contains("valid") ||
                    !numberCheck.classList.contains("valid") ||
                    !symbolCheck.classList.contains("valid") ||
                    !matchCheck.classList.contains("valid")
                ) {
                    event.preventDefault();
                    alert("يرجى التأكد من أن جميع متطلبات كلمة المرور مستوفاة قبل الإرسال.");
                }
            });
        });

        // التحقق من صحة كلمة المرور القديمة عند إرسال الفورم
        let password_form = document.getElementById('password-form')
        if(password_form) {
            password_form.addEventListener('submit', function (event) {
                const oldPassword = document.getElementById('old-password').value;
                const newPassword = document.getElementById('new-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    document.getElementById('password-error').textContent = 'كلمة المرور الجديدة غير متطابقة';
                    document.getElementById('password-error').classList.remove('hidden');
                }
            });
        }
        // تطبيق خوارزمية Luhn للتحقق من صحة الرقم
        function luhnCheck(num) {
            const digits = num.toString().split('').map(Number);
            let checksum = 0;
            const numDigits = digits.length;
            const parity = numDigits % 2;

            for (let i = 0; i < numDigits; i++) {
                let digit = digits[i];
                if (i % 2 === parity) {
                    digit *= 2;
                    if (digit > 9) {
                        digit -= 9;
                    }
                }
                checksum += digit;
            }

            return checksum % 10 === 0;
        }

        // التحقق من رقم الهوية أثناء الكتابة
        function validateIdOnInput() {
            const idNum = document.getElementById('edit_id_num').value;
            const errorMessage = document.getElementById('edit_id_num_error');
            const successMessage = document.getElementById('edit_id_num_success');
            const inputField = document.getElementById('edit_id_num');

            // منع المستخدم من إدخال أكثر من 9 أرقام
            if (idNum.length > 9) {
                document.getElementById('edit_id_num').value = idNum.slice(0, 9);  // اقتصاص الأرقام الزائدة
            }

            // التحقق إذا كان الرقم غير صالح أو صحيح
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                inputField.style.borderColor = '#ff0000';  // جعل الحافة حمراء
                inputField.style.outlineColor = '#ff0000';  // تحديد اللون الأحمر للـ outline
                errorMessage.style.display = 'inline';  // عرض رسالة الخطأ
                successMessage.style.display = 'none';  // إخفاء رسالة النجاح
            } else if (idNum.length === 9 && luhnCheck(idNum)) {
                inputField.style.borderColor = '#35b735';  // جعل الحافة خضراء
                inputField.style.outlineColor = '#35b735';  // تحديد اللون الأخضر للـ outline
                errorMessage.style.display = 'none';  // إخفاء رسالة الخطأ
                successMessage.style.display = 'inline';  // عرض رسالة النجاح
            } else {
                inputField.style.borderColor = '';  // إعادة تعيين اللون إذا لم يكتمل الإدخال
                inputField.style.outlineColor = '';  // إعادة تعيين اللون إذا لم يكتمل الإدخال
                errorMessage.style.display = 'none';  // إخفاء رسالة الخطأ
                successMessage.style.display = 'none';  // إخفاء رسالة النجاح
            }
        }

        // التحقق من رقم الهوية عند إرسال النموذج
        function validateIdNumber() {
            const idNum = document.getElementById('id_num').value;

            // إذا كان الرقم غير صالح
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                Swal.fire({
                    icon: 'error',
                    title: 'رقم الهوية غير صالح',
                    text: 'الرجاء التأكد من إدخال رقم هوية صحيح.',
                    background: '#fff',
                    confirmButtonColor: '#d33',
                    iconColor: '#d33',
                    confirmButtonText: 'إغلاق',  // النص الخاص بالزر
                    customClass: {
                        confirmButton: 'swal-button-custom'
                    }
                });
                return false;  // منع إرسال النموذج
            }
            return true;  // السماح بإرسال النموذج إذا كان الرقم صالح
        }

        function openPopup() {
            document.getElementById("editPopup").classList.remove("hidden");

            let current_city = "{{ $person->current_city }}"
            let neighborhood = "{{ $person->neighborhood }}"

            // تحديث قائمة الأحياء بناءً على المحافظة وتحديد الحي المخزن
            updateNeighborhoods(current_city, neighborhood);
        }

        function closeFamilyPopup() {
            document.getElementById("editFamilyMemberModal").classList.add("hidden");
        }

        function toggleConditionDescription() {
            var checkBox = document.getElementById("has_condition");
            var descriptionRow = document.getElementById("condition_description_group");


            if (checkBox.checked) {
                descriptionRow.classList.remove("hidden");
            } else {
                descriptionRow.classList.add("hidden");
            }
        }

        function showAlert(message, type) {
            let iconType, iconColor, confirmButtonColor;

            switch (type) {
                case 'success':
                    iconType = 'success';       // أيقونة صح
                    iconColor = '#28a745';      // أخضر
                    confirmButtonColor = '#28a745';
                    break;
                case 'warning':
                    iconType = 'warning';       // علامة تعجب
                    iconColor = '#fd7e14';      // برتقالي
                    confirmButtonColor = '#fd7e14';
                    break;
                case 'error':
                default:
                    iconType = 'error';         // خطأ
                    iconColor = '#dc3545';      // أحمر
                    confirmButtonColor = '#dc3545';
                    break;
            }

            Swal.fire({
                html: message,
                icon: iconType,
                iconColor: iconColor,
                background: '#fff',
                color: '#000',
                confirmButtonColor: confirmButtonColor,
                confirmButtonText: 'حسناً'
            });
        }

        function saveChangesParent() {
            console.log("✅ الدالة saveChangesParent تعمل!");

            let neighborhoodValue = document.getElementById('edit_neighborhood').value.trim();
            let areaResponsibleInput = document.getElementById('edit_area_responsible_id');
            let rawValue = areaResponsibleInput ? areaResponsibleInput.value.trim() : '';
            let originalIdNumValue = document.getElementById('edit_id_num').getAttribute('data-original') || '';
            console.log('originalIdNumValue:', originalIdNumValue);

            let formData = {
                old_id_num: originalIdNumValue,
                id_num: document.getElementById('edit_id_num').value.trim(),
                first_name: document.getElementById('edit_first_name').value.trim(),
                father_name: document.getElementById('edit_father_name').value.trim(),
                grandfather_name: document.getElementById('edit_grandfather_name').value.trim(),
                family_name: document.getElementById('edit_family_name').value.trim(),
                dob: document.getElementById('edit_dob').value.trim(),
                gender: document.getElementById('edit_gender').value.trim(),
                phone: document.getElementById('edit_phone').value.trim(),
                social_status: document.getElementById('edit_social_status').value.trim(),
                relatives_count: document.getElementById('edit_relatives_count') ? document.getElementById('edit_relatives_count').value.trim() : '0',
                employment_status: document.getElementById('edit_employment_status').value.trim(),
                has_condition: document.getElementById('edit_has_condition').value.trim(),
                condition_description: document.getElementById('edit_condition_description').value.trim(),
                city: document.getElementById('edit_city').value.trim(),
                housing_damage_status: document.getElementById('edit_housing_damage_status').value.trim(),
                current_city: document.getElementById('edit_current_city').value.trim(),
                housing_type: document.getElementById('edit_housing_type').value.trim(),
                neighborhood: neighborhoodValue,
                area_responsible_id: rawValue === '' ? null : rawValue,
                landmark: document.getElementById('edit_landmark').value.trim()
            };

            console.log('formData to send:', formData);

            // التحقق من الحقول المطلوبة
            for (let key in formData) {
                if ((key !== 'condition_description' && key !== 'area_responsible_id' && key !== 'phone') && (!formData[key])) {
                    showAlert(`يرجى ملء جميع الحقول المطلوبة (${key})`, 'warning');
                    return;
                }
            }

            fetch('/update-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject({ status: response.status, data: data });
                    }
                    return data;
                });
            })
            .then(data => {
                console.log("📌 استجابة السيرفر:", data);
                if (data.success) {
                    showAlert('تم تحديث الملف الشخصي بنجاح!', 'success', () => {
                        closePopup();
                        location.reload();
                    });
                } else if (data.rejected_id && data.reason) {
                    showAlert(
                        `رقم الهوية المرفوض: <strong>${data.rejected_id}</strong><br>` +
                        `سبب الرفض: <strong>${data.reason}</strong>`,
                        'error'
                    );
                } else {
                    showAlert(data.message || 'حدث خطأ أثناء التحديث', 'error');
                }
            })
            .catch(error => {
                if (error.data) {
                    if (error.data.rejected_id && error.data.reason) {
                        showAlert(
                            `رقم الهوية المرفوض: <strong>${error.data.rejected_id}</strong><br>` +
                            `سبب الرفض: <strong>${error.data.reason}</strong>`,
                            'error'
                        );
                    } else {
                        showAlert(error.data.message || 'حدث خطأ أثناء التحديث', 'error');
                    }
                } else {
                    console.error("❌ خطأ في جلب البيانات:", error);
                    showAlert('[translate:تعذر الاتصال بالخادم، يرجى التحقق من الاتصال بالإنترنت.]', 'error');
                }
            });
        }

        function saveChangesChild() {
            console.log("✅ الدالة saveChangesChild تعمل!");

            let familyMemberId = document.getElementById('familyMemberId');
            if (!familyMemberId) {
                showAlert('[translate:خطأ: العنصر familyMemberId غير موجود]', 'error');
                return;
            }

            let hasConditionElement = document.getElementById('edit_f_has_condition');
            let conditionDescriptionElement = document.getElementById('edit_f_condition_description');

            let newIdNum = document.getElementById('edit_f_id_num')?.value.trim() || "";
            let originalIdNumValue = document.getElementById('edit_f_id_num').getAttribute('data-original') || "";
            originalIdNumValue = originalIdNumValue.trim();

            let formData = {
                id_num: newIdNum,
                id: familyMemberId.value.trim(),
                first_name: document.getElementById('edit_f_first_name')?.value.trim() || "",
                father_name: document.getElementById('edit_f_father_name')?.value.trim() || "",
                grandfather_name: document.getElementById('edit_f_grandfather_name')?.value.trim() || "",
                family_name: document.getElementById('edit_f_family_name')?.value.trim() || "",
                dob: document.getElementById('edit_f_dob')?.value.trim() || "",
                relationship: document.getElementById('edit_f_relationship')?.value.trim() || "",
                has_condition: hasConditionElement?.value.trim() || "",
                condition_description: conditionDescriptionElement?.value.trim() || "",
                // التعديل الجديد: إضافة رقم الجوال
                phone: document.getElementById('edit_f_phone')?.value.trim() || ""
            };

            // أرسل old_id_num فقط إذا اختلف عن الرقم الجديد وباي قيمة صالحة
            if (originalIdNumValue && originalIdNumValue !== newIdNum) {
                formData.old_id_num = originalIdNumValue;
            }

            // معالجة حالة لا يوجد حالة صحية
            if (formData.has_condition === "لا" || formData.has_condition === "0") {
                formData.has_condition = 0;
                formData.condition_description = null;
                if (conditionDescriptionElement) {
                    conditionDescriptionElement.value = "";
                }
            }

            // التعديل الجديد: التحقق من صحة رقم الجوال إذا كانت العلاقة "زوجة"
            if (formData.relationship === 'wife') {
                if (!formData.phone) {
                    showAlert('يرجى إدخال رقم جوال للزوجة', 'error');
                    return;
                }
                if (!validatePhone('edit_f_phone')) {
                    showAlert('يرجى إدخال رقم جوال صحيح للزوجة', 'error');
                    return;
                }
            }

            console.log("📌 البيانات المُرسلة:", formData);

            fetch('/update-family-member', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject({ status: response.status, data: data });
                    }
                    return data;
                });
            })
            .then(data => {
                console.log("📌 استجابة السيرفر:", data);
                if (data.success) {
                    showAlert('[translate:تم تحديث فرد الأسرة بنجاح!]', 'success', () => {
                        closeFamilyPopup();
                        location.reload();
                    });
                } else if (data.rejected_id && data.reason) {
                    showAlert(
                        `[translate:رقم الهوية المرفوض:] <strong>${data.rejected_id}</strong><br>` +
                        `[translate:سبب الرفض:] <strong>${data.reason}</strong>`,
                        'error'
                    );
                } else {
                    showAlert(data.message || '[translate:حدث خطأ أثناء التحديث]', 'error');
                }
            })
            .catch(error => {
                if (error.data) {
                    if (error.data.rejected_id && error.data.reason) {
                        showAlert(
                            `[translate:رقم الهوية المرفوض:] <strong>${error.data.rejected_id}</strong><br>` +
                            `[translate:سبب الرفض:] <strong>${error.data.reason}</strong>`,
                            'error'
                        );
                    } else {
                        showAlert(error.data.message || '[translate:حدث خطأ أثناء التحديث]', 'error');
                    }
                } else {
                    console.error("❌ خطأ في جلب البيانات:", error);
                    showAlert('[translate:تعذر الاتصال بالخادم، يرجى التحقق من الاتصال بالإنترنت.]', 'error');
                }
            });
        }

        // دالة موحدة محسّنة للتنبيهات
        function showAlert(message, type = 'info', callback = null) {
            const config = {
                error: { icon: 'error', title: 'خطأ!', confirmButtonColor: '#d33' },
                warning: { icon: 'warning', title: 'تحذير!', confirmButtonColor: '#ffc107' },
                success: { icon: 'success', title: 'نجح!', confirmButtonColor: '#28a745' },
                info: { icon: 'info', title: 'معلومات', confirmButtonColor: '#17a2b8' }
            };

            Swal.fire({
                ...config[type],
                html: message, // دعم HTML للرسائل المفصلة
                confirmButtonText: 'إغلاق'
            }).then((result) => {
                if (callback && result.isConfirmed) {
                    callback();
                }
            });
        }

        function saveChangesPassword() {
            // جمع القيم من الحقول
            let formData = {
                passkey: document.getElementById('old-password').value.trim(),
                new_passkey: document.getElementById('new-password').value.trim(),
                confirm_passkey: document.getElementById('confirm-password').value.trim(),
                id_num: document.getElementById('id_num').value.trim(),
            };

            // التحقق من ملء جميع الحقول المطلوبة
            for (let key in formData) {
                if (!formData[key]) {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: "يرجى ملء جميع الحقول المطلوبة.",
                        icon: 'warning',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }
            }

            // التحقق من تطابق كلمة المرور الجديدة مع التأكيد
            if (formData.new_passkey !== formData.confirm_passkey) {
                Swal.fire({
                    title: 'خطأ!',
                    text: "كلمة المرور الجديدة وتأكيد كلمة المرور غير متطابقين.",
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            // جلب CSRF Token من الميتا
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // إرسال البيانات إلى السيرفر عبر fetch
            fetch('/update-passkey', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // ✅ حماية Laravel عبر CSRF Token
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم التحديث بنجاح!',
                        text: 'تم تحديث كلمة المرور الخاصة بك.',
                        icon: 'success',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        closepasswordpopup(); // ✅ إغلاق النافذة المنبثقة
                        location.reload(); // ✅ تحديث الصفحة لرؤية التغييرات
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء التحديث. الرجاء المحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(error => {
                console.error('❌ خطأ:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ غير متوقع. الرجاء المحاولة لاحقاً.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'حسناً'
                });
            });
        }

        function showError(message) {
            let errorDiv = document.getElementById('password-error');
            errorDiv.innerText = message;
            errorDiv.style.display = 'block';
        }

        function validateArabicInput(inputId) {
            const inputField = document.getElementById(inputId);
            const errorMessage = document.getElementById(`${inputId}_error`);
            const value = inputField.value.trim(); // إزالة المسافات الزائدة
            const arabicRegex = /^[\u0621-\u064A\s]+$/; // تطابق الحروف العربية فقط مع المسافات
            //

            if (value === '') {
                // إذا كان الحقل فارغًا
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                inputField.style.borderColor = 'red';
            } else if (/[\d!@#$%^&*(),.?":{}|<>0-9]/.test(value)) {
                // إذا أدخل المستخدم أرقامًا أو رموزًا
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'غير مسموح بإدخال الأرقام والرموز.';
                inputField.style.borderColor = 'red';
            } else if (!arabicRegex.test(value)) {
                // إذا أدخل المستخدم نصًا بلغة غير العربية
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'لغة الكتابة المسموح بها العربية فقط.';
                inputField.style.borderColor = 'red';
            } else {
                // إذا كان النص صحيحًا
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function validateGender() {
            const inputField = document.getElementById("gender");
            const errorMessage = document.getElementById("gender_error");
            const value = inputField.value;

            if (!value) {
                // إذا كان الحقل فارغًا
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'يرجى اختيار الجنس.';
                inputField.style.borderColor = 'red';
            }

            else if (value === "غير محدد") {
                // إذا اختار "غير محدد"
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'لا يمكنك اختيار "غير محدد".';
                inputField.style.borderColor = 'red';
            }

            else {
            // إذا كان الاختيار صحيحًا
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            inputField.style.borderColor = ''; // إزالة لون الإطار
            return true;
            }
        }

        function validatedob() {
            const inputField = document.getElementById("edit_dob");
            const errorMessage = document.getElementById("edit_dob_error");
            const value = inputField.value.trim();
            //

            if (!value) {
                // إذا كان الحقل فارغًا
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                inputField.style.borderColor = 'red';
                return;
            }

            const birthDate = new Date(value);
            const today = new Date();
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 100); // الحد الأدنى للعمر: 100 سنة

            if (birthDate > today) {
                // إذا كان تاريخ الميلاد في المستقبل
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'تاريخ الميلاد لا يمكن أن يكون في المستقبل.';
                inputField.style.borderColor = 'red';
            }else {
                // إذا كان تاريخ الميلاد صحيحًا
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function validatedobf() {
            const inputField = document.getElementById("dobf");
            const errorMessage = document.getElementById("dobf_error");
            const value = inputField.value.trim();

            if (!value) {
                // إذا كان الحقل فارغًا
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                inputField.style.borderColor = 'red';
                return;
            }

            const birthDate = new Date(value);
            const today = new Date();
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 100); // الحد الأدنى للعمر: 100 سنة

            if (birthDate > today) {
                // إذا كان تاريخ الميلاد في المستقبل
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'تاريخ الميلاد لا يمكن أن يكون في المستقبل.';
                inputField.style.borderColor = 'red';
            }else {
                // إذا كان تاريخ الميلاد صحيحًا
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                inputField.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function validatePhoneInput() {
            const phoneInput = document.getElementById('edit_phone');
            const errorMessage = document.getElementById('edit_phone_error');
            let value = phoneInput.value.trim();

            // إزالة العلامات "-" من الرقم
            const cleanValue = value.replace(/-/g, '');

            // نمط التحقق: يجب أن يبدأ بـ 059 أو 056 ويحتوي على 10 أرقام
            const phoneRegex = /^(059|056)\d{7}$/;

            // إذا كان الحقل فارغًا
            if (cleanValue === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                phoneInput.style.borderColor = 'red';
            }
            // إذا كان الرقم غير صالح
            else if (!phoneRegex.test(cleanValue)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'الرجاء إدخال رقم جوال صحيح يبدأ بـ 059 أو 056 ويتكون من 10 أرقام.';
                phoneInput.style.borderColor = 'red';
            }
            // إذا كان الرقم صالحًا
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                phoneInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }

            // تنسيق الرقم إلى الشكل 059-123-1234 أثناء الكتابة
            let formattedValue = cleanValue;
            if (cleanValue.length > 3) {
                formattedValue = cleanValue.slice(0, 3) + '-' + cleanValue.slice(3);
            }
            if (cleanValue.length > 7) {
                formattedValue = formattedValue.slice(0, 7) + '-' + formattedValue.slice(7);
            }

            // تحديد الحد الأقصى لطول الرقم (12 حرفًا مع الشرطات)
            if (formattedValue.length > 12) {
                formattedValue = formattedValue.slice(0, 12);
            }

            phoneInput.value = formattedValue;
        }

        document.getElementById('form').addEventListener('submit', function (e) {
            e.preventDefault();

            // إعادة تحقق من الرقم بعد تنسيقه
            if (!phoneRegex.test(phoneInput.value)) {
                alert('الرجاء إدخال رقم جوال صحيح');
                return;
            }

            // remove "-" from phone number
            phoneInput.value = phoneInput.value.replace(/-/g, '');


            this.submit();

        });

        function validateSocialStatus() {
            const socialStatusInput = document.getElementById('edit_social_status');
            const errorMessage = document.getElementById('edit_social_status_error');
            const value = socialStatusInput.value.trim();


            // نمط التحقق: التأكد من اختيار قيمة من القيم المعتمدة
            const validValues = @json(\App\Enums\Person\PersonSocialStatus::toValues()); // جلب القيم المسموحة من الخادم

            // إذا كان الحقل فارغًا
            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                socialStatusInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة غير صالحة
            else if (!validValues.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                socialStatusInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة صالحة
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                socialStatusInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function validateEmploymentStatus() {
            const employmentStatusInput = document.getElementById('edit_employment_status');
            const errorMessage = document.getElementById('edit_employment_status_error');
            const value = employmentStatusInput.value.trim();


            // القيم المسموحة لحالة العمل
            const validValues = ['موظف', 'عامل', 'لا يعمل'];

            // إذا كان الحقل فارغًا
            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                employmentStatusInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة غير صالحة
            else if (!validValues.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                employmentStatusInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة صالحة
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                employmentStatusInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function toggleConditionDescription() {
            const hasCondition = document.getElementById("edit_has_condition").value;
            const conditionDescriptionGroup = document.getElementById("edit_condition_description_group");


            if (hasCondition === "1") {
                conditionDescriptionGroup.style.display = "block";
            } else {
                conditionDescriptionGroup.style.display = "none";
                document.getElementById("edit_condition_description").value = ""; // تفريغ الحقل إذا تم إخفاؤه
                resetBorderAndError('edit_condition_description');
            }
        }

        function toggleConditionText() {
            const hasCondition = document.getElementById("edit_f_has_condition").value;
            const conditionDescriptionGroup = document.getElementById("edit_f_condition_description_group");


            if (hasCondition === "1") {
                conditionDescriptionGroup.style.display = "block";
            } else {
                conditionDescriptionGroup.style.display = "none";
                document.getElementById("edit_f_condition_description").value = ""; // تفريغ الحقل إذا تم إخفاؤه
                resetBorderAndError('edit_f_condition_description');
            }
        }

        function validateConditionText() {
            const inputField = document.getElementById("edit_condition_description");
            const errorMessage = document.getElementById("edit_condition_description_error");
            const value = inputField.value.trim();
            const hasCondition = document.getElementById("edit_has_condition").value;


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

        function validateConditionTextf() {
            const inputField = document.getElementById("condition_descriptionf");
            const errorMessage = document.getElementById("condition_descriptionf_error");
            const value = inputField.value.trim();
            const hasCondition = document.getElementById("condition_descriptionf").value;


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
            const cityInput = document.getElementById('edit_city');
            const errorMessage = document.getElementById('edit_city_error');
            const value = cityInput.value.trim();


            // جلب القيم المسموحة من الخادم
            const validValues = @json(\App\Enums\Person\PersonCity::toValues());

            // إذا كان الحقل فارغًا
            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                cityInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة غير صالحة
            else if (!validValues.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                cityInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة صالحة
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                cityInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function populateNeighborhoodSelect(neighborhoods, neighborhoodSelect) {
            neighborhoods.forEach(neighborhood => {
                const option = document.createElement("option");
                option.value = neighborhood.value;
                option.textContent = neighborhood.label;
                neighborhoodSelect.appendChild(option);
            });
        }

        function updateNeighborhoods(selectedCity, selectedNeighborhood = null, originalCity) {
            const neighborhoodSelect = document.getElementById("edit_neighborhood");
            neighborhoodSelect.innerHTML = '<option value="">اختر الحي السكني الحالي</option>';

            const cityNeighborhoods = {
                "rafah": [
                    { value: "masbah", label: "مصبح" },
                    { value: "khirbetAlAdas", label: "خربة العدس" },
                    { value: "alJaninehNeighborhood", label: "حي الجنينة" },
                    { value: "alAwda", label: "العودة" },
                    { value: "alZohourNeighborhood", label: "حي الزهور" },
                    { value: "brazilianHousing", label: "الإسكان البرازيلي" },
                    { value: "telAlSultan", label: "تل السلطان" },
                    { value: "alShabouraNeighborhood", label: "حي الشابورة" },
                    { value: "rafahProject", label: "مشروع رفح" },
                    { value: "zararRoundabout", label: "دوار زعرب" }
                ],
                "khanYounis": [
                    { value: "qizanAlNajjar", label: "قيزان النجار" },
                    { value: "qizanAbuRashwan", label: "قيزان أبو رشوان" },
                    { value: "juraAlLoot", label: "جورة اللوت" },
                    { value: "sheikhNasser", label: "الشيخ ناصر" },
                    { value: "maAn", label: "معن" },
                    { value: "alManaraNeighborhood", label: "حي المنارة" },
                    { value: "easternLine", label: "السطر الشرقي" },
                    { value: "westernLine", label: "السطر الغربي" },
                    { value: "alMahatta", label: "المحطة" },
                    { value: "alKatiba", label: "الكتيبة" },
                    { value: "alBatanAlSameen", label: "البطن السمين" },
                    { value: "alMaskar", label: "المعسكر" },
                    { value: "alMashroo", label: "المشروع" },
                    { value: "hamidCity", label: "مدينة حمد" },
                    { value: "alMawasi", label: "المواصي" },
                    { value: "alQarara", label: "القرارة" },
                    { value: "eastKhanYounis", label: "شرق خانيونس" },
                    { value: "downtown", label: "وسط البلد" },
                    { value: "mirage", label: "ميراج" },
                    { value: "european", label: "الأوروبي" },
                    { value: "alFakhari", label: "الفخاري" },
                    { value: "alQalaaSouth", label: "القلعة وجنوبها" },
                    { value: "northJalalStreet", label: "شمال شارع جلال" }
                ],
                "northGaza": [
                    { value: "jabalia", label: "جباليا" },
                    { value: "beitLahia", label: "بيت لاهيا" },
                    { value: "beitHanoun", label: "بيت حانون" },
                    { value: "omAlNasr", label: "أم النصر" },
                    { value: "nazla", label: "النزلة" }
                ],
                "alwsta": [
                    { value: "alZahra", label: "الزهراء" },
                    { value: "alMughraqa", label: "المغراقة" },
                    { value: "alBureij", label: "البريج" },
                    { value: "alNuseirat", label: "النصيرات" },
                    { value: "alMaghazi", label: "المغازي" },
                    { value: "alZawaida", label: "الزوايدة" },
                    { value: "deirAlBalah", label: "دير البلح" }
                ],
                "gaza": [
                    { value: "shujaiya", label: "الشجاعية" },
                    { value: "alDaraj", label: "الدرج" },
                    { value: "alTuffah", label: "التفاح" },
                    { value: "alRimal", label: "الرمال" },
                    { value: "alZaytoun", label: "الزيتون" },
                    { value: "alNasr", label: "النصر" },
                    { value: "sheikhRadwan", label: "الشيخ رضوان" },
                    { value: "telAlHawa", label: "تل الهوا" },
                    { value: "sheikhAjleen", label: "الشيخ عجلين" },
                    { value: "alSabra", label: "الصبرة" },
                    { value: "alKaramah", label: "الكرامة" },
                    { value: "birAlNajah", label: "بير النعجة" },
                    { value: "juhrAlDeek", label: "جحر الديك" },
                    { value: "shatiCamp", label: "مخيم الشاطئ" }
                ]
            };

            const neighborhoods = cityNeighborhoods[selectedCity] || [];

            neighborhoods.forEach(function (neighborhood) {
                const option = document.createElement("option");
                option.value = neighborhood.value;
                option.textContent = neighborhood.label;
                neighborhoodSelect.appendChild(option);
            });

            // Preselect the neighborhood if passed and exists
            if (selectedNeighborhood) {
                const optionExists = [...neighborhoodSelect.options].some(opt => opt.value === selectedNeighborhood);
                if (optionExists) {
                    neighborhoodSelect.value = selectedNeighborhood;
                }
            }
        }

        window.onload = function () {
            const currentCitySelect = document.getElementById('edit_current_city');
            const selectedCity = currentCitySelect.value;
            const selectedNeighborhood = '{{ $person->neighborhood }}';
            const originalCity = '{{ $person->current_city }}';

            updateNeighborhoods(selectedCity, selectedNeighborhood, originalCity);

            currentCitySelect.onchange = function () {
                const cityValue = this.value;
                const neighborhoodValue = '{{ $person->neighborhood }}';
                const originalCityValue = '{{ $person->current_city }}';
                updateNeighborhoods(cityValue, neighborhoodValue, originalCityValue);
            };
        };

        function validateCurrentCity() {
            const currentCityInput = document.getElementById('edit_current_city');
            const errorMessage = document.getElementById('edit_current_city_error');
            const value = currentCityInput.value.trim();

            // جلب القيم المسموحة من الخادم
            const validValues = @json(\App\Enums\Person\PersonCurrentCity::toValues());

            // إذا كان الحقل فارغًا
            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                currentCityInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة غير صالحة
            else if (!validValues.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                currentCityInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة صالحة
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                currentCityInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function validateNeighborhood() {
            const neighborhoodInput = document.getElementById('edit_neighborhood');
            const errorMessage = document.getElementById('edit_neighborhood_error');
            const value = neighborhoodInput.value.trim();

            const neighborhoodSelect = document.getElementById('edit_neighborhood');
            const areaResponsibleContainer = document.getElementById('edit_areaResponsibleField');
            const areaResponsibleSelect = document.getElementById('edit_area_responsible_id');

            const visibleNeighborhoods = [
                    'المواصي',
                    'السطر الغربي',
                    'السطر الشرقي',
                    'المحطة',
                    'الكتيبة',
                    'البطن السمين',
                    'المعسكر',
                    'المشروع',
                    'مدينة حمد',
                    'وسط البلد',
                    'القلعة وجنوبها',
                    'شمال شارع جلال'
                ];

            const selectedOption = neighborhoodSelect.options[neighborhoodSelect.selectedIndex].text.trim();

            if (visibleNeighborhoods.includes(selectedOption)) {
                areaResponsibleContainer.style.display = 'block';
            } else {
                areaResponsibleContainer.style.display = 'none';
                areaResponsibleSelect.value = ''; // إعادة تعيين القيمة عندما لا يكون في القائمة
            }

            // جلب القيم المسموحة من الخادم
            const validValues = @json(\App\Enums\Person\PersonNeighborhood::toValues());

            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                neighborhoodInput.style.borderColor = 'red';
            }
            else if (!validValues.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                neighborhoodInput.style.borderColor = 'red';
            }
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                neighborhoodInput.style.borderColor = '';
                return true;
            }
            if (editNeighborhoodSelect) {
                updateAreaResponsibleVisibility();
                editNeighborhoodSelect.addEventListener('change', updateAreaResponsibleVisibility);
            }
        }

        function validateAreaResponsible() {
            const select = document.getElementById('edit_area_responsible_id');
            const errorDiv = document.getElementById('edit_area_responsible_id_error');

            if (select.value.trim() === '') {
                errorDiv.textContent = 'يرجى اختيار مسؤول المنطقة.';
                errorDiv.style.display = 'block';
                select.style.borderColor = 'red';
            } else {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
                select.style.borderColor = '';
            }
        }

        function validateHousingType() {
            const housingTypeInput = document.getElementById('edit_housing_type');
            const errorMessage = document.getElementById('edit_housing_type_error');
            const value = housingTypeInput.value.trim();


            // جلب القيم المسموحة من الخادم
            const validHousingTypes = @json(\App\Enums\Person\PersonHousingType::toValues());

            // إذا كان الحقل فارغًا
            if (value === '') {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'هذا الحقل مطلوب.';
                housingTypeInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة غير صالحة
            else if (!validHousingTypes.includes(value)) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
                housingTypeInput.style.borderColor = 'red';
            }
            // إذا كانت القيمة صالحة
            else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                housingTypeInput.style.borderColor = ''; // إزالة لون الإطار
                return true;
            }
        }

        function resetBorderAndError(inputId) {
            // إعادة تعيين لون الإطار ورسائل الخطأ عند التركيز على الحقل
            const input = document.getElementById(inputId);
            const errorMessage = document.getElementById(`${inputId}_error`);

            input.style.borderColor = ''; // إزالة لون الإطار
            errorMessage.style.display = 'none'; // إخفاء رسالة الخطأ
            errorMessage.textContent = ''; // مسح نص رسالة الخطأ
        }

        // تحديد الزر و النموذج
        const submitButton = document.getElementById('submit-button');
        const form = document.getElementById('form');  // تأكد أن النموذج له ID "form"

        let errorMessages = []; // Move errorMessages outside of validateForm function

        function validateForm() {
            let isValid = true;
            errorMessages = []; // Clear the error messages at the start of validation
            // clearErrors();
            // تحقق من صحة جميع الحقول

            if (!validateArabicInput('edit_first_name')) {
                isValid = false;
                errorMessages.push({ field: 'edit_first_name', message: 'الرجاء إدخال الاسم الأول بشكل صحيح.' });
            }

            if (!validateArabicInput('edit_father_name')) {
                isValid = false;
                errorMessages.push({ field: 'edit_father_name', message: 'الرجاء إدخال اسم الأب بشكل صحيح.' });
            }

            if (!validateArabicInput('edit_grandfather_name')) {
                isValid = false;
                errorMessages.push({ field: 'edit_grandfather_name', message: 'الرجاء إدخال اسم الجد بشكل صحيح.' });
            }

            if (!validateArabicInput('edit_family_name')) {
                isValid = false;
                errorMessages.push({ field: 'edit_family_name', message: 'الرجاء إدخال اسم العائلة بشكل صحيح.' });
            }

            if (!validatedob()) {
                isValid = false;
                errorMessages.push({ field: 'edit_dob', message: 'الرجاء إدخال تاريخ الميلاد بشكل صحيح.' });
            }

            if (!validatePhoneInput()) {
                isValid = false;
                errorMessages.push({ field: 'edit_phone', message: 'الرجاء إدخال رقم الهاتف بشكل صحيح.' });
            }

            if (!validateSocialStatus()) {
                isValid = false;
                errorMessages.push({ field: 'edit_social_status', message: 'الرجاء تحديد الحالة الاجتماعية.' });
            }

            if (!validateEmploymentStatus()) {
                isValid = false;
                errorMessages.push({ field: 'edit_employment_status', message: 'الرجاء تحديد حالة العمل.' });
            }

            if (!validateConditionText()) {
                isValid = false;
                errorMessages.push({ field: 'edit_condition_description', message: 'الرجاء وصف الحالة الصحية التي تعاني منها.' });
            }

            if (!validateCity()) {
                isValid = false;
                errorMessages.push({ field: 'edit_city', message: 'الرجاء إدخال المدينة بشكل صحيح.' });
            }

            if (!validateCurrentCity()) {
                isValid = false;
                errorMessages.push({ field: 'edit_current_city', message: 'الرجاء إدخال المدينة الحالية بشكل صحيح.' });
            }

            if (!validateNeighborhood()) {
                isValid = false;
                errorMessages.push({ field: 'edit_neighborhood', message: 'الرجاء إدخال الحي بشكل صحيح.' });
            }

            if (!validateAreaResponsible()) {
                isValid = false;
                errorMessages.push({ field: 'edit_area_responsible_id', message: 'الرجاء إدخال مسؤول المنطقة في المواصي بشكل صحيح.' });
            }

            if (!validateArabicInput('edit_landmark')) {
                isValid = false;
                errorMessages.push({ field: 'edit_landmark', message: 'الرجاء إدخال المعلم بشكل صحيح.' });
            }

            if (!validateHousingType()) {
                isValid = false;
                errorMessages.push({ field: 'edit_housing_type', message: 'الرجاء تحديد نوع السكن.' });
            }

            if (!validateHousingDamageStatus()) {
                isValid = false;
                errorMessages.push({ field: 'edit_housing_damage_status', message: 'الرجاء تحديد حالة السكن.' });
            }

            // تحقق من الأخطاء في الرسائل
            if (errorMessages.length > 0) {
                isValid = false;
                displayErrors(); // Call function to display errors next to each field
            }

            return isValid;
        }

        function displayErrors() {
            // إخفاء الرسائل القديمة أولاً
            document.querySelectorAll('.error-message').forEach(errorDiv => errorDiv.style.display = 'none');

            // عرض الرسائل الجديدة
            errorMessages.forEach(error => {
                const errorElement = document.getElementById(`${error.field}_error`);
                if (errorElement) {
                    errorElement.innerText = error.message;
                    errorElement.style.display = 'block';
                }
                const inputElement = document.getElementById(error.field);
                if (inputElement) {
                    inputElement.style.borderColor = 'red'; // Highlight the input field with an error
                }
            });
        }

        submitButton.addEventListener('click', function(e) {
            e.preventDefault();  // منع الانتقال مباشرة

            // تحقق من صحة المدخلات (على سبيل المثال: المدخلات التي تم تعديلها)
            const isValid = validateForm();

            if (isValid) {
                form.submit();  // إرسال النموذج إذا كان صحيحًا
            } else {
                // تسجيل الأخطاء في الكونسول لمساعدتك في التشخيص


                // استخدام SweetAlert لعرض رسالة الأخطاء
                Swal.fire({
                    icon: 'error',
                    title: 'يوجد أخطاء في المدخلات',
                    html: '<ul>' + errorMessages.map(error => `<li>${error.message}</li>`).join('') + '</ul>',
                    background: '#fff',
                    confirmButtonColor: '#d33',
                    iconColor: '#d33',
                    confirmButtonText: 'إغلاق',
                    customClass: {
                    confirmButton: 'swal-button-custom'  // تنسيق الزر
                    }
                });
            }
        });


        function editFamilyMember(familyMemberId) {
            fetch(`/get-family-member-data/${familyMemberId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(familyMemberData => {
                    // 🔍 تشخيص كامل للـ response
                    console.log("=" .repeat(50));
                    console.log("📦 كامل الـ Response:", familyMemberData);
                    console.log("📦 familyMemberData.data:", familyMemberData.data);
                    console.log("📱 Phone من data:", familyMemberData.data?.phone);
                    console.log("📱 Phone مباشرة:", familyMemberData.phone);
                    console.log("🔍 typeof phone:", typeof familyMemberData.data?.phone);
                    console.log("=" .repeat(50));

                    if (!familyMemberData.success) {
                        showAlert(familyMemberData.message || 'تعذر تحميل بيانات العضو', 'error');
                        return;
                    }

                    // تعبئة البيانات الأساسية
                    document.getElementById('familyMemberId').value = familyMemberData.data.id || familyMemberData.id;
                    document.getElementById('edit_f_first_name').value = familyMemberData.data.first_name || familyMemberData.first_name || '';
                    document.getElementById('edit_f_father_name').value = familyMemberData.data.father_name || familyMemberData.father_name || '';
                    document.getElementById('edit_f_grandfather_name').value = familyMemberData.data.grandfather_name || familyMemberData.grandfather_name || '';
                    document.getElementById('edit_f_family_name').value = familyMemberData.data.family_name || familyMemberData.family_name || '';
                    document.getElementById('edit_f_id_num').value = familyMemberData.data.id_num || familyMemberData.id_num || '';

                    // معالجة تاريخ الميلاد
                    let dobValue = familyMemberData.data.dob || familyMemberData.dob;
                    if (dobValue) {
                        dobValue = String(dobValue).split('T')[0];
                    }
                    document.getElementById('edit_f_dob').value = dobValue || '';

                    // حفظ رقم الجوال مع تشخيص
                    const phoneValue = familyMemberData.data?.phone || familyMemberData.phone || '';
                    console.log("💾 phoneValue المحفوظ:", phoneValue, "| empty?", phoneValue === '');

                    // تحديد الصلة
                    const relSelect = document.getElementById('edit_f_relationship');
                    const incomingRel = (familyMemberData.data.relationship || familyMemberData.relationship || '').toString().trim();

                    relSelect.value = incomingRel;

                    if (!relSelect.value && incomingRel) {
                        for (let i = 0; i < relSelect.options.length; i++) {
                            if (relSelect.options[i].value.toLowerCase() === incomingRel.toLowerCase()) {
                                relSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }

                    console.log("👔 الصلة المختارة:", relSelect.value);

                    // تعيين رقم الجوال قبل handleEditRelationshipChange
                    const phoneField = document.getElementById('edit_f_phone');
                    if (phoneField) {
                        phoneField.value = phoneValue;
                        console.log("✅ تم تعيين phone قبل toggle:", phoneField.value);
                    }

                    // تحديث الرؤية
                    handleEditRelationshipChange();

                    // تحقق من القيمة بعد toggle مباشرة
                    console.log("🔍 القيمة بعد toggle مباشرة:", phoneField.value);

                    // إعادة تعيين بعد 100ms
                    setTimeout(() => {
                        const phoneFieldAfter = document.getElementById('edit_f_phone');
                        if (phoneFieldAfter) {
                            const currentValue = phoneFieldAfter.value;
                            console.log("🔍 القيمة الحالية قبل setTimeout:", currentValue);

                            if (!currentValue && phoneValue) {
                                phoneFieldAfter.value = phoneValue;
                                console.log("✅ تم إعادة تعيين القيمة:", phoneValue);
                            } else {
                                console.log("ℹ️ القيمة موجودة أو phoneValue فارغ");
                            }
                        }
                    }, 100);

                    document.getElementById('editFamilyMemberModal').classList.remove('hidden');
                    console.log("✅ تم فتح الفورم");
                })
                .catch(error => {
                    console.error("❌ خطأ:", error);

                    if (error.name === 'SyntaxError' || error.message.includes('HTTP error')) {
                        fetch(`/get-family-member-data/${familyMemberId}`)
                            .then(response => response.text())
                            .then(text => {
                                try {
                                    const errorData = JSON.parse(text);
                                    if (errorData.rejected_id && errorData.reason) {
                                        showAlert(
                                            `رقم الهوية المرفوض: <strong>${errorData.rejected_id}</strong><br>` +
                                            `سبب الرفض: <strong>${errorData.reason}</strong>`,
                                            'error'
                                        );
                                    } else {
                                        showAlert(errorData.message || 'تعذر تحميل بيانات العضو', 'error');
                                    }
                                } catch (e) {
                                    showAlert('تعذر تحميل بيانات العضو، يرجى المحاولة مرة أخرى.', 'error');
                                }
                            })
                            .catch(() => {
                                showAlert('تعذر الاتصال بالخادم، يرجى التحقق من الاتصال بالإنترنت.', 'error');
                            });
                    } else {
                        showAlert('حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.', 'error');
                    }
                });
        }

        function handleEditRelationshipChange() {
            togglePhoneVisibility('edit_f_relationship', 'edit_f_phone_group', 'edit_f_phone', 'edit_f_phone_error');
        }

        // دالة لإغلاق الفورم المنبثق
        function closeModal1() {
            document.getElementById('editFamilyMemberModal').classList.add('hidden');
        }
        function closeModal2() {
            document.getElementById('form-popup').classList.add('hidden');
        }

        // دالة الحذف باستخدام SweetAlert
        // إعداد CSRF token في AJAX
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // أخذ توكن CSRF من الـ meta tag
                }
            });
        });

        // دالة الحذف
        function deletePerson(id) {
            // عرض نافذة تحذير باستخدام SweetAlert
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لا يمكنك التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، حذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إرسال طلب AJAX لحذف العنصر
                    $.ajax({
                        url: '/person/' + id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // أخذ توكن CSRF من الـ meta tag
                        },
                        success: function(response) {
                            Swal.fire(
                                'تم الحذف!',
                                'تم حذف الفرد بنجاح.',
                                'success'
                            );
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'خطأ!',
                                'يرجة تعديل الحالة الاجتماعية لتتمكن من القيام بعملية الحذف',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // ========================================
        // Navigation and Sidebar Functions
        // ========================================

        // Toggle navigation item and its sub-menu
        function toggleNav(element, subNavId) {
            // Remove active from all nav-items
            const allNavItems = document.querySelectorAll('.nav-item');
            allNavItems.forEach(item => {
                if (item !== element) {
                    item.classList.remove('active');
                    const subNav = item.querySelector('.sub-nav');
                    if (subNav && subNav.id !== subNavId) {
                        subNav.classList.remove('active');
                    }
                }
            });

            // Toggle active on clicked item
            element.classList.toggle('active');

            // Toggle sub-nav if exists
            if (subNavId) {
                const subNav = document.getElementById(subNavId);
                if (subNav) {
                    subNav.classList.toggle('active');
                }
            }
        }

        // Show specific content section (Scroll to it)
        function showSection(sectionId, event) {
            if (event) {
                event.stopPropagation();
            }

            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Update active sub-nav item styling
            const allSubNavItems = document.querySelectorAll('.sub-nav-item');
            allSubNavItems.forEach(item => {
                item.classList.remove('active');
            });
            if (event && event.target) {
                event.target.classList.add('active');
            }
        }

        // Open password popup
        function openPasswordPopup(event) {
            if (event) {
                event.stopPropagation();
            }
            document.getElementById('password-popup').classList.remove('hidden');
        }

        // Close password popup
        function closepasswordpopup() {
            document.getElementById('password-popup').classList.add('hidden');
        }
    </script>

</body>
</html>
