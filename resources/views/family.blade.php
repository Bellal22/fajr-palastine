<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نموذج تسجيل المواطنين - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                            url({{asset('background/image.jpg')}}) center center no-repeat;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
            font-size: 26px;
            margin-bottom: 30px;
        }

        #num_of_people {
            width: 120px;
            padding: 8px;
            font-size: 1.2rem;
            margin-right: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #open-form-btn {
            padding: 8px 15px;
            font-size: 1rem;
            background-color: #FF6F00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #E65100;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        table thead {
            background-color: #FF6F00;
            color: white;
        }

        table th, table td {
            text-align: center;
            padding: 1rem;
            border: 1px solid #ccc;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
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

        #edit-popup {
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

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: right;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
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

        #close-edit-popup-btn {
            background-color: #E65100;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        #close-edit-popup-btn:hover {
            background-color: #C41C00;
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

        #save-edits {
            background-color: #FF6F00;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
        }

        #save-edits:hover {
            background-color: #E65100;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .custom-btn {
            background-color: #FF6F00;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            padding: 12px 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            margin-top: 20px !important;
        }

        .custom-btn:hover {
            background-color: #E65100;
        }

        .custom-btn:focus {
            outline: none;
        }

        .error-message {
            color: red;
        }

        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 150px;
        }

        p {
            text-align: right;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn, .delete-btn {
            font-size: 18px;
            padding: 5px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .edit-btn i, .delete-btn i {
            color: #FF6F00;
        }

        .delete-btn i {
            color: #000000;
        }

        .edit-btn:hover i, .delete-btn:hover i {
            opacity: 0.8;
        }

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

            table{
                width: auto;
            }
            table th, table td {
                font-size: 0.9rem;
            }

            .form-popup {
                width: 90%;
                padding: 1rem;
            }

            .row .form-group {
                flex: 1;
                min-width: 45%;
            }
        }
    </style>
</head>
<body>
    <div id="overlay"></div>

    <div class="container">
        <div class="logo-container">
            <img src="{{asset('background/image.jpg')}}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>
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
        <p>
            قم بإدخال عدد أفراد أسرتك ثم قم بالضغط على زر إضافة فرد جديد لتقوم بإدخال بيانات الأفراد كاملة
        </p>
        <p>
            احرص عزيزي المواطن على تعبئة كافة بيانات أفراد أسرتك لضمان الاستفادة الكاملة من المشاريع الإغاثية القائمة 🤗
        </p>
        <div class="form-group" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
            <label for="num_of_people">عدد الأفراد</label>
            <input type="number" id="num_of_people" placeholder="عدد الأفراد" required>
            <button type="button" id="open-form-btn" disabled>إضافة فرد جديد</button>
        </div>

        <table id="family-table">
            <thead>
                <tr>
                    <th>رقم الهوية</th>
                    <th>الاسم الأول</th>
                    <th>اسم الأب</th>
                    <th>اسم الجد</th>
                    <th>اسم العائلة</th>
                    <th>تاريخ الميلاد</th>
                    <th>صلة القرابة</th>
                     <th>رقم الجوال</th>
                    <th>هل يعاني من أمراض</th>
                    <th>وصف الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr id="default-row" style="display: none;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <button id="send-btn"type="button" onclick="submitForm()" class="custom-btn">
            إرسال البيانات
        </button>
        <div id="loadingModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); color: white; font-size: 20px; text-align:center; padding-top: 20%;">
            جاري تسجيل بياناتك...
        </div>
    </div>

    <div id="form-popup">
        <h1>إضافة بيانات فرد</h1>

        <div class="form-row">
            <div class="form-group">
                <label for="firstname">الاسم الأول</label>
                <input type="text" id="firstname" name="firstname" placeholder="الاسم الأول" required>
            </div>
            <div class="form-group">
                <label for="fathername">اسم الأب</label>
                <input type="text" id="fathername" name="fathername" placeholder="اسم الأب" required>
            </div>
            <div class="form-group">
                <label for="grandfathername">اسم الجد</label>
                <input type="text" id="grandfathername" name="grandfathername" placeholder="اسم الجد" required>
            </div>
            <div class="form-group">
                <label for="familyname">اسم العائلة</label>
                <input type="text" id="familyname" name="familyname" placeholder="اسم العائلة" required>
            </div>
        </div>
        <div class="form-group">
            <label for="relationship">صلة القرابة</label>
            <select id="relationship" name="relationship" required>
                <option value="">اختر صلة القرابة</option>
                @foreach($relationships as $key => $relationship)
                    <option value="{{$key}}">{{$relationship}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" id="phone-group" style="display: none;">
            <label for="phone">رقم الجوال <span style="color: red;">*</span></label>
            <input type="tel" id="phone" name="phone" placeholder="مثال: 0599123456" maxlength="10" pattern="[0-9]{10}">
            <span id="phoneerror" class="error-message" style="display:none; color: #ff0000;">رقم الجوال غير صحيح (يجب أن يكون 10 أرقام)</span>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="idnum">رقم الهوية:</label>
                <input type="number" id="idnum" name="idnum" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInput('idnum')" maxlength="9">
                <span id="idnum_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                <span id="idnum_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
            </div>
            <div class="form-group">
                <label for="dob">تاريخ الميلاد</label>
                <input type="date" id="dob" name="dob" required>
                <span id="dob_error" class="error-message" style="display:none; color:#ff0000;"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="hascondition">هل يعاني من من مرض أو إعاقة أو إصابة حرب</label>
            <select id="hascondition" name="hascondition">
                <option value="0">لا</option>
                <option value="1">نعم</option>
            </select>
        </div>
        <div class="form-group" id="condition-description-group" style="display: none;">
            <label for="conditiondescription">وصف الحالة</label>
            <textarea id="conditiondescription" name="conditiondescription" type="text" placeholder="وصف الحالة"></textarea>
        </div>
        <button type="button" id="add-person-btn">إضافة</button>
        <button type="button" id="close-popup-btn">إغلاق</button>
    </div>

    <!-- نموذج التعديل -->
    <div id="edit-popup" style="display: none;">
        <h1>تعديل بيانات فرد</h1>

        <div class="form-row">
            <div class="form-group">
                <label for="editfirstname">الاسم الأول</label>
                <input type="text" id="editfirstname" name="editfirstname" placeholder="الاسم الأول" required>
            </div>
            <div class="form-group">
                <label for="editfathername">اسم الأب</label>
                <input type="text" id="editfathername" name="editfathername" placeholder="اسم الأب" required>
            </div>
            <div class="form-group">
                <label for="editgrandfathername">اسم الجد</label>
                <input type="text" id="editgrandfathername" name="editgrandfathername" placeholder="اسم الجد" required>
            </div>
            <div class="form-group">
                <label for="editfamilyname">اسم العائلة</label>
                <input type="text" id="editfamilyname" name="editfamilyname" placeholder="اسم العائلة" required>
            </div>
        </div>
        <div class="form-group">
            <label for="editrelationship">صلة القرابة</label>
            <select id="editrelationship" name="editrelationship" required>
                <option value="">اختر صلة القرابة</option>
                @foreach($relationships as $key => $relationship)
                    <option value="{{$key}}">{{$relationship}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" id="editphone-group" style="display: none;">
            <label for="editphone">رقم الجوال <span style="color: red;">*</span></label>
            <input type="tel" id="editphone" name="editphone" placeholder="مثال: 0599123456" maxlength="10" pattern="[0-9]{10}">
            <span id="editphoneerror" class="error-message" style="display:none; color: #ff0000;">رقم الجوال غير صحيح (يجب أن يكون 10 أرقام)</span>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="editidnum">رقم الهوية:</label>
                <input type="number" id="editidnum" name="editidnum" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInput('editidnum')" maxlength="9">
                <span id="editidnum_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                <span id="editidnum_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
            </div>
            <div class="form-group">
                <label for="editdob">تاريخ الميلاد</label>
                <input type="date" id="editdob" name="editdob" required>
                <span id="editdob_error" class="error-message" style="display:none; color:#ff0000;"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="edithascondition">هل يعاني من من مرض أو إعاقة أو إصابة حرب</label>
            <select id="edithascondition" name="edithascondition">
                <option value="0">لا</option>
                <option value="1">نعم</option>
            </select>
        </div>
        <div class="form-group" id="editcondition-description-group" style="display: none;">
            <label for="editconditiondescription">وصف الحالة</label>
            <textarea id="editconditiondescription" name="editconditiondescription" type="text" placeholder="وصف الحالة"></textarea>
        </div>
        <button id="save-edits">حفظ التعديلات</button>
        <button type="button" id="close-edit-popup-btn">إغلاق</button>
    </div>

    <script>
        function calculateAge(dobStr) {
            const dob = new Date(dobStr);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            return age;
        }

        // عناصر الإضافة
        const relationship     = document.getElementById('relationship');
        const dob              = document.getElementById('dob');
        const dobError         = document.getElementById('dob_error');
        const addBtn           = document.getElementById('add-person-btn');

        // عناصر التعديل
        const editRelationship = document.getElementById('editrelationship');
        const editDob          = document.getElementById('editdob');
        const editDobError     = document.getElementById('editdob_error');
        const saveEditBtn      = document.getElementById('save-edits');

        // في البداية تاريخ الميلاد disabled
        dob.disabled = true;
        editDob.disabled = true;

        // دالة لتفعيل حقل تاريخ الميلاد
        function enableDobField(dobField, errorField) {
            dobField.disabled = false;
            errorField.style.display = 'none';
        }

        // دالة لمعالجة تغيير العلاقة
        function handleRelationshipChange(selectElement, phoneGroupId, phoneInputId, dobField, errorField) {
            const phoneGroup = document.getElementById(phoneGroupId);
            const phoneInput = document.getElementById(phoneInputId);

            enableDobField(dobField, errorField);

            if (selectElement.value === 'wife') {
                phoneGroup.style.display = 'block';
                phoneInput.required = true;
            } else {
                phoneGroup.style.display = 'none';
                phoneInput.required = false;
                phoneInput.value = '';
            }
        }

        relationship.addEventListener('change', function () {
            handleRelationshipChange(this, 'phone-group', 'phone', dob, dobError);
        });

        editRelationship.addEventListener('change', function () {
            handleRelationshipChange(this, 'editphone-group', 'editphone', editDob, editDobError);
        });

        function validatePhone(phoneField) {
            const phoneInput = document.getElementById(phoneField);
            const phoneValue = phoneInput.value;
            const phoneError = document.getElementById(phoneField + 'error');

            // التحقق من أن الرقم 10 خانات ويبدأ بـ 05
            const phonePattern = /^05[0-9]{8}$/;

            if (phoneValue && !phonePattern.test(phoneValue)) {
                phoneError.style.display = 'inline';
                phoneInput.style.borderColor = '#ff0000';
                return false;
            } else {
                phoneError.style.display = 'none';
                phoneInput.style.borderColor = '';
                return true;
            }
        }

        // إضافة Event Listener للتحقق أثناء الكتابة
        document.getElementById('phone').addEventListener('input', function() {
            validatePhone('phone');
        });

        document.getElementById('editphone').addEventListener('input', function() {
            validatePhone('editphone');
        });

        // دالة فحص الزوج/الزوجة
        function isSpouse(rel) {
            return ['زوج', 'زوجة', 'wife', 'husband'].includes(rel);
        }

        // دالة للتحقق من العمر
        function validateSpouseAge(rel, dobValue, errorElement) {
            if (isSpouse(rel)) {
                if (!dobValue) {
                    errorElement.textContent = 'يرجى إدخال تاريخ الميلاد للزوج/الزوجة.';
                    errorElement.style.display = 'block';
                    return false;
                }

                const age = calculateAge(dobValue);
                if (age < 16) {
                    errorElement.textContent = 'عمر الزوج/الزوجة يجب أن يكون 16 سنة فأكثر.';
                    errorElement.style.display = 'block';
                    return false;
                }
            }
            return true;
        }

        // زر إضافة فرد (AJAX)
        addBtn.addEventListener('click', function (e) {
            dobError.style.display = 'none';
            dobError.textContent   = '';

            const rel      = relationship.value;
            const dobValue = dob.value;

            if (!validateSpouseAge(rel, dobValue, dobError)) {
                return; // لا تكمل، لا تستدعي AJAX
            }

            // لو كل شيء تمام، هنا استدعي دالة الـ AJAX حقتك
            // مثال:
            // submitAddPersonAjax();
        });

        // زر حفظ التعديل (AJAX)
        saveEditBtn.addEventListener('click', function (e) {
            editDobError.style.display = 'none';
            editDobError.textContent   = '';

            const rel      = editRelationship.value;
            const dobValue = editDob.value;

            if (!validateSpouseAge(rel, dobValue, editDobError)) {
                return; // لا تكمل AJAX
            }

            // لو التحقق تمام، استدعي AJAX التعديل
            // مثال:
            // submitEditPersonAjax();
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const relationshipTranslations = {
            'father':'أب',
            'mother':'أم',
            'brother':'أخ',
            'sister':'أخت',
            'husband':'زوج',
            'wife':'زوجة',
            'son':'ابن',
            'daughter':'ابنة',
            'others':'اخرون',
        };
        let maxPeople = 0;
        let addedPeople = 0;
        let peopleList = [];

        function renderTable() {
            const tableBody = $('#family-table tbody');
            tableBody.empty();
            const firstPersonData = {!! json_encode(session('first_person_data')) !!};
            if (firstPersonData) {
                const formattedDob = firstPersonData.dob ? new Date(firstPersonData.dob).toLocaleDateString('ar-EN') : 'غير محدد';
                const translatedRelationship = relationshipTranslations[firstPersonData.relationship] || firstPersonData.relationship;
                const conditionDescription = firstPersonData.condition_description ? firstPersonData.condition_description : 'لا يوجد';

                // إضافة رقم الجوال إذا كان موجوداً
                const phoneDisplay = firstPersonData.phone ? firstPersonData.phone : '-';

                const firstPersonRow = `
                    <tr id="first-person-row">
                        <td>${firstPersonData.id_num}</td>
                        <td>${firstPersonData.first_name}</td>
                        <td>${firstPersonData.father_name}</td>
                        <td>${firstPersonData.grandfather_name}</td>
                        <td>${firstPersonData.family_name}</td>
                        <td>${formattedDob}</td>
                        <td>${translatedRelationship}</td>
                        <td>${phoneDisplay}</td>
                        <td>${firstPersonData.has_condition == 1 ? 'نعم' : 'لا'}</td>
                        <td>${firstPersonData.condition_description}</td>
                        <td></td>
                    </tr>`;
                tableBody.append(firstPersonRow);
            }
            if (peopleList && peopleList.length > 0) {
                peopleList.forEach((person, index) => {
                    const formattedDob = person.dob ? new Date(person.dob).toLocaleDateString('ar-EN') : '';
                    const translatedRelationship = relationshipTranslations[person.relationship] || person.relationship;
                    const phoneDisplay = person.relationship === 'wife' && person.phone ? person.phone : '-';

                    const row = `
                        <tr>
                            <td>${person.id_num}</td>
                            <td>${person.first_name}</td>
                            <td>${person.father_name}</td>
                            <td>${person.grandfather_name}</td>
                            <td>${person.family_name}</td>
                            <td>${formattedDob}</td>
                            <td>${translatedRelationship}</td>
                            <td>${phoneDisplay}</td>
                            <td>${person.has_condition === 1 ? 'نعم' : 'لا'}</td>
                            <td>${person.condition_description ?? ''}</td>
                            <td class="action-buttons">
                                <a class="edit-btn" data-index="${index}"><i class="fas fa-edit"></i></a>
                                <a class="delete-btn" data-index="${index}"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            } else if (!firstPersonData) {
                tableBody.html('<tr><td colspan="10">لا يوجد أفراد مضافين.</td></tr>');
            }
            updateEmptyMessage();
        }

        $(document).ready(function() {
            renderTable();
            $('#open-form-btn').prop('disabled', !$('#num_of_people').val());

            $('#num_of_people').on('input', function() {
                maxPeople = parseInt($(this).val()) || 0;
                $('#open-form-btn').prop('disabled', maxPeople === 0);
            });

            $('#open-form-btn').click(function () {
                if (maxPeople === 0) {
                    showAlert('يرجى تعبئة عدد الأفراد أولاً!', 'warning');
                    return;
                }
                if (addedPeople >= maxPeople) {
                    showAlert('لقد تجاوزت عدد أفراد أسرتك!', 'error');
                    return;
                }
                $('#form-popup').fadeIn();
                $('#overlay').fadeIn();
            });

            $('#close-popup-btn, #overlay').click(function () {
                $('#form-popup').fadeOut();
                $('#overlay').fadeOut();
            });

            $('#close-edit-popup-btn, #overlay').click(function () {
                $('#edit-popup').fadeOut();
                $('#overlay').fadeOut();
            });

            $('#hascondition').change(function () {
                $('#condition-description-group').toggle($(this).val() === '1');
            });

            $('#edithascondition').change(function () {
                $('#editcondition-description-group').toggle($(this).val() === '1');
            });

            $(document).on('click', '.edit-btn', function() {
                let index = $(this).data('index');
                editPerson(index);
            });

            $(document).on('click', '.delete-btn', function() {
                let index = $(this).data('index');
                deletePerson(index);
            });

            // دالة لتحديث زر الإضافة
            $('#add-person-btn').click(function() {
                // مسح رسائل الخطأ السابقة
                dobError.style.display = 'none';
                dobError.textContent = '';

                // التحقق من الحقول المطلوبة
                if (!validateRequiredFields()) {
                    return;
                }

                const idnum = $('#idnum').val();
                const firstname = $('#firstname').val();
                const fathername = $('#fathername').val();
                const grandfathername = $('#grandfathername').val();
                const familyname = $('#familyname').val();
                const dobVal = $('#dob').val();
                const relationshipVal = $('#relationship').val();
                const hascondition = $('#hascondition').val();
                const conditiondescription = $('#conditiondescription').val();
                const phone = $('#phone').val();

                // التحقق من رقم الجوال للزوجة
                if (relationshipVal === 'wife') {
                    if (!phone) {
                        showAlert('يرجى إدخال رقم جوال الزوجة!', 'error');
                        return;
                    }
                    if (!validatePhone('phone')) {
                        showAlert('رقم الجوال غير صحيح!', 'error');
                        return;
                    }
                }

                // التحقق من العمر للزوجة/الزوج
                if (!validateSpouseAge(relationshipVal, dobVal, dobError)) {
                    return;
                }

                // التحقق من رقم الهوية
                if (!validateIdNumber('idnum')) {
                    return;
                }

                // التحقق من تكرار رقم الهوية
                const isDuplicate = peopleList.some(person => person.id_num === idnum);
                if (isDuplicate) {
                    showAlert('رقم هوية مكرر!', 'error');
                    return;
                }

                // إضافة الشخص للقائمة مع تطابق أسماء الحقول مع قاعدة البيانات
                peopleList.push({
                    id_num: idnum,
                    first_name: firstname,
                    father_name: fathername,
                    grandfather_name: grandfathername,
                    family_name: familyname,
                    dob: dobVal,
                    relationship: relationshipVal,
                    has_condition: hascondition === '1' ? 1 : 0,
                    condition_description: hascondition === '1' ? conditiondescription : null,
                    phone: relationshipVal === 'wife' ? phone : null
                });

                addedPeople++;
                renderTable();

                if (addedPeople >= maxPeople) {
                    $('#open-form-btn').prop('disabled', true);
                }

                // مسح الحقول وإغلاق النافذة
                $('#form-popup input[type="text"], #form-popup input[type="number"], #form-popup input[type="date"], #form-popup input[type="tel"], #form-popup select, #form-popup textarea').val('');
                $('#phone-group').hide();
                $('#condition-description-group').hide();
                $('#form-popup').fadeOut();
                $('#overlay').fadeOut();

                showAlert('تمت الإضافة بنجاح!', 'success');
            });

            // تحديث زر حفظ التعديلات
            $('#save-edits').off('click').on('click', function(e) {
                e.preventDefault();

                // مسح رسائل الخطأ السابقة
                editDobError.style.display = 'none';
                editDobError.textContent = '';

                // التحقق من الحقول المطلوبة
                if (!validateEditRequiredFields()) {
                    return;
                }

                const rel = $('#editrelationship').val();
                const dobVal = $('#editdob').val();
                const phone = $('#editphone').val();
                const currentIndex = $(this).data('index');
                const idnum = $('#editidnum').val();

                // التحقق من رقم الجوال للزوجة
                if (rel === 'wife') {
                    if (!validatePhone('editphone')) {
                        showAlert('رقم الجوال غير صحيح!', 'error');
                        return;
                    }
                }

                // التحقق من العمر للزوجة/الزوج
                if (!validateSpouseAge(rel, dobVal, editDobError)) {
                    return;
                }

                // التحقق من رقم الهوية
                if (!validateIdNumber('editidnum')) {
                    return;
                }

                // التحقق من تكرار رقم الهوية (باستثناء السجل الحالي)
                const isDuplicate = peopleList.some((person, idx) =>
                    person.id_num === idnum && idx !== currentIndex
                );

                if (isDuplicate) {
                    showAlert('رقم هوية مكرر!', 'error');
                    return;
                }

                // حفظ التعديلات
                if (currentIndex !== undefined) {
                    peopleList[currentIndex] = {
                        ...peopleList[currentIndex],
                        first_name: $('#editfirstname').val(),
                        father_name: $('#editfathername').val(),
                        grandfather_name: $('#editgrandfathername').val(),
                        family_name: $('#editfamilyname').val(),
                        id_num: idnum,
                        dob: dobVal,
                        relationship: rel,
                        has_condition: $('#edithascondition').val() ? 1 : 0,
                        condition_description: $('#editconditiondescription').val(),
                        phone: rel === 'wife' ? phone : null
                    };

                    $('#edit-popup').fadeOut();
                    $('#overlay').fadeOut();
                    showAlert('تم التعديل بنجاح!', 'success');
                    renderTable();
                } else {
                    console.error('index غير موجود!');
                }
            });

            // دالة لإرسال النموذج
            window.submitForm = function submitForm() {
                const submitBtn = document.getElementById('send-btn');
                submitBtn.disabled = true;

                Swal.fire({
                    title: 'جاري تسجيل بياناتك...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let person = @json($person);

                // التحقق من صحة العلاقات الزوجية
                if (['single', 'divorced', 'widowed'].includes(person.social_status)) {
                    const forbiddenRelationships = ['husband', 'wife'];
                    const hasForbidden = peopleList.some(p => forbiddenRelationships.includes(p.relationship));
                    if (hasForbidden) {
                        Swal.close();
                        showAlert('لا يمكن تسجيل أفراد الأسرة ذات علاقات زوج/زوجة إذا كانت الحالة الاجتماعية أعزب/ة أو مطلق/ة أرمل/ة.', 'error');
                        submitBtn.disabled = false;
                        return;
                    }
                }

                // التحقق من وجود بيانات
                if (peopleList.length === 0 && !(['single', 'divorced', 'widowed'].includes(person.social_status))) {
                    Swal.close();
                    showAlert('لا توجد بيانات لإرسالها!', 'warning');
                    submitBtn.disabled = false;
                    return;
                }

                // التحقق من عدد الزوجات حسب الحالة الاجتماعية
                const wivesCount = peopleList.filter(p => p.relationship === 'wife').length;
                if (person.social_status === 'married' && wivesCount !== 1) {
                    Swal.close();
                    showAlert('الشخص المتزوج يجب أن يكون لديه زوجة واحدة فقط في قائمة الأفراد.', 'error');
                    submitBtn.disabled = false;
                    return;
                } else if (person.social_status === 'polygamous' && (wivesCount < 2 || wivesCount > 4)) {
                    Swal.close();
                    showAlert('الشخص المتعدد يجب أن يكون لديه من 2 إلى 4 زوجات في قائمة الأفراد.', 'error');
                    submitBtn.disabled = false;
                    return;
                }

                // حفظ بيانات الأسرة في الجلسة
                $.ajax({
                    url: '/store-people-session',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({ peopleList: peopleList }),
                    success: function(sessionResponse) {
                        if (!sessionResponse.success) {
                            Swal.close();
                            showAlert(sessionResponse.message || 'حدث خطأ أثناء تجهيز بيانات الأسرة.', 'error');
                            submitBtn.disabled = false;
                            return;
                        }

                        // تسجيل الأسرة نهائياً
                        $.ajax({
                            url: sessionResponse.postRedirect || '/store-family',
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function(storeResponse) {
                                Swal.close();

                                if (!storeResponse.success) {
                                    if (storeResponse.rejected_id && storeResponse.reason) {
                                        showAlert(
                                            `رقم الهوية المرفوض: <strong>${storeResponse.rejected_id}</strong><br>` +
                                            `سبب الرفض: <strong>${storeResponse.reason}</strong>`,
                                            'error'
                                        );
                                    } else {
                                        showAlert(storeResponse.message || 'حدث خطأ أثناء تسجيل البيانات.', 'error');
                                    }
                                    submitBtn.disabled = false;
                                    return;
                                }

                                // نجح التسجيل
                                if (storeResponse.redirect) {
                                    window.location.href = storeResponse.redirect;
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'تم التسجيل بنجاح!',
                                        text: 'سيتم تحويلك قريباً...',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = '/persons/success';
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                submitBtn.disabled = false;

                                const errorResponse = xhr.responseJSON || {};
                                if (errorResponse.message) {
                                    showAlert(errorResponse.message, 'error');
                                } else if (errorResponse.rejected_id && errorResponse.reason) {
                                    showAlert(
                                        `رقم الهوية المرفوض: <strong>${errorResponse.rejected_id}</strong><br>` +
                                        `سبب الرفض: <strong>${errorResponse.reason}</strong>`,
                                        'error'
                                    );
                                } else {
                                    showAlert('حدث خطأ غير متوقع أثناء التسجيل.', 'error');
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        submitBtn.disabled = false;
                        const response = xhr.responseJSON || {};

                        if (response.message) {
                            showAlert(response.message, 'error');
                        } else {
                            showAlert('فشل في حفظ بيانات الجلسة. يرجى المحاولة مرة أخرى.', 'error');
                        }
                    }
                });
            };

            // إضافة CSS للنافذة المنبثقة
            const style = document.createElement('style');
            style.textContent = `
                .rtl-popup {
                    direction: rtl;
                    text-align: right;
                }

                .rtl-content {
                    text-align: right !important;
                }

                .swal2-html-container ul {
                    text-align: right;
                }

                input.empty-field,
                select.empty-field,
                textarea.empty-field {
                    border-color: #ff0000 !important;
                    animation: shake 0.3s;
                }

                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-5px); }
                    75% { transform: translateX(5px); }
                }
            `;
            document.head.appendChild(style);

            // إضافة مؤشر بصري للحقول الفارغة عند التركيز
            $('#form-popup input, #form-popup select, #form-popup textarea').on('input change', function() {
                if ($(this).val() && $(this).val().trim() !== '') {
                    $(this).css('border-color', '#35b735');
                    setTimeout(() => {
                        $(this).css('border-color', '');
                    }, 1000);
                }
            });

            $('#edit-popup input, #edit-popup select, #edit-popup textarea').on('input change', function() {
                if ($(this).val() && $(this).val().trim() !== '') {
                    $(this).css('border-color', '#35b735');
                    setTimeout(() => {
                        $(this).css('border-color', '');
                    }, 1000);
                }
            });
        });

        function editPerson(index) {
            console.log('editPerson index =', index);

            if (!Array.isArray(peopleList) || peopleList.length === 0) {
                console.error('قائمة الأشخاص فارغة!');
                return;
            }

            if (index === undefined || index < 0 || index >= peopleList.length) {
                console.error('index غير صحيح!');
                return;
            }

            const person = peopleList[index];
            console.log('تعديل الشخص:', person);

            $('#editfirstname').val(person.first_name);
            $('#editfathername').val(person.father_name);
            $('#editgrandfathername').val(person.grandfather_name);
            $('#editfamilyname').val(person.family_name);
            $('#editidnum').val(person.id_num);
            $('#editdob').val(person.dob);
            $('#editrelationship').val(person.relationship);
            $('#edithascondition').val(person.has_condition ? '1' : '0');
            $('#editconditiondescription').val(person.condition_description);
            $('#editphone').val(person.phone || '');

            // إظهار حقل الجوال إذا كانت زوجة
            if (person.relationship === 'wife') {
                $('#editphone-group').show();
                $('#editphone').prop('required', true);
            } else {
                $('#editphone-group').hide();
                $('#editphone').prop('required', false);
            }

            if (person.has_condition) {
                $('#editcondition-description-group').show();
            } else {
                $('#editcondition-description-group').hide();
            }

            $('#save-edits').data('index', index);
            $('#edit-popup').fadeIn();
        }

        function deletePerson(index) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لا يمكن التراجع عن هذا التعديل!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم, حذف!',
                cancelButtonText: 'لا, إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    peopleList.splice(index, 1);
                    addedPeople--;
                    renderTable();
                    Swal.fire(
                        'تم الحذف!',
                        'تم حذف العنصر بنجاح.',
                        'success'
                    );
                }
            });
        }

        function updateEmptyMessage() {
            const tableBody = $('#family-table tbody');
            if (peopleList.length === 0 && !{!! json_encode(session('first_person_data')) !!}) {
                tableBody.html('<tr class="empty-row"><td colspan="10" style="text-align:center;">لا يوجد بيانات لعرضها</td></tr>');
            } else {
                tableBody.find('.empty-row').remove();
            }
        }

        function showAlert(message, type) {
            let bgColor = '';
            let textColor = '';
            let confirmButtonColor = '';

            if (type === 'success') {
                bgColor = 'white';
                textColor = '#4CAF50';
                confirmButtonColor = '#4CAF50';
            } else if (type === 'error') {
                bgColor = 'white';
                textColor = '#ff0000';
                confirmButtonColor = '#ff0000';
            } else if (type === 'warning') {
                bgColor = 'white';
                textColor = '#FF8C00';
                confirmButtonColor = '#FF8C00';
            }

            Swal.fire({
                icon: type,
                title: message,
                showConfirmButton: true,
                background: bgColor,
                color: textColor,
                confirmButtonText: 'إغلاق',
                confirmButtonColor
            });
        }

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

        function validateIdOnInput(idField) {
            const inputField = document.getElementById(idField);
            const idNum = inputField.value;
            const errorMessage = document.getElementById(idField + '_error');
            const successMessage = document.getElementById(idField + '_success');
            if (idNum.length > 9) {
                inputField.value = idNum.slice(0, 9);
            }
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                inputField.style.borderColor = '#ff0000';
                inputField.style.outlineColor = '#ff0000';
                errorMessage.style.display = 'inline';
                successMessage.style.display = 'none';
            } else if (idNum.length === 9 && luhnCheck(idNum)) {
                inputField.style.borderColor = '#35b735';
                inputField.style.outlineColor = '#35b735';
                errorMessage.style.display = 'none';
                successMessage.style.display = 'inline';
            } else {
                inputField.style.borderColor = '';
                inputField.style.outlineColor = '';
                errorMessage.style.display = 'none';
                successMessage.style.display = 'none';
            }
        }

        function validateIdNumber(idField) {
            const inputField = document.getElementById(idField);
            const idNum = inputField.value;
            if (idNum.length === 9 && !luhnCheck(idNum)) {
                Swal.fire({
                    icon: 'error',
                    title: 'رقم الهوية غير صالح',
                    text: 'الرجاء التأكد من إدخال رقم هوية صحيح.',
                    background: '#fff',
                    confirmButtonColor: '#d33',
                    iconColor: '#d33',
                    confirmButtonText: 'إغلاق',
                    customClass: {
                        confirmButton: 'swal-button-custom'
                    }
                });
                return false;
            }
            return true;
        }

        function validateRequiredFields() {
            const fields = {
                'idnum': { label: 'رقم الهوية', element: $('#idnum') },
                'firstname': { label: 'الاسم الأول', element: $('#firstname') },
                'fathername': { label: 'اسم الأب', element: $('#fathername') },
                'grandfathername': { label: 'اسم الجد', element: $('#grandfathername') },
                'familyname': { label: 'اسم العائلة', element: $('#familyname') },
                'dob': { label: 'تاريخ الميلاد', element: $('#dob') },
                'relationship': { label: 'صلة القرابة', element: $('#relationship') },
                'hascondition': { label: 'هل يعاني من أمراض', element: $('#hascondition') }
            };

            const missingFields = [];
            const emptyFields = [];

            // التحقق من كل حقل
            Object.entries(fields).forEach(([key, field]) => {
                const value = field.element.val();

                if (!value || value.trim() === '') {
                    missingFields.push(field.label);
                    field.element.css('border-color', '#ff0000');
                    emptyFields.push(field.element);
                } else {
                    field.element.css('border-color', '');
                }
            });

            // إذا كانت صلة القرابة "زوجة"، تحقق من رقم الجوال
            const relationship = $('#relationship').val();
            if (relationship === 'wife') {
                const phone = $('#phone').val();
                if (!phone || phone.trim() === '') {
                    missingFields.push('رقم الجوال');
                    $('#phone').css('border-color', '#ff0000');
                    emptyFields.push($('#phone'));
                }
            }

            // إذا كانت الحالة الصحية "نعم"، تحقق من وصف الحالة
            const hasCondition = $('#hascondition').val();
            if (hasCondition === '1') {
                const conditionDesc = $('#conditiondescription').val();
                if (!conditionDesc || conditionDesc.trim() === '') {
                    missingFields.push('وصف الحالة الصحية');
                    $('#conditiondescription').css('border-color', '#ff0000');
                    emptyFields.push($('#conditiondescription'));
                }
            }

            // إذا كانت هناك حقول فارغة
            if (missingFields.length > 0) {
                let message = '<div style="text-align: right;">';
                message += '<p style="font-size: 18px; margin-bottom: 15px;">⚠️ يرجى ملء الحقول التالية:</p>';
                message += '<ul style="list-style: none; padding: 0;">';

                missingFields.forEach(field => {
                    message += `<li style="padding: 8px; margin: 5px 0; background: #fff3cd; border-right: 4px solid #ff6f00; border-radius: 4px;">
                        <i class="fas fa-exclamation-circle" style="color: #ff6f00; margin-left: 8px;"></i>
                        <strong>${field}</strong>
                    </li>`;
                });

                message += '</ul></div>';

                Swal.fire({
                    icon: 'warning',
                    title: 'حقول مطلوبة!',
                    html: message,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ff6f00',
                    customClass: {
                        popup: 'rtl-popup',
                        htmlContainer: 'rtl-content'
                    }
                });

                // التركيز على أول حقل فارغ
                if (emptyFields.length > 0) {
                    emptyFields[0].focus();
                }

                return false;
            }

            // إعادة تعيين جميع الحدود
            Object.values(fields).forEach(field => {
                field.element.css('border-color', '');
            });

            return true;
        }

        function validateEditRequiredFields() {
            const fields = {
                'editidnum': { label: 'رقم الهوية', element: $('#editidnum') },
                'editfirstname': { label: 'الاسم الأول', element: $('#editfirstname') },
                'editfathername': { label: 'اسم الأب', element: $('#editfathername') },
                'editgrandfathername': { label: 'اسم الجد', element: $('#editgrandfathername') },
                'editfamilyname': { label: 'اسم العائلة', element: $('#editfamilyname') },
                'editdob': { label: 'تاريخ الميلاد', element: $('#editdob') },
                'editrelationship': { label: 'صلة القرابة', element: $('#editrelationship') },
                'edithascondition': { label: 'هل يعاني من أمراض', element: $('#edithascondition') }
            };

            const missingFields = [];
            const emptyFields = [];

            // التحقق من كل حقل
            Object.entries(fields).forEach(([key, field]) => {
                const value = field.element.val();

                if (!value || value.trim() === '') {
                    missingFields.push(field.label);
                    field.element.css('border-color', '#ff0000');
                    emptyFields.push(field.element);
                } else {
                    field.element.css('border-color', '');
                }
            });

            // إذا كانت صلة القرابة "زوجة"، تحقق من رقم الجوال
            const relationship = $('#editrelationship').val();
            if (relationship === 'wife') {
                const phone = $('#editphone').val();
                if (!phone || phone.trim() === '') {
                    missingFields.push('رقم الجوال');
                    $('#editphone').css('border-color', '#ff0000');
                    emptyFields.push($('#editphone'));
                }
            }

            // إذا كانت الحالة الصحية "نعم"، تحقق من وصف الحالة
            const hasCondition = $('#edithascondition').val();
            if (hasCondition === '1') {
                const conditionDesc = $('#editconditiondescription').val();
                if (!conditionDesc || conditionDesc.trim() === '') {
                    missingFields.push('وصف الحالة الصحية');
                    $('#editconditiondescription').css('border-color', '#ff0000');
                    emptyFields.push($('#editconditiondescription'));
                }
            }

            // إذا كانت هناك حقول فارغة
            if (missingFields.length > 0) {
                let message = '<div style="text-align: right;">';
                message += '<p style="font-size: 18px; margin-bottom: 15px;">⚠️ يرجى ملء الحقول التالية:</p>';
                message += '<ul style="list-style: none; padding: 0;">';

                missingFields.forEach(field => {
                    message += `<li style="padding: 8px; margin: 5px 0; background: #fff3cd; border-right: 4px solid #ff6f00; border-radius: 4px;">
                        <i class="fas fa-exclamation-circle" style="color: #ff6f00; margin-left: 8px;"></i>
                        <strong>${field}</strong>
                    </li>`;
                });

                message += '</ul></div>';

                Swal.fire({
                    icon: 'warning',
                    title: 'حقول مطلوبة!',
                    html: message,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ff6f00',
                    customClass: {
                        popup: 'rtl-popup',
                        htmlContainer: 'rtl-content'
                    }
                });

                // التركيز على أول حقل فارغ
                if (emptyFields.length > 0) {
                    emptyFields[0].focus();
                }

                return false;
            }

            // إعادة تعيين جميع الحدود
            Object.values(fields).forEach(field => {
                field.element.css('border-color', '');
            });

            return true;
        }
    </script>
</body>
</html>
