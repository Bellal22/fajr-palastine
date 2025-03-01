<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إرسال الشكوى بنجاح</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                        url({{ asset('background/image.jpg') }}) center center no-repeat;
            background-size: cover;
            min-height: 100vh;
            overflow: auto;
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
            font-size: 25px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        p {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .highlight {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FF6F00;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .back-btn {
            display: inline-block;
            background-color: #FF6F00;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #E65100;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            h1 {
                font-size: 22px;
            }

            .container {
                padding: 20px;
                width: 90%;
            }

            .back-btn {
                font-size: 1rem;
                padding: 8px 16px;
            }

            .logo {
                width: 120px;
            }

            p {
                font-size: 1rem;
            }

            .highlight {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 18px;
            }

            .container {
                padding: 15px;
                width: 100%;
            }

            .back-btn {
                font-size: 0.9rem;
                padding: 6px 12px;
            }

            .logo {
                width: 100px;
            }

            p {
                font-size: 0.9rem;
            }

            .highlight {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('background/image.jpg') }}" alt="شعار الجمعية" class="logo">
        </div>
        <h1>تم إرسال الشكوى بنجاح✔️</h1>
        <p>تم تسجيل شكواك في بنجاح . وسنعمل على مراجعتها ومعالجتها في أقرب وقت ممكن🤗</p>
        <p>شكراً لك على وقتك وتواصلك معنا ❤️</p>
        <a href="{{ route('persons.intro') }}" class="back-btn">الرجوع إلى الرئيسية</a>
    </div>
</body>
</html>
