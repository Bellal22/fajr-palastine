<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الشكاوى</title>

    <!-- استيراد خط من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

    <!-- استيراد مكتبة SweetAlert -->
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
            padding: 20px; /* تحسين الهوامش للأجهزة الصغيرة */
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
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: right;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            font-size: 1rem;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        /* تحسين الأزرار */
        button {
            background-color: #FF6F00;
            color: white;
            padding: 12px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #E65100;
        }

        /* تحسين الشعار */
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

        /* تحسين التصميم للشاشات الصغيرة */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            button {
                font-size: 0.95rem;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            input, textarea {
                font-size: 0.9rem;
                padding: 8px;
            }

            button {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- الشعار -->
        <div class="logo-container">
            <img src="{{ asset('background/image.jpg') }}" alt="شعار الجمعية" class="logo">
        </div>

        <!-- العنوان -->
        <h1>إرسال شكوى</h1>

        <!-- النموذج -->
        <form action="{{ route('complaints.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="id_num">رقم الهوية</label>
                <input type="number" id="id_num" name="id_num"
                    placeholder="أدخل رقم الهوية"
                    value="{{ old('id_num') }}"
                    required>
                </div>

            <div class="form-group">
                <label for="complaint_title">عنوان الشكوى</label>
                <input
                    type="text"
                    id="complaint_title"
                    name="complaint_title"
                    placeholder="أدخل عنوان الشكوى"
                    value="{{ old('complaint_title') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="complaint_text">نص الشكوى</label>
                <textarea
                    id="complaint_text"
                    name="complaint_text"
                    placeholder="أدخل نص الشكوى"
                    rows="5"
                    required>{{ old('complaint_text') }}</textarea>
            </div>

            <button type="submit">إرسال الشكوى</button>
        </form>

    </div>

    <!-- رسائل SweetAlert -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: '{{ session('success') }}',
                confirmButtonText: 'حسنًا'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح!',
                confirmButtonText: 'حسنًا'
            });
        </script>
    @endif
</body>
</html>
