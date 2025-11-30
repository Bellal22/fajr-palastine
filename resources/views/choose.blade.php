<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="id_num" content="{{ session('id_num', '') }}">
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
            background-attachment: fixed; /* لتثبيت الخلفية عند التمرير */
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

        .buttons-container {
            display: flex;
            flex-wrap: wrap; /* السماح للأزرار بالانتقال إلى سطر جديد عند الحاجة */
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

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
            width: 100%;
            max-width: 48%; /* لتجنب التمدد الزائد */
        }

        button:hover, .link-btn:hover {
            background-color: #E65100;
        }

        .swal-button-custom {
            font-size: 1.2rem;
            padding: 12px 30px;
            border-radius: 8px;
            min-width: 100px;
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
    </style>
</head>
<body>

    <div class="container">
        <!-- الشعار -->
        <div class="logo-container">
            <img src="{{asset('background/image.jpg')}}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>

        <!-- العنوان الرئيسي -->
        <h1>جمعية الفجر الشبابي الفلسطيني</h1>

        <p>
            فيما يلي أزرار تنقل لصفحتيين
        </p>

        <p>
            الصفحة الأولى ⬅️ تحتوي على فورم التسجيل للاستفادة من برامج الإغاثةالحالية ويتكون الفورم من قسميين
        </p>

        <p>
            القسم الأول : يحتوي على البيانات الشخصية لرب الأسرة
        </p>

        <p>
            القسم الثاني : يمكن رب الأسرة من إدخال بيانات أفراد أسرته كاملة.
        </p>

        <p>
            احرص عزيزي المواطن على تعبئة البيانات المطلوبة بدقة و مصدايقة لضمان الاستفاد.
        </p>

        <p>
            وحرصاً منا على سماعكم و تقديراً منا لمعاناتكم سهلنا وصولكم إلينا ووضعنا لكم في الصفحة الثانية ⬅️ فورم خاص بتسجيل الشكاوي والملاحظات.
        </p>

        <p>
            مع تمنياتنا لكم بدوام الصحة و الأمان ❤️
        </p>

        <div class="buttons-container">
            <button onclick="checkIdNumberAndRedirect()">انتقال إلى صفحة التسجيل</button>
            <button onclick="window.location.href='{{ route('complaint') }}'">انتقال إلى صفحة الشكاوى</button>
        </div>
    </div>

    <script>
        function checkIdNumberAndRedirect() {
            let id_num_meta = document.querySelector('meta[name="id_num"]');
            if (!id_num_meta || !id_num_meta.content) {
                console.error("رقم الهوية غير موجود في الجلسة.");
                return;
            }

            fetch(`/check-id`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // 1) مسجل في جدول الأشخاص
                if (data.exists_in_persons) {
                    Swal.fire({
                        icon: 'error',
                        title: 'رقم الهوية مسجل مسبقاً',
                        background: '#fff',
                        confirmButtonColor: '#d33',
                        iconColor: '#d33',
                        confirmButtonText: 'إغلاق',
                        customClass: {
                            confirmButton: 'swal-button-custom'
                        }
                    }).then(() => {
                        window.location.href = '/';
                    });
                    return;
                }

                // 2) غير مسجل في الأشخاص لكن موجود في قائمة المحظورين
                if (data.exists_in_banned) {
                    let reasonText = data.banned_reason
                        ? `سبب الرفض: ${data.banned_reason}`
                        : 'لا يمكنك إكمال التسجيل لهذا الرقم.';

                    Swal.fire({
                        icon: 'error',
                        title: 'رقم الهوية مرفوض من التسجيل',
                        text: reasonText,
                        background: '#fff',
                        confirmButtonColor: '#d33',
                        iconColor: '#d33',
                        confirmButtonText: 'إغلاق',
                        customClass: {
                            confirmButton: 'swal-button-custom'
                        }
                    }).then(() => {
                        window.location.href = '/';
                    });
                    return;
                }

                // 3) لا مسجل ولا محظور → يكمل على صفحة الإنشاء
                window.location.href = '/create';
            })
            .catch(error => console.error('Error:', error));
        }

        function checkAndRedirectToComplaints() {
            // جلب رقم الهوية من الجلسة
            let id_num = '{{ session('id_num') }}';

            // التحقق من وجود رقم الهوية في قاعدة البيانات
            fetch(`/check-id`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // إذا كان المستخدم مسجلًا في قاعدة البيانات، يتم التوجيه إلى صفحة الشكاوى
                        window.location.href = '{{ route('complaints.create') }}';
                    } else {
                        // إذا لم يكن مسجلًا، عرض رسالة خطأ
                        Swal.fire({
                            icon: 'error',
                            title: 'لا يمكن الوصول إلى صفحة الشكاوى',
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
                        text: 'يرجى المحاولة لاحقًا.',
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
    </script>
</body>
</html>
