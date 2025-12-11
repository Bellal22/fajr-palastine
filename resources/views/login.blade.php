<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                        url({{ asset('background/image.jpg') }}) center center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        h1 {
            color: #FF6F00;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: right;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        /* تحسين الزر وتخطيط الأزرار */
        .button-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .button-container button,
        .button-container a {
            flex: 1;
            max-width: 200px;
            background-color: #FF6F00;
            color: white;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            box-sizing: border-box;
        }

        .button-container button:hover,
        .button-container a:hover {
            background-color: #E65100;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        .toggle-password {
            position: absolute;
            left: 1rem;
            top: 50%;
            cursor: pointer;
            color: #555;
            font-size: 1rem;
        }

        /* خلفية البوب أب */
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
        }

        /* محتوى البوب أب */
        .popup-content {
            position: relative;
            width: 90%;
            max-width: 400px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* زر الإغلاق */
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
            color: #555;
        }

        .close:hover {
            color: black;
        }

        /* حقول الإدخال */
        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* زر الإرسال */
        .submit-btn {
            background-color: #ff7f00;
            color: white;
            border: none;
            padding: 10px 15px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background-color: #e66a00;
        }


        /* تحسين الاستجابة للأجهزة الصغيرة */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            input {
                font-size: 20px;
                padding: 10px;
            }

            .button-container {
                flex-direction: column;
                width: 100%;
            }

            .button-container button,
            .button-container a {
                max-width: 100%;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 18px;
            }

            input {
                font-size: 13px;
                padding: 8px;
            }

            .button-container button,
            .button-container a {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- الشعار -->
        <div class="logo-container">
            <img src="{{asset('background/image.jpg')}}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>

        {{-- <h1>النظام قيد الصيانة سنكون متواجدين في أقرب وقت</h1> --}}

        <h1>تسجيل الدخول</h1>

        <form id="loginForm" action="{{ route('user.login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="id_num">رقم الهوية:</label>
                <input type="string" id="id_num" name="id_num" placeholder="أدخل رقم الهوية" required>
                <span id="id_num_error" class="error-message">رقم الهوية غير صالح.</span>
            </div>

            <div class="form-group" style="position: relative; display: inline-block; width: 100%;">
                <input type="password" id="passkey" name="passkey" placeholder="أدخل كلمة المرور" required>
                <i id="togglePass" class="fa fa-eye toggle-password"
                    style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #555;">
                </i>
            </div>

            <a href="#" id="forgotPasswordLink" style="color: #E65100; text-decoration: none; font-weight: bold;">استعادة كلمة المرور</a>

            <div class="button-container">
                <button type="submit">تسجيل الدخول</button>
                <a href="{{ route('persons.intro') }}">الصفحة الرئيسية</a>
            </div>
        </form>

        <!-- بوب أب استعادة كلمة المرور -->
        <div id="forgotPasswordModal" class="popup" style="display: none;">
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 style="text-align: center; color: #ff7f00;">استعادة كلمة المرور</h2>

                <!-- إدخال رقم الهوية -->
                <label for="reset_id" style="display: block; font-weight: bold; margin-bottom: 5px;">أدخل رقم الهوية:</label>
                <input type="text" id="reset_id" placeholder="رقم الهوية" class="input-field">

                <!-- إدخال رقم الجوال -->
                <label for="reset_phone" style="display: block; font-weight: bold; margin-bottom: 5px;">أدخل رقم الجوال:</label>
                <input type="text" id="reset_phone" placeholder="رقم الجوال" class="input-field">

                <button id="resetPasswordBtn" class="submit-btn">إرسال</button>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            let passInput = document.getElementById('passkey');
            if (passInput.type === "password") {
                passInput.type = "text";
                this.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passInput.type = "password";
                this.classList.replace("fa-eye-slash", "fa-eye");
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // منع الإرسال الافتراضي

            const idNum = document.getElementById('id_num').value.trim();
            const passkey = document.getElementById('passkey').value.trim();
            const idNumError = document.getElementById('id_num_error');
            const form = event.target;

            idNumError.style.display = 'none'; // إخفاء الخطأ افتراضياً

            // التحقق من رقم الهوية في الفرونت إند
            if (idNum.length !== 9 || !/^\d{9}$/.test(idNum)) {
                idNumError.style.display = 'block';
                idNumError.textContent = 'رقم الهوية يجب أن يتكون من 9 أرقام فقط.';
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في تسجيل الدخول',
                    text: 'رقم الهوية يجب أن يكون مكونًا من 9 أرقام فقط.',
                    confirmButtonText: 'إغلاق'
                });
                return;
            }

            // التحقق من أن كلمة المرور غير فارغة
            if (passkey === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في تسجيل الدخول',
                    text: 'كلمة المرور مطلوبة.',
                    confirmButtonText: 'إغلاق'
                });
                return;
            }

            // إظهار مؤشر التحميل
            Swal.fire({
                title: 'جاري تسجيل الدخول...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // إرسال النموذج باستخدام AJAX
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // التحقق من نوع الاستجابة
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => ({
                        ok: response.ok,
                        status: response.status,
                        data: data
                    }));
                } else {
                    // في حالة عدم وجود JSON (مثل أخطاء الـ validation من Laravel)
                    return response.text().then(text => {
                        throw new Error('حدث خطأ في الاتصال بالخادم.');
                    });
                }
            })
            .then(result => {
                if (result.ok && result.data.success) {
                    // إعادة التوجيه في حالة النجاح
                    Swal.fire({
                        icon: 'success',
                        title: 'تم تسجيل الدخول بنجاح',
                        text: 'جاري تحويلك إلى صفحة الملف الشخصي...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = result.data.redirect;
                    });
                } else {
                    // عرض رسالة الخطأ حسب نوع الخطأ
                    let errorTitle = 'خطأ في تسجيل الدخول';
                    let errorIcon = 'error';

                    // تخصيص العنوان حسب حالة الخطأ
                    if (result.status === 404) {
                        errorTitle = 'رقم هوية غير موجود';
                    } else if (result.status === 403) {
                        errorTitle = 'تسجيل دخول غير مسموح';
                        errorIcon = 'warning';
                    } else if (result.status === 401) {
                        errorTitle = 'كلمة مرور خاطئة';
                    }

                    Swal.fire({
                        icon: errorIcon,
                        title: errorTitle,
                        text: result.data.message || 'حدث خطأ أثناء تسجيل الدخول.',
                        confirmButtonText: 'إغلاق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: 'حدث خطأ غير متوقع. الرجاء التحقق من الاتصال بالإنترنت والمحاولة مرة أخرى.',
                    confirmButtonText: 'إغلاق'
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("forgotPasswordModal");
            const closeModal = document.querySelector(".close");
            const forgotPasswordLink = document.getElementById("forgotPasswordLink");
            const resetPasswordBtn = document.getElementById("resetPasswordBtn");
            const resetIdInput = document.getElementById("reset_id");
            const resetPhoneInput = document.getElementById("reset_phone");

            forgotPasswordLink.addEventListener("click", function (event) {
                event.preventDefault();
                modal.style.display = "flex";
                // إعادة تعيين الحقول والرسائل عند فتح البوب أب
                resetIdInput.value = "";
                resetPhoneInput.value = "";
            });

            closeModal.addEventListener("click", function () {
                modal.style.display = "none";
            });

            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            resetPasswordBtn.addEventListener("click", function () {
                let idNum = resetIdInput.value.trim();
                let phone = resetPhoneInput.value.trim();

                if (!/^\d{9}$/.test(idNum)) {
                    Swal.fire({
                        icon: "warning",
                        title: "تنبيه",
                        text: "يرجى إدخال رقم هوية صحيح مكون من 9 أرقام.",
                        confirmButtonColor: "#ff7f00",
                    });
                    return;
                }

                if (!phone.match(/^\d{10,15}$/)) {
                    Swal.fire({
                        icon: "warning",
                        title: "تنبيه",
                        text: "يرجى إدخال رقم جوال صحيح (من 10 إلى 15 رقمًا).",
                        confirmButtonColor: "#ff7f00",
                    });
                    return;
                }

                fetch("{{ route('password.reset') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id_num: idNum, phone: phone })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = "none"; // إغلاق البوب أب

                        Swal.fire({
                            icon: 'success',
                            title: 'تم تحديث كلمة المرور',
                            text: 'تم تحديث كلمة مرورك إلى: ' + data.new_password,
                            confirmButtonColor: '#FF6F00',
                            confirmButtonText: 'موافق'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: data.message,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'إغلاق'
                        });
                    }
                })
                .catch(error => {
                    console.error("حدث خطأ:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في النظام',
                        text: 'حدث خطأ أثناء معالجة الطلب. حاول مرة أخرى لاحقًا.',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'إغلاق',
                    });
                });
            });
        });

    </script>

</body>
</html>
