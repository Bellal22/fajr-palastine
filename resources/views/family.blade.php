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
            background-color: #FF6F00; /* اللون البرتقالي */
            color: white; /* النص باللون الأبيض */
            font-weight: bold; /* خط سميك */
            border-radius: 8px; /* زوايا دائرية */
            padding: 12px 24px; /* مسافات حول النص */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* ظل الزر */
            transition: background-color 0.3s ease; /* تأثير الانتقال */
            margin-top: 20px !important; /* مسافة بين الزر والجدول */
        }

        .custom-btn:hover {
            background-color: #E65100; /* تغيير اللون عند المرور فوق الزر */
        }

        .custom-btn:focus {
            outline: none; /* إزالة الحدود عند التركيز */
        }

        .error-message {
            color: red;
        }

        .form-row {
            display: flex;
            gap: 15px; /* مسافة بين الحقول */
            flex-wrap: wrap; /* السماح بكسر السطر إذا كانت الشاشة صغيرة */
        }

        .form-group {
            flex: 1; /* يجعل كل عنصر يأخذ مساحة متساوية */
            min-width: 150px; /* يضمن عدم تصغير الحقول أكثر من اللازم */
        }

        p {
            text-align: right;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex; /* جعل الأزرار في نفس السطر */
            gap: 10px; /* تحديد المسافة بين الأيقونات */
        }

        .edit-btn, .delete-btn {
            font-size: 18px; /* حجم الأيقونة */
            padding: 5px; /* padding حول الأيقونة */
            border: none; /* إزالة الحدود */
            background: none; /* إزالة الخلفية */
            cursor: pointer; /* تغيير شكل المؤشر عند المرور فوق الأيقونة */
        }

        .edit-btn i, .delete-btn i {
            color: #FF6F00; /* لون الأيقونة الخاصة بالتعديل */
        }

        .delete-btn i {
            color: #000000; /* لون الأيقونة الخاصة بالحذف */
        }

        .edit-btn:hover i, .delete-btn:hover i {
            opacity: 0.8; /* تأثير عند المرور فوق الأيقونة */
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
        {{-- {{$id_num}} --}}
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
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="submitForm()" class="custom-btn">
            إرسال البيانات
        </button>

    </div>

    <div id="form-popup">
        <h1>إضافة بيانات فرد</h1>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">الاسم الأول</label>
                <input type="text" id="first_name" name="first_name" placeholder="الاسم الأول" required>
            </div>
            <div class="form-group">
                <label for="father_name">اسم الأب</label>
                <input type="text" id="father_name" name="father_name" placeholder="اسم الأب" required>
            </div>
            <div class="form-group">
                <label for="grandfather_name">اسم الجد</label>
                <input type="text" id="grandfather_name" name="grandfather_name" placeholder="اسم الجد" required>
            </div>
            <div class="form-group">
                <label for="family_name">اسم العائلة</label>
                <input type="text" id="family_name" name="family_name" placeholder="اسم العائلة" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="id_num">رقم الهوية:</label>
                <input type="number" id="id_num" name="id_num" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInput('id_num')"maxlength="9">
                <span id="id_num_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                <span id="id_num_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
            </div>
            <div class="form-group">
                <label for="dob">تاريخ الميلاد</label>
                <input type="date" id="dob" name="dob" required>
            </div>
        </div>
        <div class="form-group">
            <label for="relationship">صلة القرابة</label>
            <select id="relationship" name="relationship" required>
                @foreach($relationships as $key => $relationship)
                    <option value="{{$key}}">{{$relationship}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="has_condition">هل يعاني من من مرض أو إعاقة أو إصابة حرب</label>
            <select id="has_condition" name="has_condition">
                <option value="لا">لا</option>
                <option value="نعم">نعم</option>
            </select>
        </div>
        <div class="form-group" id="condition-description-group" style="display: none;">
            <label for="condition_description">وصف الحالة</label>
            <textarea id="condition_description" name="condition_description" type="text" placeholder="وصف الحالة"></textarea>
        </div>
        <button type="button" id="add-person-btn">إضافة</button>
        <button type="button" id="close-popup-btn">إغلاق</button>
    </div>

    <!-- نموذج التعديل -->
    <div id="edit-popup" style="display: none;">
        <h1>تعديل بيانات فرد</h1>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">الاسم الأول</label>
                <input type="text" id="edit_first_name" name="edit_first_name" placeholder="الاسم الأول" required>
            </div>
            <div class="form-group">
                <label for="father_name">اسم الأب</label>
                <input type="text" id="edit_father_name" name="edit_father_name" placeholder="اسم الأب" required>
            </div>
            <div class="form-group">
                <label for="grandfather_name">اسم الجد</label>
                <input type="text" id="edit_grandfather_name" name="edit_grandfather_name" placeholder="اسم الجد" required>
            </div>
            <div class="form-group">
                <label for="family_name">اسم العائلة</label>
                <input type="text" id="edit_family_name" name="edit_family_name" placeholder="اسم العائلة" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="id_num">رقم الهوية:</label>
                <input type="number" id="edit_id_num" name="edit_id_num" placeholder="أدخل رقم الهوية" required oninput="validateIdOnInput('edit_id_num')"maxlength="9">
                <span id="edit_id_num_error" class="error-message" style="display:none; color: #ff0000;">رقم الهوية غير صالح.</span>
                <span id="edit_id_num_success" class="success-message" style="display:none; color: #35b735;">رقم الهوية صحيح.</span>
            </div>
            <div class="form-group">
                <label for="dob">تاريخ الميلاد</label>
                <input type="date" id="edit_dob" name="edit_dob" required>
            </div>
        </div>
        <div class="form-group">
            <label for="relationship">صلة القرابة</label>
            <select id="edit_relationship" name="edit_relationship" required>
                @foreach($relationships as $key => $relationship)
                    <option value="{{$key}}">{{$relationship}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="has_condition">هل يعاني من من مرض أو إعاقة أو إصابة حرب</label>
            <select id="edit_has_condition" name="edit_has_condition">
                <option value="لا">لا</option>
                <option value="نعم">نعم</option>
            </select>
        </div>
        <div class="form-group" id="condition-description-group" style="display: none;">
            <label for="condition_description">وصف الحالة</label>
            <textarea id="edit_condition_description" name="edit_condition_description" type="text" placeholder="وصف الحالة"></textarea>
        </div>
        <button id="save-edits">حفظ التعديلات</button>
        <button type="button" id="close-edit-popup-btn">إغلاق</button>

    </div>


    <script>
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

                const firstPersonRow = `
                    <tr id="first-person-row">
                        <td>${firstPersonData.id_num}</td>
                        <td>${firstPersonData.first_name}</td>
                        <td>${firstPersonData.father_name}</td>
                        <td>${firstPersonData.grandfather_name}</td>
                        <td>${firstPersonData.family_name}</td>
                        <td>${formattedDob}</td>
                        <td>${translatedRelationship}</td>
                        <td>${firstPersonData.has_condition == 1 ? 'نعم' : 'لا'}</td>
                        <td>${firstPersonData.condition_description}</td>
                        <td></td>
                    </tr>`;
                tableBody.append(firstPersonRow);
            }
            if (peopleList && peopleList.length > 0) {
                peopleList.forEach((person, index) => {
                    const formattedDob = person.dob ? new Date(person.dob).toLocaleDateString('ar-EN') : 'غير محدد';
                    const translatedRelationship = relationshipTranslations[person.relationship] || person.relationship;

                    const row = `
                        <tr>
                            <td>${person.id_num}</td>
                            <td>${person.first_name}</td>
                            <td>${person.father_name}</td>
                            <td>${person.grandfather_name}</td>
                            <td>${person.family_name}</td>
                            <td>${formattedDob}</td>
                            <td>${translatedRelationship}</td>
                            <td>${person.has_condition == 1 ? 'نعم' : 'لا'}</td>
                            <td>${person.condition_description ?? 'لا يوجد'}</td>
                            <td class="action-buttons">
                                <a class="edit-btn" data-index="${index}"><i class="fas fa-edit"></i></a>
                                <a class="delete-btn" data-index="${index}"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else if (!firstPersonData) {
                tableBody.html('<tr><td colspan="10">لا يوجد أفراد مضافين.</td></tr>');
            }
            updateEmptyMessage();
        }

        $(document).ready(function() {
            renderTable(); // استدعاء renderTable عند تحميل الصفحة
            $('#open-form-btn').prop('disabled', !$('#num_of_people').val());

            $('#num_of_people').on('input', function() {
                maxPeople = parseInt($(this).val()) || 0;
                $('#open-form-btn').prop('disabled', maxPeople === 0);
            });

            // بقية الكود الخاص بـ $(document).ready() يبقى كما هو
        });

        function editPerson(index) {
            console.log("📌 استدعاء editPerson مع index =", index);
            if (!Array.isArray(peopleList) || peopleList.length === 0) {
                console.error("❌ خطأ: قائمة الأشخاص غير متوفرة!");
                return;
            }
            if (index === undefined || index < 0 || index >= peopleList.length) {
                console.error("❌ خطأ: `index` غير صالح أو خارج النطاق!");
                return;
            }
            const person = peopleList[index];
            console.log("🟢 بيانات الشخص:", person);
            $('#edit_first_name').val(person.first_name || '');
            $('#edit_father_name').val(person.father_name || '');
            $('#edit_grandfather_name').val(person.grandfather_name || '');
            $('#edit_family_name').val(person.family_name || '');
            $('#edit_id_num').val(person.id_num || '');
            $('#edit_dob').val(person.dob || '');
            $('#edit_relationship').val(person.relationship || '');
            $('#edit_has_condition').val(person.has_condition ? 'نعم' : 'لا');
            $('#edit_condition_description').val(person.condition_description || 'لا يوجد');
            if (person.has_condition) {
                $('#edit_condition-description-group').show();
            } else {
                $('#edit_condition-description-group').hide();
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

        $(document).ready(function () {
            $('#open-form-btn').prop('disabled', !$('#num_of_people').val());
            updateEmptyMessage();

            $('#num_of_people').on('input', function () {
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

            $('#add-person-btn').click(function () {
                const id_num = $('#id_num').val();
                const first_name = $('#first_name').val();
                const father_name = $('#father_name').val();
                const grandfather_name = $('#grandfather_name').val();
                const family_name = $('#family_name').val();
                const dob = $('#dob').val();
                const relationship = $('#relationship').val();
                const has_condition = $('#has_condition').val();
                const condition_description = $('#condition_description').val();

                if (!id_num || !first_name ||!father_name ||!grandfather_name ||!family_name || !dob || !relationship || !has_condition || (has_condition === 'نعم' && !condition_description)) {
                    showAlert('يرجى ملء جميع الحقول المطلوبة!', 'error');
                    return;
                }
                if (!validateIdNumber('id_num')) {
                    return;
                }
                peopleList.push({
                    id_num,
                    first_name,
                    father_name,
                    grandfather_name,
                    family_name,
                    dob,
                    relationship,
                    has_condition: has_condition === 'نعم' ? 1 : 0,
                    condition_description: has_condition === 'نعم' ? condition_description : null
                });

                addedPeople++;
                renderTable();

                if (addedPeople === maxPeople) {
                    $('#open-form-btn').prop('disabled', true);
                }
                $('#form-popup input[type="text"], #form-popup input[type="number"], #form-popup input[type="date"], #form-popup select, #form-popup textarea').val('');
                $('#form-popup').fadeOut();
                $('#overlay').fadeOut();
                showAlert('تمت الإضافة بنجاح', 'success');
            });

            $('#has_condition').change(function () {
                $('#condition-description-group').toggle($(this).val() === 'نعم');
            });

            $('#edit_has_condition').change(function () {
                $('#edit_condition-description-group').toggle($(this).val() === 'نعم');
            });

            $(document).on('click', '.edit-btn', function() {
                let index = $(this).data('index');
                console.log("🖊️ تم النقر على زر التعديل للشخص رقم:", index);
                editPerson(index);
            });

            $(document).on('click', '.delete-btn', function() {
                let index = $(this).data('index');
                console.log("🗑️ تم النقر على زر الحذف للشخص رقم:", index);
                deletePerson(index);
            });

            $('#save-edits').off('click').on('click', function() {
                let index = $(this).data('index');
                console.log("📌 حفظ التعديلات للشخص رقم:", index);
                if (index !== undefined && peopleList[index]) {
                    peopleList[index] = {
                        first_name: $('#edit_first_name').val(),
                        father_name: $('#edit_father_name').val(),
                        grandfather_name: $('#edit_grandfather_name').val(),
                        family_name: $('#edit_family_name').val(),
                        id_num: $('#edit_id_num').val(),
                        dob: $('#edit_dob').val(),
                        relationship: $('#edit_relationship').val(),
                        has_condition: $('#edit_has_condition').val() === 'نعم' ? 1 : 0,
                        condition_description: $('#edit_condition_description').val()
                    };
                    $('#edit-popup').fadeOut();
                    Swal.fire({
                        icon: 'success',
                        title: 'تم تعديل البيانات بنجاح',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    renderTable();
                } else {
                    console.error("❌ خطأ: لا يمكن تعديل الشخص لأن الفهرس غير صحيح!");
                }
            });

            window.submitForm = function submitForm() {
                let person = @json($person);

                if (peopleList.length === 0 &&
                    !(['single', 'divorced', 'widowed'].includes(person.social_status))
                ) {
                    showAlert('لا توجد بيانات لإرسالها!', 'warning');
                    return;
                }

                const wivesCount = peopleList.filter(p => p.relationship === 'wife').length;

                if (person.social_status === 'married') { // "متزوج"
                    if (wivesCount !== 1) {
                        showAlert('الشخص المتزوج يجب أن يكون لديه زوجة واحدة فقط في قائمة الأفراد.', 'error');
                        return;
                    }
                } else if (person.social_status === 'polygamous') { // "متعدد" (لأكثر من زوجة)
                    if (wivesCount < 2 || wivesCount > 4) {
                        showAlert('الشخص المتعدد يجب أن يكون لديه من 2 إلى 4 زوجات في قائمة الأفراد.', 'error');
                        return;
                    }
                }

                $.ajax({
                    url: '/store-people-session',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({ peopleList: peopleList }),
                    success: function (response) {
                        if (response.success) {
                            $.ajax({
                                url: response.postRedirect,
                                type: 'POST',
                                data: { _token: csrfToken },
                                success: function(res) {
                                    console.log("تم تنفيذ storeFamily بنجاح، إعادة التوجيه إلى:", res.redirect);
                                    if (res.redirect) {
                                        window.location.href = res.redirect;
                                    } else {
                                        console.error("إعادة التوجيه غير معرفة في الاستجابة");
                                    }
                                },
                                error: function(xhr) {
                                    console.error("خطأ أثناء تنفيذ storeFamily:", xhr.responseText);
                                }
                            });
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: response.error,  // عرض الخطأ مع أرقام الهوية المكررة
                                confirmButtonColor: '#d33',
                                iconColor: '#d33',
                                confirmButtonText: 'إغلاق'
                            }).then(() => {
                                // إعادة البيانات المدخلة مع أرقام الهوية المكررة إلى الصفحة
                                sessionStorage.setItem('peopleList', JSON.stringify(response.peopleList));  // حفظ البيانات في sessionStorage
                                window.location.href = response.redirect;  // الرجوع إلى صفحة الإدخال مع البيانات
                            });
                        }
                    }
                });
            };
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

        // ✅ التحقق من رقم الهوية أثناء الكتابة بناءً على معرف الحقل (idField)
        function validateIdOnInput(idField) {
            const inputField = document.getElementById(idField);
            const idNum = inputField.value;
            const errorMessage = document.getElementById(idField + '_error');
            const successMessage = document.getElementById(idField + '_success');

            // منع المستخدم من إدخال أكثر من 9 أرقام
            if (idNum.length > 9) {
                inputField.value = idNum.slice(0, 9);  // اقتصاص الأرقام الزائدة
            }

            // التحقق إذا كان الرقم غير صالح أو صحيح باستخدام خوارزمية Luhn
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

        // ✅ التحقق من رقم الهوية عند إرسال النموذج بناءً على معرف الحقل (idField)
        function validateIdNumber(idField) {
            const inputField = document.getElementById(idField);
            const idNum = inputField.value;

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
