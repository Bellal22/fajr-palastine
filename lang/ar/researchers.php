<?php

return [
    'plural' => 'الباحثين',
    'singular' => 'الباحث',
    'empty' => 'لا يوجد باحثين',
    'empty_hint' => 'لا يوجد باحثين حالياً. يمكنك إضافة باحث جديد من الزر أعلاه.',
    'select' => 'اختر الباحث',
    'permission' => 'ادارة الباحثين',
    'trashed' => 'الباحثين المحذوفين',
    'perPage' => 'عدد النتائج في الصفحة',
    'profile_details' => 'تفاصيل الملف الشخصي',
    'pagination_info' => 'عرض :from إلى :to من أصل :total',

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'security' => 'الأمان',
        'search_settings' => 'إعدادات البحث',
    ],

    'actions' => [
        'list' => 'كل الباحثين',
        'show' => 'عرض',
        'create' => 'إضافة',
        'new' => 'إضافة',
        'edit' => 'تعديل الباحث',
        'delete' => 'حذف الباحث',
        'restore' => 'استعادة',
        'forceDelete' => 'حذف نهائي',
        'save' => 'حفظ',
        'filter' => 'بحث',
        'reset_filter' => 'تفريغ الحقول',
        'clear_filter' => 'مسح الفلتر',
        'actions' => 'الإجراءات',
    ],

    'messages' => [
        'created' => 'تم إضافة الباحث بنجاح .',
        'updated' => 'تم تعديل الباحث بنجاح .',
        'deleted' => 'تم حذف الباحث بنجاح .',
        'restored' => 'تم استعادة الباحث بنجاح .',
        'forceDeleted' => 'تم حذف الباحث نهائياً.',
    ],

    'attributes' => [
        'name' => 'اسم الباحث',
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الالكترونى',
        'created_at' => 'تاريخ الإنضمام',
        'old_password' => 'كلمة المرور القديمة',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'type' => 'نوع المستخدم',
        'avatar' => 'الصورة الشخصية',
        'person_status' => 'حالة الباحث',
    ],

    'status' => [
        'active' => 'فعال',
        'inactive' => 'غير فعال',
    ],

    'hints' => [
        'password' => 'اترك الحقل فارغاً إذا كنت لا تريد تغيير كلمة المرور',
        'avatar_title' => 'نصائح لرفع الصورة:',
        'avatar_size' => 'الحجم الموصى به: 400x400 بكسل',
        'avatar_format' => 'الصيغ المدعومة: JPG, PNG, GIF',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم الباحث',
        'email' => 'أدخل البريد الإلكتروني',
        'phone' => 'أدخل رقم الهاتف',
        'password' => 'أدخل كلمة المرور',
        'password_confirmation' => 'أعد إدخال كلمة المرور',
    ],

    'filter_placeholders' => [
        'name' => 'ابحث بالاسم...',
        'email' => 'ابحث بالبريد الإلكتروني...',
        'phone' => 'ابحث برقم الهاتف...',
        'status' => 'اختر الحالة',
        'all_status' => 'كل الحالات',
    ],

    'dialogs' => [
        'delete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد حذف هذا الباحث ؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد استعادة هذا الباحث ؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد حذف هذا الباحث نهائياً؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];
