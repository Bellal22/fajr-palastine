<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>نموذج تسجيل المواطنين - جمعية الفجر الشبابي الفلسطيني</title>

    <!-- استيراد خط من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- Import Font Awesome library for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Import TailwindCSS library for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        /* تنسيق عام */
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
            background-attachment: fixed;
            min-height: 100vh;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* تنسيق الشعار */
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

        /* تنسيق النموذج */
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .form-group label {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        input, select, textarea {
            font-size: 1rem;
            padding: 8px;
            width: 100%;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* ترتيب الحقول في صفوف متجاوبة */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
        }

        .row .form-group {
            flex: 1;
            min-width: 250px;
        }

        .row .form-group label {
            text-align: right;
        }

        #condition_description {
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        /* تنسيق الأزرار */
        button, .link-btn {
            background-color: #FF6F00;
            color: white;
            padding: 12px 0;
            font-size: 1rem;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
            width: 48%;
            min-width: 120px;
        }

        button:hover {
            background-color: #E65100;
        }

        /* تنسيق أزرار التحكم */
        .buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .back-btn {
            font-size: 1rem;
            padding: 10px 20px;
            background-color: #FF6F00;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn i {
            margin-left: 5px;
        }

        .back-btn:hover {
            background-color: #E65100;
        }

        /* رسائل الخطأ */
        .error-message {
            margin-top: 5px;
            font-size: 0.9rem;
            color: red;
            text-align: right;
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

            .row {
                flex-direction: column;
                align-items: center;
            }

            .row .form-group {
                width: 100%;
                min-width: unset;
            }

            input, select, textarea {
                font-size: 14px;
                padding: 8px;
            }

            .buttons-container {
                flex-direction: column;
                width: 100%;
            }

            .buttons-container button,
            .buttons-container a {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 18px;
            }

            input, select, textarea {
                font-size: 13px;
                padding: 8px;
            }

            .buttons-container button,
            .buttons-container a {
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

        <!-- العنوان الرئيسي -->
        <h1>جمعية الفجر الشبابي الفلسطيني</h1>
        @if ($errors->any())
            <div class="alert alert-danger text-start">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- النموذج -->
        <form action="{{ route('persons.store') }}" method="POST" id="form">
            @csrf
            <div class="row">
                <div class="form-group">
                    <label for="first_name">الاسم الأول</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        placeholder="الاسم الأول"
                        value="{{ old('first_name') }}"
                        oninput="validateArabicInput('first_name')"
                        onfocus="resetBorderAndError('first_name')"
                        onblur="validateArabicInput('first_name')"
                        required>
                    <div class="error-message" id="first_name_error" style="display:none; color: red;"></div>
                </div>
                <div class="form-group">
                    <label for="father_name">اسم الأب</label>
                    <input
                        type="text"
                        id="father_name"
                        name="father_name"
                        value="{{ old('father_name') }}"
                        placeholder="اسم الأب"
                        oninput="validateArabicInput('father_name')"
                        onfocus="resetBorderAndError('father_name')"
                        onblur="validateArabicInput('father_name')"
                        required>
                    <div class="error-message" id="father_name_error" style="display:none; color: red;"></div>
                </div>
                <div class="form-group">
                    <label for="grandfather_name">اسم الجد</label>
                    <input
                        type="text"
                        id="grandfather_name"
                        name="grandfather_name"
                        value="{{ old('grandfather_name') }}"
                        placeholder="اسم الجد"
                        oninput="validateArabicInput('grandfather_name')"
                        onfocus="resetBorderAndError('grandfather_name')"
                        onblur="validateArabicInput('grandfather_name')"
                        required>
                    <div class="error-message" id="grandfather_name_error" style="display:none; color: red;"></div>
                </div>
                <div class="form-group">
                    <label for="family_name">اسم العائلة</label>
                    <input
                        type="text"
                        id="family_name"
                        name="family_name"
                        value="{{ old('family_name') }}"
                        placeholder="اسم العائلة"
                        oninput="validateArabicInput('family_name')"
                        onfocus="resetBorderAndError('family_name')"
                        onblur="validateArabicInput('family_name')"
                        required>
                    <div class="error-message" id="family_name_error" style="display:none; color: red;"></div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="id_num">رقم الهوية</label>
                    <input type="number" disabled id="id_num" name="id_num" value="{{ $id_num }}" readonly>
                </div>

                <div class="form-group">
                    <label for="gender">الجنس</label>
                    <select id="gender" name="gender" required oninput="validateGender()" onfocus="resetBorderAndError('gender')" onblur="validateGender()">
                        <option value="">اختر الجنس</option>
                        @foreach(['غير محدد' => 'غير محدد','ذكر' => 'ذكر', 'أنثى' => 'أنثى'] as $key => $gender)
                            <option {{ old('gender') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $gender }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="gender_error" style="color: red;"></div>
                </div>


                <div class="form-group">
                    <label for="dob">تاريخ الميلاد</label>
                    <input
                        type="date"
                        id="dob"
                        name="dob"
                        value="{{ old('dob') }}"
                        oninput="validatedob()"
                        onfocus="resetBorderAndError('dob')"
                        onblur="validatedob()"
                        required>
                    <div class="error-message" id="dob_error" style="display:none; color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الجوال</label>
                    <input
                        type="text"
                        class="text-left"
                        dir="ltr"
                        placeholder="059-123-1234 or 056-123-1234"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        oninput="validatePhoneInput()"
                        onfocus="resetPhoneError()"
                        onblur="validatePhoneInput()"
                        required>
                    <div class="error-message" id="phone_error" style="display: none; color: red;"></div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="social_status">الحالة الاجتماعية</label>
                    <select id="social_status" name="social_status" required oninput="validateSocialStatus()" onfocus="resetBorderAndError('social_status')" onblur="validateSocialStatus()">
                        <option value="">اختر الحالة</option>
                        @foreach($social_statuses as $key => $status)
                            <option {{ old('social_status') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $status }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="social_status_error" style="color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="employment_status">حالة العمل</label>
                    <select id="employment_status" name="employment_status" required oninput="validateEmploymentStatus()" onfocus="resetBorderAndError('employment_status')" onblur="validateEmploymentStatus()">
                        <option value="لا يعمل" {{ old('employment_status') == 'لا يعمل' ? 'selected' : '' }}>لا يعمل</option>
                        <option value="موظف" {{ old('employment_status') == 'موظف' ? 'selected' : '' }}>موظف</option>
                        <option value="عامل" {{ old('employment_status') == 'عامل' ? 'selected' : '' }}>عامل</option>
                    </select>
                    <div class="error-message" id="employment_status_error" style="display: none; color: red;"></div>
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <label for="has_condition">هل لديك حالة صحية؟</label>
                <select id="has_condition" name="has_condition" onchange="toggleConditionDescription()">
                    <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                    <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                </select>
            </div>

            <div class="form-group" id="condition_description_group" style="display: none;">
                <label for="condition_description">وصف الحالة الصحية</label>
                <textarea
                    id="condition_description"
                    name="condition_description"
                    rows="4"
                    cols="50"
                    value="{{ old('condition_description') }}"
                    oninput="validateConditionText()"
                    onfocus="resetBorderAndError('condition_description')"
                    onblur="validateConditionText()"></textarea>
                <div class="error-message" id="condition_description_error" style="display: none; color: red;"></div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="city">المحافظة الأصلية</label>
                    <select id="city" name="city" required oninput="validateCity()" onfocus="resetBorderAndError('city')" onblur="validateCity()">
                        <option value="">اختر المحافظةالأصلية</option>
                        @foreach($cities as $key => $city)
                            <option {{ old('city') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $city }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="city_error" style="color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="housing_damage_status">حالة السكن السابق</label>
                    <select id="housing_damage_status" name="housing_damage_status" required oninput="validateHousingDamageStatus()" onfocus="resetBorderAndError('housing_damage_status')" onblur="validateHousingDamageStatus()">
                        <option value="">اختر حالة السكن السابق</option>
                        @foreach($housing_damage_statuses as $key => $status)
                            <option value="{{ $key }}" {{ old('housing_damage_status') == $key ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="housing_damage_status_error" style="color: red;"></div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="current_city">المحافظة الحالية</label>
                    <select id="current_city" name="current_city" required oninput="validateCurrentCity()" onfocus="resetBorderAndError('current_city')" onblur="validateCurrentCity()">
                        <option value="">اختر المحافظة الحالية</option>
                        @foreach($current_cities as $key => $city)
                            <option value="{{ $key }}" {{ old('current_city') == $key ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="current_city_error" style="color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="housing_type">نوع السكن الحالي</label>
                    <select id="housing_type" name="housing_type" required oninput="validateHousingType()" onfocus="resetBorderAndError('housing_type')" onblur="validateHousingType()">
                        <option value="">اختر نوع السكن الحالي</option>
                        @foreach($housing_types as $key => $type)
                            <option value="{{ $key }}" {{ old('housing_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="housing_type_error" style="color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="neighborhood">الحي السكني الحالي</label>
                    <select id="neighborhood" name="neighborhood" required oninput="validateNeighborhood()" onfocus="resetBorderAndError('neighborhood')" onblur="validateNeighborhood()">
                        <option value="">اختر الحي السكني الحالي </option>
                        <!-- سيتم تحديث هذه الخيارات باستخدام JavaScript -->
                    </select>
                    <div class="error-message" id="neighborhood_error" style="color: red;"></div>
                </div>

                <div class="form-group">
                    <label for="landmark">أقرب معلم</label>
                    <input
                        type="text"
                        id="landmark"
                        name="landmark"
                        placeholder="أقرب معلم"
                        value="{{ old('landmark') }}"
                        oninput="validateArabicInput('landmark')"
                        onfocus="resetBorderAndError('landmark')"
                        onblur="validateArabicInput('landmark')">
                    <div class="error-message" id="landmark_error" style="display:none; color: red;"></div>
                </div>
            </div>

            <!-- زر الإرسال -->
            <div class="buttons-container">
                <a href="{{ route('persons.intro') }}" class="link-btn">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
                <button type="submit" id="submit-button">تسجيل بيانات أفراد الأسرة</button>
            </div>
        </form>
    </div>

<script>

    function validateArabicInput(inputId) {
        const inputField = document.getElementById(inputId);
        const errorMessage = document.getElementById(`${inputId}_error`);
        const value = inputField.value.trim(); // إزالة المسافات الزائدة
        const arabicRegex = /^[\u0621-\u064A\s]+$/; // تطابق الحروف العربية فقط مع المسافات
        // console.log(value); //

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

    function validatedob() {
        const inputField = document.getElementById("dob");
        const errorMessage = document.getElementById("dob_error");
        const value = inputField.value.trim();
        // console.log(value);

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

    function resetBorderAndError(id) {
        document.getElementById(id).style.border = "";
        document.getElementById(id + "_error").textContent = "";
    }

    function validatePhoneInput() {
        const phoneInput = document.getElementById('phone');
        const errorMessage = document.getElementById('phone_error');
        let value = phoneInput.value.trim();
        console.log(value);

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

    function resetPhoneError() {
        // إعادة تعيين رسائل الخطأ وإزالة لون الإطار عند التركيز على الحقل
        const phoneInput = document.getElementById('phone');
        const errorMessage = document.getElementById('phone_error');

        phoneInput.style.borderColor = ''; // إزالة لون الإطار
        errorMessage.style.display = 'none'; // إخفاء رسالة الخطأ
        errorMessage.textContent = ''; // مسح نص رسالة الخطأ
    }


    document.getElementById('form').addEventListener('submit', function (e) {
        e.preventDefault();

        const phoneInput = document.getElementById('phone'); // إدخال رقم الجوال

        const phoneRegex = /^[0-9]{10,15}$/; // مثال للتحقق من رقم الجوال

        // إعادة تحقق من الرقم بعد تنسيقه
        if (!phoneRegex.test(phoneInput.value)) {
                Swal.fire({
                icon: 'error',
                title: 'خطأ في الإدخال',
                text: 'الرجاء إدخال رقم جوال صحيح',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        // إزالة "-" من رقم الجوال
        phoneInput.value = phoneInput.value.replace(/-/g, '');

        console.log('Form submitted');
        this.submit();
    });

    function validateSocialStatus() {
        const socialStatusInput = document.getElementById('social_status');
        const errorMessage = document.getElementById('social_status_error');
        const value = socialStatusInput.value.trim();
        console.log(value);

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
        const employmentStatusInput = document.getElementById('employment_status');
        const errorMessage = document.getElementById('employment_status_error');
        const value = employmentStatusInput.value.trim();
        console.log(value);

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
        const hasCondition = document.getElementById("has_condition").value;
        const conditionDescriptionGroup = document.getElementById("condition_description_group");
        console.log(hasCondition);

        if (hasCondition === "1") {
            conditionDescriptionGroup.style.display = "block";
        } else {
            conditionDescriptionGroup.style.display = "none";
            document.getElementById("condition_description").value = ""; // تفريغ الحقل إذا تم إخفاؤه
            resetBorderAndError('condition_description');
        }
    }

    function validateConditionText() {
        const inputField = document.getElementById("condition_description");
        const errorMessage = document.getElementById("condition_description_error");
        const value = inputField.value.trim();
        const hasCondition = document.getElementById("has_condition").value;
        console.log(value);

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
        const cityInput = document.getElementById('city');
        const errorMessage = document.getElementById('city_error');
        const value = cityInput.value.trim();
        console.log(value);

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

    document.getElementById('current_city').addEventListener('change', function () {
        const selectedCity = this.value;
        const neighborhoodSelect = document.getElementById('neighborhood');

        // مسح الخيارات السابقة
        neighborhoodSelect.innerHTML = '<option value="">اختر الحي السكني</option>';

        if (selectedCity === 'rafah') {
            // إضافة خيارات رفح
            const rafahNeighborhoods = [
                { value: 'masbah', label: 'مصبح' },
                { value: 'khirbetAlAdas', label: 'خربة العدس' },
                { value: 'alJaninehNeighborhood', label: 'حي الجنينة' },
                { value: 'alAwda', label: 'العودة' },
                { value: 'alZohourNeighborhood', label: 'حي الزهور' },
                { value: 'brazilianHousing', label: 'الإسكان البرازيلي' },
                { value: 'telAlSultan', label: 'تل السلطان' },
                { value: 'alShabouraNeighborhood', label: 'حي الشابورة' },
                { value: 'rafahProject', label: 'مشروع رفح' },
                { value: 'zararRoundabout', label: 'دوار زعرب' }
            ];

            rafahNeighborhoods.forEach(neighborhood => {
                const option = document.createElement('option');
                option.value = neighborhood.value;
                option.textContent = neighborhood.label;
                neighborhoodSelect.appendChild(option);
            });
        } else if (selectedCity === 'khanYounis') {
            // إضافة خيارات خانيونس
            const khanYounisNeighborhoods = [
                { value: 'qizanAlNajjar', label: 'قيزان النجار' },
                { value: 'qizanAbuRashwan', label: 'قيزان أبو رشوان' },
                { value: 'juraAlLoot', label: 'جورة اللوت' },
                { value: 'sheikhNasser', label: 'الشيخ ناصر' },
                { value: 'maAn', label: 'معن' },
                { value: 'alManaraNeighborhood', label: 'حي المنارة' },
                { value: 'easternLine', label: 'السطر الشرقي' },
                { value: 'westernLine', label: 'السطر الغربي' },
                { value: 'alMahatta', label: 'المحطة' },
                { value: 'alKatiba', label: 'الكتيبة' },
                { value: 'alBatanAlSameen', label: 'البطن السمين' },
                { value: 'alMaskar', label: 'المعسكر' },
                { value: 'alMashroo', label: 'المشروع' },
                { value: 'hamidCity', label: 'مدينة حمد' },
                { value: 'alMawasi', label: 'المواصي' },
                { value: 'alQarara', label: 'القرارة' },
                { value: 'eastKhanYounis', label: 'شرق خانيونس' },
                { value: 'downtown', label: 'وسط البلد' },
                { value: 'mirage', label: 'ميراج' },
                { value: 'european', label: 'الأوروبي' },
                { value: 'alFakhari', label: 'الفخاري' }
            ];

            khanYounisNeighborhoods.forEach(neighborhood => {
                const option = document.createElement('option');
                option.value = neighborhood.value;
                option.textContent = neighborhood.label;
                neighborhoodSelect.appendChild(option);
            });
        }
    });

    function validateCurrentCity() {
        const currentCityInput = document.getElementById('current_city');
        const errorMessage = document.getElementById('current_city_error');
        const value = currentCityInput.value.trim();
        console.log(value);

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
        const neighborhoodInput = document.getElementById('neighborhood');
        const errorMessage = document.getElementById('neighborhood_error');
        const value = neighborhoodInput.value.trim();
        console.log(value);

        // جلب القيم المسموحة من الخادم
        const validValues = @json(\App\Enums\Person\PersonNeighborhood::toValues());

        // إذا كان الحقل فارغًا
        if (value === '') {
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'هذا الحقل مطلوب.';
            neighborhoodInput.style.borderColor = 'red';
        }
        // إذا كانت القيمة غير صالحة
        else if (!validValues.includes(value)) {
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
            neighborhoodInput.style.borderColor = 'red';
        }
        // إذا كانت القيمة صالحة
        else {
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            neighborhoodInput.style.borderColor = ''; // إزالة لون الإطار
            return true;
        }
    }

    function validateHousingType() {
        const housingTypeInput = document.getElementById('housing_type');
        const errorMessage = document.getElementById('housing_type_error');
        const value = housingTypeInput.value.trim();
        console.log(value);

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

    function validateHousingDamageStatus() {
        const housingDamageStatusInput = document.getElementById('housing_damage_status');
        const errorMessage = document.getElementById('housing_damage_status_error');
        const value = housingDamageStatusInput.value.trim();
        console.log(value);

        // جلب القيم المسموحة من الخادم
        const validHousingDamageStatuses = @json(\App\Enums\Person\PersonDamageHousingStatus::toValues());

        // إذا كان الحقل فارغًا
        if (value === '') {
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'هذا الحقل مطلوب.';
            housingDamageStatusInput.style.borderColor = 'red';
        }
        // إذا كانت القيمة غير صالحة
        else if (!validHousingDamageStatuses.includes(value)) {
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'القيمة المدخلة غير صالحة.';
            housingDamageStatusInput.style.borderColor = 'red';
        }
        // إذا كانت القيمة صالحة
        else {
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            housingDamageStatusInput.style.borderColor = ''; // إزالة لون الإطار
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
        console.log(validateArabicInput('first_name'));
        if (!validateArabicInput('first_name')) {
            isValid = false;
            errorMessages.push({ field: 'first_name', message: 'الرجاء إدخال الاسم الأول.' });
        }
        console.log(validateArabicInput('father_name'));
        if (!validateArabicInput('father_name')) {
            isValid = false;
            errorMessages.push({ field: 'father_name', message: 'الرجاء إدخال اسم الأب.' });
        }
        console.log(validateArabicInput('grandfather_name'));
        if (!validateArabicInput('grandfather_name')) {
            isValid = false;
            errorMessages.push({ field: 'grandfather_name', message: 'الرجاء إدخال اسم الجد.' });
        }
        console.log(validateArabicInput('family_name'));
        if (!validateArabicInput('family_name')) {
            isValid = false;
            errorMessages.push({ field: 'family_name', message: 'الرجاء إدخال اسم العائلة.' });
        }
        console.log(validateGender());
        if (!validateGender()) {
            isValid = false;
            errorMessages.push({ field: 'gender', message: 'الرجاء إدخال الجنس.' });
        }
        console.log(validatedob());
        if (!validatedob()) {
            isValid = false;
            errorMessages.push({ field: 'dob', message: 'الرجاء إدخال تاريخ الميلاد.' });
        }
        console.log(validatePhoneInput());
        if (!validatePhoneInput()) {
            isValid = false;
            errorMessages.push({ field: 'phone', message: 'الرجاء إدخال رقم الهاتف.' });
        }
        console.log(validateSocialStatus());
        if (!validateSocialStatus()) {
            isValid = false;
            errorMessages.push({ field: 'social_status', message: 'الرجاء تحديد الحالة الاجتماعية.' });
        }
        console.log(validateEmploymentStatus());
        if (!validateEmploymentStatus()) {
            isValid = false;
            errorMessages.push({ field: 'employment_status', message: 'الرجاء تحديد حالة العمل.' });
        }
        console.log(validateConditionText());
        if (!validateConditionText()) {
            isValid = false;
            errorMessages.push({ field: 'condition_description', message: 'الرجاء وصف الحالة الصحية التي تعاني منها.' });
        }
        console.log(validateCity());
        if (!validateCity()) {
            isValid = false;
            errorMessages.push({ field: 'city', message: 'الرجاء إدخال المدينة.' });
        }
        console.log(validateCurrentCity());
        if (!validateCurrentCity()) {
            isValid = false;
            errorMessages.push({ field: 'current_city', message: 'الرجاء إدخال المدينة الحالية.' });
        }
        console.log(validateNeighborhood());
        if (!validateNeighborhood()) {
            isValid = false;
            errorMessages.push({ field: 'neighborhood', message: 'الرجاء إدخال الحي.' });
        }
        console.log(validateArabicInput('landmark'));
        if (!validateArabicInput('landmark')) {
            isValid = false;
            errorMessages.push({ field: 'landmark', message: 'الرجاء إدخال المعلم.' });
        }
        console.log(validateHousingType());
        if (!validateHousingType()) {
            isValid = false;
            errorMessages.push({ field: 'housing_type', message: 'الرجاء تحديد نوع السكن.' });
        }
        console.log(validateHousingDamageStatus());
        if (!validateHousingDamageStatus()) {
            isValid = false;
            errorMessages.push({ field: 'housing_damage_status', message: 'الرجاء تحديد حالة السكن.' });
        }
        const genderInput = document.getElementById('gender'); // إدخال الجنس
        const socialStatusInput = document.getElementById('social_status');
        if (genderInput.value === "أنثى" && (socialStatusInput.value === "married" || socialStatusInput.value === "polygamous")) {
            isValid = false;
            errorMessages.push({ field: 'social_status', message: 'يرجى التسجيل ببيانات الزوج حتى لو كان الزوج متزوج أكثر من زوجة.' });
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

    // تحديد الزر الذي سيتم الاستماع له
    document.getElementById('submit-button').addEventListener('click', function(e) {
        e.preventDefault();

        var data = {
            first_name:        document.getElementById('first_name').value,
            father_name:       document.getElementById('father_name').value,
            grandfather_name:  document.getElementById('grandfather_name').value,
            family_name:       document.getElementById('family_name').value,
            gender:            document.getElementById('gender').value,
            social_status:     document.getElementById('social_status').value
        };

        fetch('/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                first_name: document.getElementById('first_name').value,
                father_name: document.getElementById('father_name').value,
                grandfather_name: document.getElementById('grandfather_name').value,
                family_name: document.getElementById('family_name').value,
                gender: document.getElementById('gender').value,
                social_status: document.getElementById('social_status').value
            })
        })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('❌ خطأ في الطلب:', error));

    });

</script>
</body>
</html>
