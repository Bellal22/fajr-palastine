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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                        url({{asset('background/image.jpg')}}) center center no-repeat;
            background-size: cover;
            display: flex;
            padding: 20px;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            overflow: auto;
            position: relative;
        }

        h1 {
            color: #FF6F00;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1rem;
            margin-bottom: 2rem;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 15rem;
            height: auto;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .profile-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .profile-item {
            flex: 1;
            min-width: 200px;
        }

        label {
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .welcome-message {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .family-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: collapse;
        }

        .family-table th, .family-table td {
            padding: 1rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        .family-table th {
            background-color: #FF6F00;
            color: white;
            font-size: 1rem;
        }

        .family-table td {
            background-color: #f9f9f9;
            font-size: 1rem;
        }

        .complaints-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: collapse;
        }

        .complaints-table th, .complaints-table td {
            padding: 1rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        .complaints-table th {
            background-color: #FF6F00;
            color: white;
            font-size: 1rem;
        }

        .complaints-table td {
            background-color: #f9f9f9;
            font-size: 1rem;
        }

        .logout-btn-container {
            position: absolute;
            top: 1rem;
            left: 1rem;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            background-color: #f44336;
            color: white;
            padding: 0.6rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout-btn i {
            margin-right: 0.5rem;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        p {
            text-align: right;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .styled-text {
            font-weight: bold;
            font-size: 2rem;
            color: #FF6F00;
            text-align: right;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            padding: 1rem;
        }

        .edit-icon {
            margin-left: 1rem;
            font-size: 1rem;
            color: #FF6F00;
            text-decoration: none;
        }

        .edit-icon:hover {
            color: #FF6F00;
        }

        .add-icon {
            margin-left: 1rem;
            font-size: 1rem;
            color: #FF6F00;
            text-decoration: none;
        }

        .add-icon:hover {
            color: #FF6F00;
        }

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

        .popup-content {
            position: relative;
            width: 90%;
            max-width: 1000px;
            max-height: 80vh;
            overflow-y: auto;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
            width: 100%;
        }

        .form-group label {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        input, select {
            font-size: 1rem;
            padding: 0.8rem;
            width: 100%;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: space-between;
        }

        .row .form-group {
            flex: 1;
            min-width: 20%;
        }

        .row .form-group:nth-child(-n+3) {
            flex: 1;
            min-width: 20%;
        }

        .row .form-group label {
            text-align: right;
        }

        #condition_description {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .hidden {
            display: none;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .save-btn {
            background-color: #FF6F00;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
        }

        .save-btn:hover {
            background-color: #e65c00;
        }

        .error-message {
            color: #ff0000;
            font-size: 12px;
            margin-top: 0.5rem;
        }

        .success-message {
            color: #22b722;
            font-size: 12px;
            margin-top: 0.5rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .settings-icon-container {
            background: none;
            border: none;
            color: #FF6F00;
            cursor: pointer;
            font-size: 1rem;
        }

        .settings-icon-container:hover {
            color: #e65c00;
        }

        #settings-dropdown {
            display: none;
            position: absolute;
            top: 1rem;
            right: -8rem;
            background-color: white;
            color: orange;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 15rem;
            z-index: 300;
        }

        .settings-menu-btn {
            background: white;
            border: 1px solid #FF6F00;
            color: #FF6F00;
            font-size: 1rem;
            font-weight: bold;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .settings-menu-btn i {
            margin-bottom: 0.5rem;
        }

        .settings-menu-btn:hover {
            background-color: #FF6F00;
            color: white;
        }

        .password-requirements {
            font-size: 0.9rem;
            color: #000;
            margin-top: 0.2rem;
            line-height: 1.2;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .password-requirements span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .password-requirements span i {
            display: none;
        }

        .password-requirements span.valid {
            color: #17b517;
        }

        .password-requirements span.valid i {
            display: inline;
            color: #17b517;
        }

        .password-requirements span.invalid {
            color: red;
        }

        .password-requirements span.invalid i {
            display: inline;
            color: red;
        }

        .toggle-password {
            position: absolute;
            left: 1rem;
            top: 50%;
            cursor: pointer;
            color: #555;
            font-size: 1rem;
        }

        .error-message {
            color: #ff0000;
        }

        .success-message {
            color: #35b735;
        }

        #open-form {
            margin-left: 1rem;
            font-size: 1.3rem;
            color: #FF6F00;
            text-decoration: none;
        }

        #close-popup {
             margin-left: 1rem;
            font-size: 1.3rem;
            color: #000000;
            text-decoration: none;
        }

        #form-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 1000px;
            max-height: 80vh;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            overflow-y: auto;
        }

        .hidden {
            display: none;
        }

        .overlay-class {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* خلفية معتمة */
            z-index: 1000; /* تأكد من أنه فوق العناصر الأخرى */
        }

        button {
            padding: 8px 15px;
            font-size: 1rem;
            cursor: pointer;
        }

        #close-popup-btn {
            background-color: #E65100;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        #close-popup-btn:hover {
            background-color: #C41C00;
        }

        #add-person-btn {
            background-color: #FF6F00;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
        }

        #add-person-btn:hover {
            background-color: #E65100;
        }

        .area-responsible-container {
            display: flex;
            flex-direction: column; /* ترتيب العناصر عموديًا */
        }

        .area-responsible-container label.form-label {
            order: 1;
            margin-bottom: 0.5rem; /* مساحة بين الـ label والـ select */
        }

        .area-responsible-container select.form-control {
            order: 2;
        }

        .area-responsible-container .error-message {
            order: 3; /* خلي رسالة الخطأ تالت عنصر (تحت الـ select) */
            margin-top: 0.5rem; /* مساحة بين الـ select ورسالة الخطأ */
        }

        /* عند إخفاء الحقل، حافظ على ظهور رسالة الخطأ في مكانها (لكنها هتكون مخفية) */
        #areaResponsibleField[style*="display:none"] .error-message {
            display: block !important;
            visibility: hidden;
        }

        #areaResponsibleField[style*="display:none"] label.form-label,
        #areaResponsibleField[style*="display:none"] select.form-control {
            display: none !important;
        }

        /* Media Queries */
        @media screen and (max-width: 768px) {
            h1 {
                font-size: 1.2rem;
            }

            .container {
                padding: 1rem;
            }

            .logo {
                width: 10rem;
            }

            .profile-item {
                min-width: 100%;
            }

            .family-table th, .family-table td {
                font-size: 0.9rem;
            }

            .popup-content {
                width: 90%;
                padding: 1rem;
            }

            .row .form-group {
                flex: 1;
                min-width: 45%;
            }

            .row .form-group:nth-child(-n+3) {
                flex: 1;
                min-width: 45%;
            }

            .settings-menu-btn {
                font-size: 0.9rem;
            }
        }

    </style>
</head>
<body>

    <div class="container">

        <div class="logo-container">
            <img src="{{asset('background/image.jpg')}}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>

        <h1>
            مرحباً، {{ $person->first_name }} {{ $person->family_name }} 👋🏼!

            <div class="relative inline-block text-right">
                <button id="settings-toggle" class="settings-icon-container">
                    <i class="fa fa-cog text-2xl" id="settings-icon"></i>
                </button>

                <!-- قائمة الإعدادات التي تظهر عند الضغط -->
                <div id="settings-dropdown" class="hidden absolute mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
                    <ul class="flex flex-col text-center space-y-2 p-3">
                        <li>
                            <button id="change-password-btn" class="settings-menu-btn">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-lock" style="font-size: 16px;"></i>
                                    <span style="font-size: 16px;">تعديل كلمة المرور</span>
                                </div>
                            </button>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" class="settings-menu-btn">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-sign-out-alt text-2xl mb-1" style="font-size: 16px;"></i>
                                    <span class="text-lg" style="font-size: 16px;">تسجيل الخروج</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </h1>

        <!-- بوب أب تعديل كلمة المرور -->
        <div class="popup hidden" id="password-popup">
            <div class="popup-content">
                <span class="close" onclick="closepasswordpopup()">&times;</span>
                <h1>تعديل كلمة المرور</h1>
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <!-- كلمة المرور القديمة -->
                <div class="form-group">
                    <div class="form-group" style="position: relative; display: inline-block; width: 100%;">
                        <label for="old-password">كلمة المرور القديمة</label>
                        <input type="password" id="old-password" name="passkey" required
                            style="width: 100%; padding: 10px; padding-left: 40px; font-size: 16px; box-sizing: border-box;">
                        <i class="fa fa-eye toggle-password" data-target="old-password"
                            style="position: absolute; left: 10px; top: 50%; bottom: 50%; transform: translateY(-50%); cursor: pointer; color: #555; font-size: 16px;">
                        </i>
                    </div>
                </div>

                <!-- كلمة المرور الجديدة -->
                <div class="form-group">
                    <div class="form-group" style="position: relative; display: inline-block; width: 100%;">
                        <label for="new-password">كلمة المرور الجديدة</label>
                        <input type="password" id="new-password" name="new_passkey" required
                            style="width: 100%; padding: 10px; padding-left: 40px; font-size: 16px; box-sizing: border-box;">
                        <i class="fa fa-eye toggle-password" data-target="new-password"
                            style="position: absolute; left: 10px; top: 50%; bottom: 50%; transform: translateY(-50%); cursor: pointer; color: #555; font-size: 16px;">
                        </i>
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
                    <div class="form-group" style="position: relative; display: inline-block; width: 100%;">
                        <label for="confirm-password">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" id="confirm-password" name="confirm_passkey" required
                            style="width: 100%; padding: 10px; padding-left: 40px; font-size: 16px; box-sizing: border-box;">
                        <i class="fa fa-eye toggle-password" data-target="confirm-password"
                            style="position: absolute; left: 10px; top: 50%; bottom: 50%; transform: translateY(-50%); cursor: pointer; color: #555; font-size: 16px;">
                        </i>
                    </div>
                    <div class="password-requirements">
                        <span id="match-check">تطابق كلمة المرور <i>✔</i></span>
                    </div>
                </div>

                <button class="save-btn" onclick="saveChangesPassword()">حفظ التغييرات</button>
                <div class="error-message hidden" id="password-error"></div>
            </div>
        </div>

        <h1 style="text-align: right">
            البيانات الشخصية:
            <a href="#" class="edit-icon" onclick="openPopup()">
                <i class="fas fa-edit"></i>
            </a>
        </h1>


        <div class="profile-container">
            <!-- الاسم الأول، الأب، الجد، العائلة -->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="first_name">الاسم الأول:</label>
                    <input type="text" id="first_name" value="{{ $person->first_name }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="father_name">اسم الأب:</label>
                    <input type="text" id="father_name" value="{{ $person->father_name }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="grandfather_name">اسم الجد:</label>
                    <input type="text" id="grandfather_name" value="{{ $person->grandfather_name }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="family_name">اسم العائلة:</label>
                    <input type="text" id="family_name" value="{{ $person->family_name }}" disabled>
                </div>
            </div>

            <!-- رقم الهوية، تاريخ الميلاد، رقم الجوال -->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="id_num">رقم الهوية:</label>
                    <input type="text" id="id_num" value="{{ $person->id_num }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="dob">تاريخ الميلاد:</label>
                    <input type="text" id="dob" value="{{ $person->dob ? $person->dob->format('d/m/Y') : 'غير محدد' }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="phone">رقم الجوال:</label>
                    <input type="text" id="phone" value="{{ $person->phone }}" disabled>
                </div>
            </div>

            <!-- الحالة الاجتماعية، عدد أفراد الأسرة، حالة العمل-->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="social_status">الحالة الاجتماعية:</label>
                    <input type="text" id="social_status" value="{{ $person->social_status ? __($person->social_status) : 'غير محدد' }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="family_members">عدد أفراد الأسرة:</label>
                    <input type="text" id="family_members" value="{{ $person->relatives_count}}" disabled>
                </div>
                <div class="profile-item">
                    <label for="employment_status"> حالة العمل:</label>
                    <input type="text" id="employment_status" value="{{ $person->employment_status}}" disabled>
                </div>
            </div>

            <!-- الحالة الصحية -->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="has_condition">هل يعاني من حالة صحية؟</label>
                    <input type="text" id="has_condition"
                        value="{{ $person->has_condition ? 'نعم' : 'لا' }}"
                        disabled>
                </div>
            </div>

            @if ($person->has_condition)
                <div class="profile-row">
                    <div class="profile-item">
                        <label for="condition_description">وصف الحالة الصحية:</label>
                        <textarea id="condition_description" disabled>{{ $person->condition_description }}</textarea>
                    </div>
                </div>
            @endif

            <!-- المحافظة الأصلية، حالة المسكن السابق -->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="city">المحافظة الأصلية:</label>
                    <input type="text" id="city" value="{{ $person->city ? __($person->city) : 'غير محدد' }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="housing_damage_status">حالة السكن السابق:</label>
                    <input type="text" id="housing_damage_status" value="{{ $person->housing_damage_status ? __($person->housing_damage_status) : 'غير محدد' }}" disabled>
                </div>
            </div>

            <!-- المحافظة الحالية، نوع السكن الحالي، الحي السكني الحالي، أقرب معلم -->
            <div class="profile-row">
                <div class="profile-item">
                    <label for="current_city">المحافظة الحالية:</label>
                    <input type="text" id="current_city" value="{{ $person->current_city ? __($person->current_city) : 'غير محدد' }}" disabled>
                </div>
                <div class="profile-item">
                    <label for="housing_type">نوع السكن الحالي:</label>
                    <input type="text" id="housing_type" value="{{ $person->housing_type ? __($person->housing_type) : 'غير محدد' }}" disabled>
                </div>
            </div>

            <div class="profile-row">
                <div class="profile-item">
                    <label for="neighborhood">الحي السكني الحالي:</label>
                    <input type="text" id="neighborhood" value="{{ $person->neighborhood ? __($person->neighborhood) : 'غير محدد' }}" disabled>
                </div>
                @if($person->areaResponsible)
                    <div class="profile-item">
                        <label for="area_responsible_id">مسؤول المنطقة في المواصي:</label>
                        <input type="text" id="area_responsible_id" value="{{ $person->areaResponsible ? $person->areaResponsible->name : 'غير محدد' }}" disabled>
                    </div>
                @endif
                <div class="profile-item">
                    <label for="landmark">أقرب معلم:</label>
                    <input type="text" id="landmark" value="{{ $person->landmark ?? 'غير محدد' }}" disabled>
                </div>
            </div>
        </div>

        <!-- تعديل البيانات الشخصية -->
        <div id="editPopup" class="popup hidden">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <h1 style="text-align: center">تعديل البيانات الشخصية</h1>

                <div class="row">
                    <div class="form-group">
                        <label for="first_name">الاسم الأول</label>
                        <input
                            type="text"
                            id="edit_first_name"
                            name="first_name"
                            placeholder="الاسم الأول"
                            value="{{ $person->first_name }}"
                            oninput="validateArabicInput('edit_first_name')"
                            onfocus="resetBorderAndError('edit_first_name')"
                            onblur="validateArabicInput('edit_first_name')"
                            required>
                        <div class="error-message" id="edit_first_name_error" style="display:none; color: red;"></div>
                    </div>
                    <div class="form-group">
                        <label for="father_name">اسم الأب</label>
                        <input
                            type="text"
                            id="edit_father_name"
                            name="father_name"
                            value="{{ $person->father_name }}"
                            placeholder="اسم الأب"
                            oninput="validateArabicInput('edit_father_name')"
                            onfocus="resetBorderAndError('edit_father_name')"
                            onblur="validateArabicInput('edit_father_name')"
                            required>
                        <div class="error-message" id="edit_father_name_error" style="display:none; color: red;"></div>
                    </div>
                    <div class="form-group">
                        <label for="grandfather_name">اسم الجد</label>
                        <input
                            type="text"
                            id="edit_grandfather_name"
                            name="grandfather_name"
                            value="{{ $person->grandfather_name }}"
                            placeholder="اسم الجد"
                            oninput="validateArabicInput('edit_grandfather_name')"
                            onfocus="resetBorderAndError('grandfather_name')"
                            onblur="validateArabicInput('edit_grandfather_name')"
                            required>
                        <div class="error-message" id="edit_grandfather_name_error" style="display:none; color: red;"></div>
                    </div>
                    <div class="form-group">
                        <label for="family_name">اسم العائلة</label>
                        <input
                            type="text"
                            id="edit_family_name"
                            name="family_name"
                            value="{{ $person->family_name }}"
                            placeholder="اسم العائلة"
                            oninput="validateArabicInput('edit_family_name')"
                            onfocus="resetBorderAndError('edit_family_name')"
                            onblur="validateArabicInput('edit_family_name')"
                            required>
                        <div class="error-message" id="edit_family_name_error" style="display:none; color: red;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="id_num">رقم الهوية</label>
                        <input type="number" id="edit_id_num" name="id_num" value="{{ $person->id_num }}" required oninput="validateIdOnInput()" maxlength="9" >
                        <span id="edit_id_num_error" class="error-message" style="display:none;">رقم الهوية غير صالح.</span>
                        <span id="edit_id_num_success" class="success-message" style="display:none;">رقم الهوية صحيح.</span>
                    </div>

                    <div class="form-group">
                        <label for="edit_gender">الجنس</label>
                        <select id="edit_gender" name="gender" required
                                oninput="validateEditGender()"
                                onfocus="resetBorderAndError('edit_gender')"
                                onblur="validateEditGender()">
                            <option value="">اختر الجنس</option>
                            @foreach(['ذكر' => 'ذكر', 'أنثى' => 'أنثى', 'غير محدد' => 'غير محدد'] as $key => $gender)
                                <option value="{{ $key }}" {{ $person->gender == $key ? 'selected' : '' }}>{{ $gender }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_gender_error" style="color: red;"></div>
                    </div>

                    <div class="form-group">
                        <label for="dob">تاريخ الميلاد</label>
                        <input
                            type="date"
                            id="edit_dob"
                            name="dob"
                            value="{{ $person->dob ? \Carbon\Carbon::parse($person->dob)->format('Y-m-d') : '' }}"
                            oninput="validatedob()"
                            onfocus="resetBorderAndError('edit_dob')"
                            onblur="validatedob()"
                            required>
                        <div class="error-message" id="edit_dob_error" style="display:none; color: red;"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الجوال</label>
                        <input
                            type="text"
                            class="text-left"
                            dir="ltr"
                            placeholder="059-123-1234 or 056-123-1234"
                            id="edit_phone"
                            name="phone"
                            value="{{ $person->phone }}"
                            oninput="validatePhoneInput()"
                            onfocus="resetBorderAndError('edit_phone')"
                            onblur="validatePhoneInput()"
                            required>
                        <div class="error-message" id="edit_phone_error" style="display: none; color: red;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="social_status">الحالة الاجتماعية</label>
                        <select id="edit_social_status" name="social_status" required oninput="validateSocialStatus()" onfocus="resetBorderAndError('edit_social_status')" onblur="validateSocialStatus()">
                            <option value="">اختر الحالة</option>
                            @foreach($social_statuses as $key => $status)
                                <option value="{{ $key }}" {{ $person->social_status == $key ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_social_status_error" style="color: red;"></div>
                    </div>
                    <div class="form-group">
                        <label for="relatives_count">عدد أفراد الأسرة</label>
                        <input
                            type="text"
                            id="edit_relatives_count"
                            name="relatives_count"
                            placeholder="عدد أفراد الأسرة"
                            value="{{ $person->relatives_count }}"
                            oninput="validaterelatives_countInput('edit_relatives_count')"
                            onfocus="resetBorderAndError('edit_relatives_count')"
                            onblur="validaterelativesCountInput('edit_relatives_count')"
                            required>
                        <div class="error-message" id="edit_relatives_count_error" style="display:none; color: red;"></div>
                    </div>

                    <div class="form-group">
                        <label for="employment_status">حالة العمل</label>
                        <select id="edit_employment_status" name="employment_status" required
                            oninput="validateEmploymentStatus()"
                            onfocus="resetBorderAndError('edit_employment_status')"
                            onblur="validateEmploymentStatus()">
                                <option value="لا يعمل" {{ old('employment_status') == 'لا يعمل' ? 'selected' : '' }}>لا يعمل</option>
                                <option value="موظف" {{ old('employment_status') == 'موظف' ? 'selected' : '' }}>موظف</option>
                                <option value="عامل" {{ old('employment_status') == 'عامل' ? 'selected' : '' }}>عامل</option>
                        </select>
                        <div class="error-message" id="edit_employment_status_error" style="display: none; color: red;"></div>
                    </div>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label for="has_condition">هل لديك حالة صحية؟</label>
                    <select id="edit_has_condition" name="has_condition"
                        onchange="toggleConditionDescription()">
                            <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                            <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                    </select>
                </div>

                <div class="form-group" id="edit_condition_description_group" style="display: none;">
                    <label for="condition_description">وصف الحالة الصحية</label>
                    <textarea
                        id="edit_condition_description"
                        name="condition_description"
                        rows="4"
                        cols="50"
                        value="{{ $person->condition_description }}"
                        oninput="validateConditionText()"
                        onfocus="resetBorderAndError('edit_condition_description')"
                        onblur="validateConditionText()"></textarea>
                    <div class="error-message" id="edit_condition_description_error" style="display: none; color: red;"></div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="city">المحافظة الأصلية</label>
                        <select id="edit_city" name="city" required
                            oninput="validateCity()"
                            onfocus="resetBorderAndError('edit_city')"
                            onblur="validateCity()">
                                <option value="">اختر المحافظةالأصلية</option>
                                @foreach($cities as $key => $city)
                                    <option value="{{ $key }}" {{ $person->city == $key ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                        </select>
                        <div class="error-message" id="edit_city_error" style="color: red;"></div>
                    </div>
                    <div class="form-group">
                        <label for="housing_damage_status">حالة السكن السابق</label>
                        <select id="edit_housing_damage_status" name="housing_damage_status" required
                            oninput="validateHousingDamageStatus()"
                            onfocus="resetBorderAndError('edit_housing_damage_status')"
                            onblur="validateHousingDamageStatus()">
                                <option value="">اختر حالة السكن السابق</option>
                                @foreach($housing_damage_statuses as $key => $status)
                                    <option value="{{ $key }}" {{ $person->housing_damage_status == $key ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                        </select>
                        <div class="error-message" id="edit_housing_damage_status_error" style="color: red;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="current_city">المحافظة الحالية</label>
                        <select id="edit_current_city" name="current_city" required
                            oninput="validateCurrentCity()"
                            onfocus="resetBorderAndError('edit_current_city')"
                            onblur="validateCurrentCity()"
                            onchange="updateNeighborhoods(this.value, '{{ $person->neighborhood }}')">
                            <option value="">اختر المحافظة الحالية</option>
                            @foreach($current_cities as $key => $city)
                                <option value="{{ $key }}" {{ $person->current_city == $key ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_current_city_error" style="color: red;"></div>
                    </div>

                    <div class="form-group">
                        <label for="housing_type">نوع السكن الحالي</label>
                        <select id="edit_housing_type" name="housing_type" required
                        oninput="validateHousingType()"
                        onfocus="resetBorderAndError('edit_housing_type')"
                        onblur="validateHousingType()">
                            <option value="">اختر نوع السكن الحالي</option>
                            @foreach($housing_types as $key => $type)
                                <option value="{{ $key }}" {{ $person->housing_type == $key ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_housing_type_error" style="color: red;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group" id="neighborhoodField">
                        <label for="edit_neighborhood">الحي السكني الحالي</label>
                        <select id="edit_neighborhood" name="neighborhood" required
                                oninput="validateNeighborhood()"
                                onfocus="resetBorderAndError('edit_neighborhood')"
                                onblur="validateNeighborhood()">
                            <option value="">اختر الحي السكني الحالي</option>
                            @foreach($neighborhoods as $key => $neighborhood)
                                <option value="{{ $key }}" {{ $person->neighborhood == $key ? 'selected' : '' }}>{{ $neighborhood }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_neighborhood_error" style="display:none; color: red;"></div>
                    </div>

                    <div class="form-group area-responsible-container" id="areaResponsibleField" style="display:none;">
                        <label for="edit_area_responsible_id">مسؤول المنطقة في المواصي</label>
                        <select class="form-control"
                                id="edit_area_responsible_id"
                                name="area_responsible_id"
                                oninput="validateAreaResponsible()"
                                onfocus="resetBorderAndError('edit_area_responsible_id')"
                                onblur="validateAreaResponsible()">
                            <option value="">اختر المسؤول</option>
                            @foreach (\App\Models\AreaResponsible::all() as $responsible)
                                <option value="{{ $responsible->id }}" {{ $person->area_responsible_id == $responsible->id ? 'selected' : '' }}>
                                    {{ $responsible->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="error-message" id="edit_area_responsible_id_error" style="color: red; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="landmark">أقرب معلم</label>
                        <input
                            type="text"
                            id="edit_landmark"
                            name="landmark"
                            placeholder="أقرب معلم"
                            value="{{ $person->landmark }}"
                            oninput="validateArabicInput('edit_landmark')"
                            onfocus="resetBorderAndError('edit_landmark')"
                            onblur="validateArabicInput('edit_landmark')">
                        <div class="error-message" id="edit_landmark_error" style="display:none; color: red;"></div>
                    </div>
                </div>

                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!-- زر حفظ التعديلات -->
                <button class="save-btn" onclick="saveChangesParent()">حفظ التغييرات</button>
            </div>
        </div>

        <!-- بيانات أفراد الأسرة -->
        {{--هاد الفورم لإضافة فرد جديد على العائلة مفروض يضيف على الداتا بيز --}}
        <h1 style="text-align: right">بيانات أفراد الأسرة:
            <a href="#" id="open-form" class="add-icon">
                <i class="fas fa-plus"></i>
            </a>
        </h1>

        <div id="form-popup" class="hidden">
            <div>
                <span class="close" id="closse-popup">&times;</span>
                <div id="overlay" class="overlay-class hidden"></div>
                <h1>إضافة بيانات فرد</h1>

                    <input type="hidden" id="familyMemberId" name="id">

                    <div class="row">
                        <div class="form-group">
                            <label for="first_name">الاسم الأول:</label>
                            <input
                                type="text"
                                id="first_namef"
                                name="first_namef"
                                oninput="validateArabicInput('first_namef')"
                                onfocus="resetBorderAndError('first_namef')"
                                onblur="validateArabicInput('first_namef')"
                                required>
                            <div class="error-message" id="first_namef_error" style="display:none; color: red;"></div>
                        </div>

                        <div class="form-group">
                            <label for="father_name">اسم الأب:</label>
                            <input
                                type="text"
                                id="father_namef"
                                name="father_namef"
                                oninput="validateArabicInput('father_namef')"
                                onfocus="resetBorderAndError('father_namef')"
                                onblur="validateArabicInput('father_namef')"
                                required>
                            <div class="error-message" id="father_namef_error" style="display:none; color: red;"></div>
                        </div>

                        <div class="form-group">
                            <label for="grandfather_name">اسم الجد:</label>
                            <input
                                type="text"
                                id="grandfather_namef"
                                name="grandfather_namef"
                                oninput="validateArabicInput('grandfather_namef')"
                                onfocus="resetBorderAndError('grandfather_namef')"
                                onblur="validateArabicInput('grandfather_namef')"
                                required>
                            <div class="error-message" id="grandfather_namef_error" style="display:none; color: red;"></div>
                        </div>

                        <div class="form-group">
                            <label for="family_name">اسم العائلة:</label>
                            <input
                                type="text"
                                id="family_namef"
                                name="family_namef"
                                oninput="validateArabicInput('family_namef')"
                                onfocus="resetBorderAndError('family_namef')"
                                onblur="validateArabicInput('family_namef')"
                                required>
                            <div class="error-message" id="family_namef_error" style="display:none; color: red;"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="id_num">رقم الهوية:</label>
                            <input type="number" id="id_numf" name="id_numf" placeholder="أدخل رقم الهوية" required
                                oninput="validateIdOnInputid()" onfocus="resetBorderAndError('id_numf')" maxlength="9" pattern="\d{9}">
                            <span id="id_numf_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                            <span id="id_numf_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
                        </div>

                        <div class="form-group">
                            <label for="dob">تاريخ الميلاد:</label>
                            <input
                                type="date"
                                id="dobf"
                                name="dobf"
                                oninput="validatedob()"
                                onfocus="resetBorderAndError('dobf')"
                                onblur="validatedob()"
                                required>
                            <div class="error-message" id="dobf_error" style="display:none; color: red;"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="relationship">صلة القرابة</label>
                        <select id="relationshipf" name="relationshipf" required>
                            @foreach($relationships as $key => $relationship)
                                <option value="{{$key}}">{{$relationship}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <label for="has_condition">هل لديك حالة صحية؟</label>
                        <select id="has_conditionf" name="has_conditionf" onchange="toggleConditionText()">
                            <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                            <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                        </select>
                    </div>

                    <div class="form-group" id="condition_description_group" style="display: none;">
                        <label for="condition_description">وصف الحالة الصحية</label>
                        <textarea
                            id="condition_descriptionf"
                            name="condition_descriptionf"
                            rows="4"
                            cols="50"
                            value=""
                            oninput="validateConditionText()"
                            onfocus="resetBorderAndError('condition_descriptionf')"
                            onblur="validateConditionText()"></textarea>
                        <div class="error-message" id="condition_descriptionf_error" style="display: none; color: red;"></div>
                    </div>

                <button class="save-btn" type="button" id="add-person-btn">حفظ التغييرات</button>
            </div>
        </div>

        <!-- جدول بيانات أفراد الأسرة -->
        <table class="family-table">
            <thead>
                <tr>
                    <th class="border px-4 py-2">رقم الهوية</th>
                    <th class="border px-4 py-2"> الاسم الأول</th>
                    <th class="border px-4 py-2"> اسم الأب</th>
                    <th class="border px-4 py-2"> اسم الجد</th>
                    <th class="border px-4 py-2"> اسم العائلة</th>
                    <th class="border px-4 py-2">صلة القرابة</th>
                    <th class="border px-4 py-2">تاريخ الميلاد</th>
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
        </table>

        <!-- تعديل بيانات أفراد الأسرة -->
        <div id="editFamilyMemberModal" class="popup hidden">
            <div class="popup-content">
                <span class="close" onclick="closeFamilyPopup()">&times;</span>
                <h1>تعديل بيانات فرد من العائلة</h1>

                    <input type="hidden" id="familyMemberId" name="id">

                    <div class="row">
                        <div class="form-group">
                            <label for="first_name">الاسم الأول:</label>
                            <input type="text" id="edit_f_first_name" name="first_name" required>
                        </div>

                        <div class="form-group">
                            <label for="father_name">اسم الأب:</label>
                            <input type="text" id="edit_f_father_name" name="father_name" required>
                        </div>

                        <div class="form-group">
                            <label for="grandfather_name">اسم الجد:</label>
                            <input type="text" id="edit_f_grandfather_name" name="grandfather_name" required>
                        </div>

                        <div class="form-group">
                            <label for="family_name">اسم العائلة:</label>
                            <input type="text" id="edit_f_family_name" name="family_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="id_num">رقم الهوية:</label>
                            <input type="number" id="edit_f_id_num" name="id_num" placeholder="أدخل رقم الهوية" required>
                        </div>

                        <div class="form-group">
                            <label for="dob">تاريخ الميلاد:</label>
                            <input type="date" id="edit_f_dob" name="dob" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="relationship">صلة القرابة</label>
                        <select id="edit_f_relationship" name="relationship" required>
                            @foreach($relationships as $key => $relationship)
                                <option value="{{$key}}">{{$relationship}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <label for="has_condition">هل لديك حالة صحية؟</label>
                        <select id="edit_f_has_condition" name="has_condition" onchange="toggleConditionText()">
                            <option value="0" {{ old('has_condition') == '0' ? 'selected' : '' }}>لا</option>
                            <option value="1" {{ old('has_condition') == '1' ? 'selected' : '' }}>نعم</option>
                        </select>
                    </div>

                    <div class="form-group" id="edit_f_condition_description_group" style="display: none;">
                        <label for="condition_description">وصف الحالة الصحية</label>
                        <textarea
                            id="edit_f_condition_description"
                            name="condition_description"
                            rows="4"
                            cols="50"
                            value="{{ $person->condition_description }}"
                            oninput="validateConditionText()"
                            onfocus="resetBorderAndError('edit_condition_description')"
                            onblur="validateConditionText()"></textarea>
                        <div class="error-message" id="edit_condition_description_error" style="display: none; color: red;"></div>
                    </div>
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <button class="save-btn" onclick="saveChangesChild()">حفظ التغييرات</button>
            </div>
        </div>

        <h1 style="text-align: right">قائمة الشكاوى:
            {{-- <a href="{{ route('complaints.create') }}" id="open-complaint-form" class="add-icon">
                <i class="fas fa-plus"></i>
            </a> --}}
        </h1>

        <table class="complaints-table">
            <thead>
                <tr>
                    {{-- <th class="border px-4 py-2">رقم الهوية</th> --}}
                    <th class="border px-4 py-2">عنوان الشكوى</th>
                    <th class="border px-4 py-2">نص الشكوى</th>
                    <th class="border px-4 py-2">حالة الشكوى</th>
                    <th class="border px-4 py-2">تاريخ الإنشاء</th>
                    {{-- <th class="border px-4 py-2">الإجراء</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                    <tr>
                        {{-- <td>{{ $complaint->id_num }}</td> --}}
                        <td>{{ $complaint->complaint_title }}</td>
                        <td>{{ $complaint->complaint_text }}</td>
                        <td>{{ $complaint->status }}</td>
                        <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                        {{-- <td>
                            <a href="#" class="edit-icon" onclick="editComplaint({{ $complaint->id }})">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="#" class="delete-icon" onclick="deleteComplaint({{ $complaint->id }})">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>

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
                resetValidationStyles();
            };

            $('#open-form').click(function(event) {
                event.preventDefault();
                $('#form-popup').show(); // استخدم show() بدلاً من removeClass('hidden') مؤقتًا
            });

            // إغلاق بوب أب إضافة فرد جديد
            $('#closse-popup').click(function() {
                $('#form-popup').hide(); // استخدم hide() بدلاً من addClass('hidden') مؤقتًا
            });

            // إضافة فرد جديد إلى قاعدة البيانات
            $('#add-person-btn').click(function() {
                const id_num = $('#id_numf').val();
                const first_name = $('#first_namef').val();
                const father_name = $('#father_namef').val();
                const grandfather_name = $('#grandfather_namef').val();
                const family_name = $('#family_namef').val();
                const dob = $('#dobf').val();
                const relationship = $('#relationshipf').val();
                const has_condition = $('#has_conditionf').val();
                const condition_description = has_condition == '1' ? $('#condition_descriptionf').val() : null;

                // التحقق من الحقول المطلوبة
                if (!id_num || !first_name || !father_name || !grandfather_name || !family_name || !dob || !relationship) {
                    showAlert('يرجى ملء جميع الحقول المطلوبة!', 'error');
                    return;
                }

                // إرسال البيانات مباشرة إلى السيرفر
                $.ajax({
                    url: '{{ route("persons.addFamily") }}', // تأكد من أن هذا المسار صحيح في Laravel
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
                        condition_description: condition_description
                    },
                    success: function(response) {
                        showAlert('تمت إضافة الفرد بنجاح', 'success');

                        // تفريغ الحقول بعد الإضافة
                        $('#form-popup').find('input, select, textarea').val('');
                        $('#form-popup').hide();

                        // تحديث الصفحة لرؤية البيانات الجديدة (يمكن استبداله بتحديث جزء معين من الصفحة)
                        location.reload();
                    },
                    error: function(xhr) {
                        showAlert('حدث خطأ أثناء الإرسال!', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // دالة عرض التنبيهات
            function showAlert(message, type) {
                Swal.fire({
                    icon: type,
                    title: message,
                    showConfirmButton: true,
                    confirmButtonText: 'إغلاق'
                });
            }

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
        document.getElementById('password-form').addEventListener('submit', function(event) {
            const oldPassword = document.getElementById('old-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                document.getElementById('password-error').textContent = 'كلمة المرور الجديدة غير متطابقة';
                document.getElementById('password-error').classList.remove('hidden');
            }
        });
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


        function saveChangesParent() {
            console.log("✅ الدالة saveChangesParent تعمل!");

            let formData = {
                first_name: document.getElementById('edit_first_name').value.trim(),
                father_name: document.getElementById('edit_father_name').value.trim(),
                grandfather_name: document.getElementById('edit_grandfather_name').value.trim(),
                family_name: document.getElementById('edit_family_name').value.trim(),
                id_num: document.getElementById('edit_id_num').value.trim(),
                dob: document.getElementById('edit_dob').value.trim(),
                phone: document.getElementById('edit_phone').value.trim(),
                social_status: document.getElementById('edit_social_status').value.trim(),
                relatives_count: document.getElementById('edit_relatives_count').value.trim(),
                employment_status: document.getElementById('edit_employment_status').value.trim(),
                has_condition: document.getElementById('edit_has_condition').value.trim(),
                condition_description: document.getElementById('edit_condition_description').value.trim(),
                city: document.getElementById('edit_city').value.trim(),
                housing_damage_status: document.getElementById('edit_housing_damage_status').value.trim(),
                current_city: document.getElementById('edit_current_city').value.trim(),
                housing_type: document.getElementById('edit_housing_type').value.trim(),
                neighborhood: document.getElementById('edit_neighborhood').value.trim(),
                area_responsible_id: document.getElementById('edit_area_responsible_id').value.trim(),
                landmark: document.getElementById('edit_landmark').value.trim()
            };

            // التحقق من القيم المطلوبة
            for (let key in formData) {
                if (!formData[key] && key !== 'condition_description') {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: `يرجى ملء جميع الحقول المطلوبة (${key})`,
                        icon: 'warning',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }
            }

            // إرسال البيانات إلى الخادم
            fetch('/update-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log("📌 استجابة السيرفر:", data);
                if (data.success) {
                    Swal.fire({
                        title: 'تم التحديث بنجاح!',
                        text: 'تم تعديل بياناتك الشخصية.',
                        icon: 'success',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        closePopup(); // ✅ إغلاق النافذة
                        location.reload(); // ✅ إعادة تحميل الصفحة
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التحديث، الرجاء المحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(error => {
                console.error("❌ خطأ في جلب البيانات:", error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'تعذر الاتصال بالخادم، يرجى التحقق من الاتصال بالإنترنت.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'حسناً'
                });
            });
        }

        function saveChangesChild() {
            console.log("✅ الدالة saveChangesChild تعمل!");

            let familyMemberId = document.getElementById('familyMemberId');
            if (!familyMemberId) {
                console.error("❌ خطأ: العنصر familyMemberId غير موجود في الصفحة!");
                return;
            }

            let hasConditionElement = document.getElementById('edit_f_has_condition');
            let conditionDescriptionElement = document.getElementById('edit_f_condition_description');

            let formData = {
                id: familyMemberId.value.trim(),
                first_name: document.getElementById('edit_f_first_name')?.value.trim() || "",
                father_name: document.getElementById('edit_f_father_name')?.value.trim() || "",
                grandfather_name: document.getElementById('edit_f_grandfather_name')?.value.trim() || "",
                family_name: document.getElementById('edit_f_family_name')?.value.trim() || "",
                id_num: document.getElementById('edit_f_id_num')?.value.trim() || "",
                dob: document.getElementById('edit_f_dob')?.value.trim() || "",
                relationship: document.getElementById('edit_f_relationship')?.value.trim() || "",
                has_condition: hasConditionElement?.value.trim() || "",
                condition_description: conditionDescriptionElement?.value.trim() || ""
            };

            // إذا كان المستخدم قد اختار "لا"، قم بتفريغ حقل الوصف
            if (formData.has_condition === "لا" || formData.has_condition === "0") {
                formData.has_condition = 0; // تأكد من إرسالها كعدد
                formData.condition_description = null; // أرسل null بدلًا من نص فارغ
                if (conditionDescriptionElement) {
                    conditionDescriptionElement.value = ""; // تفريغ الحقل في الفورم
                }
            }

            console.log("📌 البيانات المُرسلة:", formData);

            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error("❌ خطأ: CSRF Token غير موجود في الصفحة!");
                return;
            }

            fetch('/update-family-member', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log("📌 استجابة السيرفر:", data);
                if (data.success) {
                    Swal.fire({
                        title: 'تم التحديث بنجاح!',
                        text: 'تم تعديل بيانات فرد العائلة.',
                        icon: 'success',
                        confirmButtonColor: '#FF6F00',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        closeFamilyPopup(); // ✅ إغلاق الفورم
                        location.reload(); // ✅ إعادة تحميل الصفحة
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التحديث، الرجاء المحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(error => {
                console.error("❌ خطأ في جلب البيانات:", error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'تعذر الاتصال بالخادم، يرجى التحقق من الاتصال بالإنترنت.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'حسناً'
                });
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

        document.addEventListener("DOMContentLoaded", function () {
            console.log("DOMContentLoaded event fired");

            const currentCitySelect = document.getElementById("edit_current_city");
            const neighborhoodSelect = document.getElementById("edit_neighborhood");
            const storedNeighborhood = "{{ $person->neighborhood }}";
            const storedCity = "{{ $person->current_city }}";
            const originalCity = "{{ $person->current_city }}"; // احفظ المحافظة الأصلية
            console.log("Original City (بعد التعريف):", originalCity);

            console.log("Stored City:", storedCity);
            console.log("Stored Neighborhood:", storedNeighborhood);

            updateNeighborhoods(storedCity, storedNeighborhood, originalCity); // مرر المحافظة الأصلية

            currentCitySelect.addEventListener("change", function () {
                console.log("City changed to:", this.value);
                updateNeighborhoods(this.value, null, originalCity); // مرر المحافظة الأصلية
            });
        });

        function populateNeighborhoodSelect(neighborhoods, neighborhoodSelect) {
            neighborhoods.forEach(neighborhood => {
                const option = document.createElement("option");
                option.value = neighborhood.value;
                option.textContent = neighborhood.label;
                neighborhoodSelect.appendChild(option);
            });
        }

        function updateNeighborhoods(selectedCity, selectedNeighborhood, originalCity) {
            console.log("--- تحديث الأحياء ---");
            console.log("المحافظة الحالية المحددة:", selectedCity);
            console.log("الحي المحدد سابقًا:", selectedNeighborhood);
            console.log("المحافظة الأصلية:", originalCity);

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
                    { value: "alFakhari", label: "الفخاري" }
                ]
            };

            const neighborhoods = cityNeighborhoods[selectedCity] || [];

            populateNeighborhoodSelect(neighborhoods, neighborhoodSelect);

            if (selectedCity === originalCity && selectedNeighborhood) {
                setTimeout(function () {
                    for (let i = 0; i < neighborhoodSelect.options.length; i++) {
                        if (neighborhoodSelect.options[i].value === selectedNeighborhood) {
                            neighborhoodSelect.selectedIndex = i;
                            break;
                        }
                    }
                }, 50);
            } else {
                neighborhoodSelect.value = "";
            }

            console.log("قيمة حقل الحي بعد التحديث:", neighborhoodSelect.value);
            console.log("--- نهاية تحديث الأحياء ---");
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

        document.addEventListener('DOMContentLoaded', function () {
            const neighborhoodSelect = document.getElementById('edit_neighborhood');
            const areaResponsibleField = document.getElementById('areaResponsibleField');
            const areaResponsibleSelect = document.getElementById('edit_area_responsible_id');
            const errorMessage = document.getElementById('edit_area_responsible_id_error');

             function toggleAreaResponsibleField() {
                const selectedNeighborhood = document.getElementById('edit_neighborhood').value;
                const areaResponsibleField = document.getElementById('areaResponsibleField');
                const responsibleInput = document.getElementById('edit_area_responsible_id');
                const errorMsg = document.getElementById('edit_area_responsible_id_error');

                if (selectedNeighborhood === 'alMawasi') {
                    areaResponsibleField.style.display = 'flex';
                } else {
                    areaResponsibleField.style.display = 'none';
                    responsibleInput.value = '';
                    if (errorMsg) errorMsg.style.display = 'none';
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const neighborhoodSelect = document.getElementById('edit_neighborhood');
                if (neighborhoodSelect) {
                    toggleAreaResponsibleField(); // عند التحميل
                    neighborhoodSelect.addEventListener('change', toggleAreaResponsibleField); // عند التغيير
                }
            });

            neighborhoodSelect.addEventListener('change', toggleAreaResponsibleField);
            toggleAreaResponsibleField(); // تشغيل عند تحميل الصفحة
        });

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
            // إرسال طلب AJAX إلى الخادم للحصول على بيانات العضو
            fetch(`/get-family-member-data/${familyMemberId}`)
                .then(response => response.json())
                .then(familyMemberData => {
                    console.log(familyMemberData);

                    // تعبئة البيانات في الفورم
                    document.getElementById('familyMemberId').value = familyMemberData.id;
                    document.getElementById('edit_f_first_name').value = familyMemberData.first_name;
                    document.getElementById('edit_f_father_name').value = familyMemberData.father_name;
                    document.getElementById('edit_f_grandfather_name').value = familyMemberData.grandfather_name;
                    document.getElementById('edit_f_family_name').value = familyMemberData.family_name;
                    document.getElementById('edit_f_id_num').value = familyMemberData.id_num;

                    // معالجة تاريخ الميلاد إذا كان غير نصي
                    let dobValue = familyMemberData.dob
                        ? String(familyMemberData.dob).split('T')[0]
                        : '';
                    document.getElementById('edit_f_dob').value = dobValue;
                    document.getElementById('edit_f_relationship').value = familyMemberData.relationship;
                    document.getElementById('edit_f_has_condition').value = familyMemberData.has_condition;
                    document.getElementById('edit_f_condition_description').value = familyMemberData.condition_description || '';

                    // فتح الفورم المنبثق
                    document.getElementById('editFamilyMemberModal').classList.remove('hidden');

                })
                .catch(error => {
                    console.error("Error fetching data:", error);

                    // ❌ إظهار رسالة خطأ بـ SweetAlert2
                    Swal.fire({
                        title: 'حدث خطأ!',
                        text: 'تعذر تحميل بيانات العضو، يرجى المحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'حسناً'
                    });
                });
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

    </script>

</body>
</html>
