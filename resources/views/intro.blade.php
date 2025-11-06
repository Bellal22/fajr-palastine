<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جمعية الفجر الشبابي الفلسطيني</title>

    <!-- استيراد خط من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- استيراد Font Awesome لأيقونات الزر -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- إضافة SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                        url({{asset('background/image.jpg')}}) center center no-repeat;
            background-size: cover;
            background-attachment: fixed; /* تثبيت الخلفية عند التمرير */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            overflow: auto;
            position: relative;
        }

        h1 {
            color: #FF6F00;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100%;
            max-width: 150px; /* منع تكبير الشعار أكثر من اللازم */
            height: auto;
        }

        p {
            text-align: right;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: flex-start;
        }

        .form-group label {
            font-weight: bold;
            font-size: 1rem;
            width: 100%;
            text-align: right;
        }

        input {
            font-size: 1rem;
            padding: 10px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        /* تحسين الأزرار */
        button, .link-btn {
            display: inline-block;
            background-color: #FF6F00;
            color: white;
            padding: 12px 20px;
            font-size: 1.1rem;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
            width: 100%; /* تناسب كل الأزرار */
        }

        button:hover, .link-btn:hover {
            background-color: #E65100;
        }

        /* ترتيب الأزرار بشكل متجاوب */
        .buttons-container {
            display: flex;
            flex-wrap: wrap; /* السماح للأزرار بالنزول في سطر جديد */
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        /* تحسين رسائل الخطأ والنجاح */
        .error-message {
            color: #ff0000;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .success-message {
            color: #22b722;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* تحسين النصوص */
        .styled-text {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            padding: 10px;
        }

        /* تحسين التصميم للأجهزة الصغيرة */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            .buttons-container {
                flex-direction: column; /* ترتيب الأزرار عموديًا على الشاشات الصغيرة */
            }

            button, .link-btn {
                max-width: 100%; /* جعل الأزرار تأخذ العرض الكامل */
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            input {
                font-size: 0.9rem;
                padding: 8px;
            }

            button {
                font-size: 1rem;
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

        <!-- العنوان الرئيسي -->
        <h1> جمعية الفجر الشبابي الفلسطيني</h1>

        <!-- نبذة تعريفية -->
        <p>
            جمعية الفجر الشبابي الفلسطيني جمعية أهلية خيرية غير ربحية مرخصة من السلطة الفلسطينية عام 2002م تحت رقم 7230 وتعمل في قطاع غزة, حيث ا أنشئت الجمعية لتساهم في تقديم الخدمات والرعاية الواجبة للشباب والنساء والأطفال على وجه الخصوص ولكافة شرائح المجتمع على وجه العموم منها خدمات اجتماعية وتعليمية وتربوية ونفسية وصحية سواء على مستوى الفرد أو الجماعة، وأن الهدف العام من إنشاء الجمعية هو تمكين الشباب والمرأة والطفل وشرائح المجتمع كافة ضمن برامج متخصصة وبشراكات مباشرة من خلال إرساء قواعد المساواة والقيم الديمقراطية والعدالة المجتمعية.
        </p>

        <p>
            تعمل الجمعية حالياً ضمن خطة طوارئ حيث تم العمل ضمن هذه الخطة من تاريخ 09/10/2023م,
            تستهدف الجمعية النازحين بمحافظات قطاع غزة وتقدم المساعدات للمواطنين والنازحين ضمن مشاريع متنوعة.
        </p>

        <p>
            وانطلاقاً من واجب الجمعية و حرصها على تقديم خدماتها لأبناء شعبنا الصامد قامت بإعداد نظام تسجيل للوصول السريع لكافة المواطنين و استهدافهم في خطة العمل الإغاثية الحالية.
        </p>

        <p class="styled-text">
            يمكنكم الآن التسجيل وتحديث بياناتك وتقديم الشكاوي عن طريق هذا النظام بعد التحقق من صحة رقم الهوية🤗
        </p>

        {{-- <h1>
            النظام قيد الصيانة الان سنكون متواجدين في أقرب وقت
        </h1> --}}

        <!-- النموذج -->
        <form action="{{ url('/set-session') }}" method="POST" onsubmit="return validateIdNumber()">
            @csrf
            <div class="form-group">
                <label for="id_num">رقم الهوية:</label>
                <input type="number" id="id_num" name="id_num" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInput()" maxlength="9">
                <span id="id_num_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                <span id="id_num_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
            </div>
            <div class="buttons-container">
            </div>
            <div class="buttons-container">
                <button type="submit">التالي</button>
                {{-- <button onclick="checkAndRedirectToLogin()">لقد سجلت بالفعل؟ الذهاب لتسجيل الدخول</button> --}}
            </div>
        </form>

        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
            <button onclick="window.location.href='{{ route('loginView') }}'">
                لقد سجلت بالفعل؟ الذهاب لتسجيل الدخول
            </button>
            <button onclick="window.location.href='{{ route('complaint') }}'">
                انتقال إلى صفحة الشكاوى
            </button>
        </div>

    </div>

    <script>
        function checkAndRedirectToLogin() {
            // جلب رقم الهوية من الجلسة
            let id_num = '{{ session('id_num') }}';

            // التحقق من وجود رقم الهوية في قاعدة البيانات
            fetch(`/check-id`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // إذا كان المستخدم مسجلًا في قاعدة البيانات، يتم التوجيه إلى صفحة الشكاوى
                        window.location.href = '{{ route('loginView') }}';
                    } else {
                        // إذا لم يكن مسجلًا، عرض رسالة خطأ
                        Swal.fire({
                            icon: 'error',
                            title: 'لا يمكن الوصول إلى صفحة تسجيل الدخول',
                            text: 'رقم الهوية الخاص بك غير مسجل لدينا. الرجاء التسجيل أولاً.',
                            background: '#fff',
                            confirmButtonColor: '#d33',
                            iconColor: '#d33',
                            confirmButtonText: 'إغلاق',
                            customClass: {
                                confirmButton: 'swal-button-custom'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'حدث خطأ',
                        text: 'يرجى القيام بإدخال رقم الهوية أولاً',
                        background: '#fff',
                        confirmButtonColor: '#d33',
                        iconColor: '#d33',
                        confirmButtonText: 'إغلاق',
                        customClass: {
                            confirmButton: 'swal-button-custom'
                        }
                    });
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
            const idNum = document.getElementById('id_num').value;
            const errorMessage = document.getElementById('id_num_error');
            const successMessage = document.getElementById('id_num_success');
            const inputField = document.getElementById('id_num');

            // منع المستخدم من إدخال أكثر من 9 أرقام
            if (idNum.length > 9) {
                document.getElementById('id_num').value = idNum.slice(0, 9);  // اقتصاص الأرقام الزائدة
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
    </script>
</body>
</html>
