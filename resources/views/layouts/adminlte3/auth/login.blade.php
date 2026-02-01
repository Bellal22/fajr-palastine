<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>تسجيل الدخول | الفجر الشبابي</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- خطوط جوجل -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- مكتبة الأيقونات FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover: #0056b3;
            --text-color: #2d3748;
            --text-muted: #718096;
            --bg-glass: rgba(255, 255, 255, 0.92);
            --border-color: #edf2f7;
            --shadow-card: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f0f4f8;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
        }

        /* حاوية الصفحة الرئيسية */
        .login-page {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Right side in RTL */
            position: relative;
            background: url("{{ asset('background/login-final-mgt.png') }}") no-repeat left center;
            background-size: cover;
        }

        /* منطقة الفورم (اليمين) */
        .login-form-container {
            width: 60%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Right side in RTL */
            padding-right: 2.5cm;
            padding-left: 20px;
            z-index: 2;
            position: relative;
        }

        .login-card {
            background: var(--bg-glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 3rem;
            width: 150%;
            max-width: 450px; /* تحديد عرض الكارد ليكون أنيقاً */
            box-shadow: var(--shadow-card);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .welcome-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .welcome-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .welcome-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            text-align: right; /* تأكيد الاتجاه داخل الفورم */
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-text {
            position: absolute;
            right: 18px; /* الأيقونة على اليمين */
            background: transparent;
            border: none;
            color: #007bff;
            z-index: 5;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            height: 54px;
            padding: 0 55px; /* مسافة للأيقونة */
            border: 2px solid var(--border-color);
            border-radius: 14px;
            font-size: 1rem;
            color: var(--text-color);
            background: #fff;
            transition: all 0.3s ease;
            font-family: 'Cairo', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.1);
        }

        /* تغيير لون الأيقونة عند التركيز */
        .form-control:focus + .input-group-text {
            color: var(--primary-color);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: var(--text-muted);
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input {
            width: 18px;
            height: 18px;
            accent-color: #007bff;
            cursor: pointer;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .forgot-password:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            height: 56px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Cairo', sans-serif;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(0, 123, 255, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn.loading {
            opacity: 0.8;
            pointer-events: none;
            background: #5a9fd4;
        }

        /* Language Selector */
        .lang-switcher {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .lang-switcher a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .lang-switcher a:hover, .lang-switcher a.active {
            color: var(--primary-color);
            background: rgba(0, 123, 255, 0.08);
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.8rem;
            margin-top: 6px;
            text-align: right;
            display: none; /* مخفي افتراضياً */
            padding-right: 5px;
        }

        /* زر إظهار كلمة المرور */
        .toggle-password {
            position: absolute;
            left: 18px; /* على اليسار في وضع RTL */
            background: transparent;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            z-index: 5;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--text-color);
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #48bb78;
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-100px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 100;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-decoration {
                display: none; /* إخفاء الصورة الجانبية في التابلت والموبايل */
            }

            .login-page {
                background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
                justify-content: center;
            }

            .login-form-container {
                width: 100%;
                padding: 20px;
            }

            .login-card {
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
            .welcome-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body class="hold-transition">

<div class="login-page">
    <!-- حاوية الفورم (اليمين) -->
    <div class="login-form-container">
        <!-- Login Card -->
        <div class="login-card">
            <div class="welcome-header">
                <h1>جمعية الفجر الشبابي الفلسطيني </h1>
                <p>مرحباً بك مجدداً<br>يرجى تسجيل الدخول للمتابعة</p>
            </div>

            <form id="loginForm" action="{{ route('login') }}" method="post">
                @csrf

                <div class="form-group">
                    <div class="input-group">
                        <input type="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="البريد الإلكتروني / اسم المستخدم"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    @error('email')
                        <div class="error-message" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="passwordInput"
                               placeholder="كلمة المرور"
                               required>
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <!-- زر إظهار/إخفاء كلمة المرور -->
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-message" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>تذكرني</span>
                    </label>
                    <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="login-btn" id="submitBtn">
                    <span>تسجيل الدخول</span>
                    <i class="fas fa-arrow-left"></i>
                </button>
            </form>

            <div class="lang-switcher">
                <a href="#" class="active">العربية</a>
                <a href="#">English</a>
            </div>
        </div>
    </div>
</div>

<!-- رسالة نجاح (Toast) -->
<div class="toast" id="successToast">
    <i class="fas fa-check-circle"></i>
    <span>تم تسجيل الدخول بنجاح!</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const passwordInput = document.getElementById('passwordInput');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const emailInput = document.querySelector('input[name="email"]');
        const toast = document.getElementById('successToast');

        // تبديل رؤية كلمة المرور
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // تغيير الأيقونة
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // معالجة تقديم النموذج
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            let isValid = true;

            // تحقق بسيط من القيم قبل الإرسال (اختياري)
            if (emailInput.value.length < 5 || passwordInput.value.length < 1) {
                isValid = false;
            }

            if (isValid) {
                startLoading();
                loginForm.submit();
            }
        });

        function startLoading() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';
        }

        // إزالة تنسيق الخطأ عند الكتابة
        emailInput.addEventListener('input', function() {
            this.style.borderColor = '#edf2f7';
        });

        passwordInput.addEventListener('input', function() {
            this.style.borderColor = '#edf2f7';
        });
    });
</script>

</body>
</html>
