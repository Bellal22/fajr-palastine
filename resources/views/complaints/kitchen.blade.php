<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تقديم شكوى المطبخ - جمعية الفجر الشبابي الفلسطيني</title>

    <!-- استيراد الخطوط والأيقونات والمكتبات -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

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

        /* جزيئات صغيرة */
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
            z-index: 10;
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

        input[type="text"],
        input[type="tel"],
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
            background: linear-gradient(135deg, #FF6F00, #E65100);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 111, 0, 0.4);
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 111, 0, 0.5);
        }

        /* Scanner Area Styles */
        #scanner-card {
            border: 2px dashed #FF6F00;
            padding: 20px;
            border-radius: 15px;
            background: #fffdfa;
            margin-bottom: 25px;
            text-align: center;
        }

        #reader {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 20px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #ccc;
        }

        .verified-card {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 12px;
            border-right: 4px solid #2e7d32;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-back {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #FF6F00;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 968px) {
            .main-row {
                grid-template-columns: 1fr;
            }
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

    <!-- الجزيئات الصغيرة -->
    <div class="particles">
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

        <div class="main-row">
            <!-- المحتوى الرئيسي -->
            <div class="main-content">
                <div class="welcome-box">
                    <h2>تقديم شكوى المطبخ</h2>
                    <p>مرحباً بك، يرجى مسح باركود البطاقة أو إدخاله للتحقق وتقديم شكواك.</p>
                </div>

                <!-- STEP 1: Verification Card (Scanner & Manual) -->
                <div id="verification-step">
                    <div id="scanner-card">
                        <h4 style="margin-bottom: 15px; color: #E65100;"><i class="fas fa-qrcode"></i> مسح باركود البطاقة</h4>
                        
                        {{-- Reader viewport --}}
                        <div id="reader"></div>

                        <div style="margin: 15px 0; color: #666; font-weight: bold;">أو</div>

                        {{-- Manual entry --}}
                        <div class="form-group" style="max-width: 400px; margin: 0 auto 15px;">
                            <label style="text-align: center;">إدخال كود الباركود يدوياً</label>
                            <div class="input-wrapper">
                                <i class="fas fa-barcode input-icon"></i>
                                <input type="text" id="manual_barcode" placeholder="مثال: KITCHEN-CAMP-1234">
                            </div>
                        </div>

                        <button class="submit-btn" style="max-width: 400px; margin: 0 auto; display: block;" onclick="verifyBarcode()">
                            <i class="fas fa-check-circle"></i> تحقق من البطاقة
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Unlocked Complaint Form (Hidden by default) -->
                <div id="complaint-step" style="display: none;">
                    <div class="verified-card">
                        <div>
                            <i class="fas fa-check-circle"></i> <strong>تم التحقق بنجاح!</strong>
                            <div style="font-size: 14px; margin-top: 5px;">
                                المخيم: <span id="camp_display"></span> | رقم البطاقة: <span id="card_display"></span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-success" onclick="resetVerification()" style="border: 1px solid; background: transparent; padding: 5px 10px; border-radius: 6px; cursor: pointer; color: inherit;">تغيير</button>
                    </div>

                    <form id="kitchenComplaintForm">
                        <!-- Hidden verified fields -->
                        <input type="hidden" name="camp_name" id="camp_name_val">
                        <input type="hidden" name="card_number" id="card_number_val">

                        <div class="form-group">
                            <label>اسم مقدم الشكوى</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="complainant_name" id="complainant_name" placeholder="أدخل اسمك الكامل" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>رقم جوال التواصل</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="mobile_number" id="mobile_number" placeholder="أدخل رقم الجوال" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>عنوان الشكوى</label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" name="complaint_title" id="complaint_title" placeholder="أدخل عنواناً مختصراً للمشكلة" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>تفاصيل الشكوى</label>
                            <div class="input-wrapper">
                                <i class="fas fa-comment-alt input-icon"></i>
                                <textarea name="complaint_text" id="complaint_text" placeholder="اكتب شكواك أو استفسارك هنا بكل وضوح..." required></textarea>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">
                            <span><i class="fas fa-paper-plane"></i> إرسال الشكوى</span>
                        </button>
                    </form>
                </div>

                <a href="{{ route('complaint') }}" class="nav-back"><i class="fas fa-arrow-right"></i> العودة لاختيار نوع الشكوى</a>
            </div>

            <!-- القسم الجانبي -->
            <div class="sidebar">
                <h2>إرشادات الشكاوى</h2>

                <div class="info-item">
                    <h3><i class="fas fa-pen"></i> وضوح الشكوى</h3>
                    <p>يرجى كتابة الشكوى بشكل واضح ومفصل لنتمكن من مساعدتك بشكل أفضل.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-clock"></i> وقت الاستجابة</h3>
                    <p>سيتم الرد على شكواك في أقرب وقت ممكن من قبل الفريق المختص.</p>
                </div>

                <div class="info-item">
                    <h3><i class="fas fa-shield-alt"></i> السرية</h3>
                    <p>جميع الشكاوى سرية تماماً ويتم التعامل معها بمهنية عالية.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner = null;

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlBarcode = urlParams.get('barcode');
            if (urlBarcode) {
                document.getElementById('manual_barcode').value = urlBarcode;
                verifyBarcode(urlBarcode);
            } else {
                startScanner();
            }

            // Periodically translate and style scanner buttons
            setInterval(() => {
                const scanButton = document.getElementById('html5-qrcode-button-camera-permission');
                if (scanButton) {
                    scanButton.innerText = 'السماح باستخدام الكاميرا للبدء';
                    scanButton.style.display = 'inline-block';
                    scanButton.style.padding = '12px 25px';
                    scanButton.style.backgroundColor = '#FF6F00';
                    scanButton.style.color = '#fff';
                    scanButton.style.border = 'none';
                    scanButton.style.borderRadius = '10px';
                    scanButton.style.fontFamily = 'Cairo';
                    scanButton.style.fontWeight = 'bold';
                    scanButton.style.cursor = 'pointer';
                    scanButton.style.margin = '10px 0';
                    scanButton.style.boxShadow = '0 4px 15px rgba(255, 111, 0, 0.3)';
                }
                const fileButton = document.getElementById('html5-qrcode-button-file-selection');
                if (fileButton) {
                    fileButton.innerText = 'مسح كود من ملف صورة';
                    fileButton.style.display = 'inline-block';
                    fileButton.style.padding = '10px 20px';
                    fileButton.style.backgroundColor = '#6c757d';
                    fileButton.style.color = '#fff';
                    fileButton.style.border = 'none';
                    fileButton.style.borderRadius = '10px';
                    fileButton.style.fontFamily = 'Cairo';
                    fileButton.style.fontWeight = '600';
                    fileButton.style.cursor = 'pointer';
                    fileButton.style.margin = '10px 0';
                }
                const readerHeader = document.querySelector('#reader__header_message');
                if (readerHeader) {
                    readerHeader.style.display = 'none';
                }
            }, 150);
        });

        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: { width: 250, height: 150 },
                rememberLastUsedCamera: true
            });
            html5QrcodeScanner.render(onScanSuccess);
        }

        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('manual_barcode').value = decodedText;
            verifyBarcode(decodedText);
        }

        function verifyBarcode(barcodeVal = null) {
            const barcode = barcodeVal || document.getElementById('manual_barcode').value.trim();

            if (!barcode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى مسح الباركود أو إدخال الكود يدوياً للتحقق.',
                    confirmButtonColor: '#FF6F00'
                });
                return;
            }

            Swal.fire({
                title: 'جاري التحقق...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('/complaint/kitchen/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().catch(err => console.log(err));
                    }

                    document.getElementById('verification-step').style.display = 'none';
                    document.getElementById('complaint-step').style.display = 'block';

                    document.getElementById('camp_display').innerText = data.camp_name;
                    document.getElementById('card_display').innerText = data.card_number;
                    
                    document.getElementById('camp_name_val').value = data.camp_name;
                    document.getElementById('card_number_val').value = data.card_number;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'لم ينجح التحقق',
                        text: data.message,
                        confirmButtonColor: '#FF6F00'
                    });
                }
            })
            .catch(err => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الاتصال بالسيرفر. يرجى المحاولة لاحقاً.',
                    confirmButtonColor: '#FF6F00'
                });
            });
        }

        function resetVerification() {
            document.getElementById('verification-step').style.display = 'block';
            document.getElementById('complaint-step').style.display = 'none';
            document.getElementById('manual_barcode').value = '';
            startScanner();
        }

        document.getElementById('kitchenComplaintForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'جاري الإرسال...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData(this);
            const dataObj = {};
            formData.forEach((value, key) => dataObj[key] = value);

            fetch('/complaint/kitchen/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dataObj)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح!',
                        text: data.message,
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسنًا'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('complaints.successcomplaint') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء حفظ الشكوى. يرجى المحاولة مرة أخرى.',
                        confirmButtonColor: '#FF6F00'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الشبكة.',
                    confirmButtonColor: '#FF6F00'
                });
            });
        });
    </script>
</body>
</html>
