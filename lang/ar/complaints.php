<?php

return [
    'singular' => 'الشكوى',
    'plural' => 'الشكاوي',
    'empty' => 'لا يوجد شكاوي حتى الآن',
    'count' => 'عدد الشكاوي',
    'search' => 'بحث',
    'select' => 'اختر الشكوى',
    'permission' => 'إدارة الشكاوي',
    'trashed' => 'الشكاوي المحذوفة',
    'perPage' => 'عدد النتائج بالصفحة',
    'filter' => 'ابحث عن شكوى',

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'إضافة شكوى',
        'show' => 'عرض',
        'edit' => 'تعديل الشكوى',
        'delete' => 'حذف الشكوى',
        'restore' => 'استعادة',
        'forceDelete' => 'حذف نهائي',
        'options' => 'خيارات',
        'save' => 'حفظ',
        'filter' => 'تصفية',
        'reset' => 'إعادة تعيين',
        'empty_filters' => 'تفريغ الحقول',
        'actions' => 'الإجراءات',
        'send_response' => 'إرسال الرد',
        'quick_actions' => 'إجراءات سريعة',
    ],

    'messages' => [
        'created' => 'تم إضافة الشكوى بنجاح.',
        'updated' => 'تم تعديل الشكوى بنجاح.',
        'deleted' => 'تم حذف الشكوى بنجاح.',
        'restored' => 'تم استعادة الشكوى بنجاح.',
    ],

    'attributes' => [
        'id' => 'رقم الشكوى',
        'id_num' => 'رقم الهوية',
        'complaint_title' => 'عنوان الشكوى',
        'complaint_text' => 'نص الشكوى',
        'complaint_number' => 'رقم الشكوى',
        'submission_date' => 'تاريخ التقديم',
        'status' => 'الحالة',
        'full_name' => 'الاسم الكامل',
        'phone' => 'رقم الجوال',
        'response_text' => 'نص الرد',
        'response_date' => 'تاريخ الرد',
        'responded_by' => 'تم الرد بواسطة',
        'created_at' => 'تاريخ الإنشاء',
    ],

    'sections' => [
        'search_settings' => 'البحث والإعدادات',
        'basic_info' => 'المعلومات الأساسية',
        'complainant_info' => 'معلومات مقدم الشكوى',
        'complaint_text' => 'نص الشكوى',
        'response' => 'الرد على الشكوى',
        'response_form' => 'الرد على الشكوى',
    ],

    'status' => [
        'pending' => 'قيد الانتظار',
        'in_progress' => 'قيد المعالجة',
        'resolved' => 'تم الحل',
        'rejected' => 'مرفوضة',
        'responded' => 'تم الرد',
    ],

    'placeholders' => [
        'id_num' => 'أدخل رقم الهوية',
        'response' => 'اكتب الرد هنا...',
    ],

    'pagination_info' => 'عرض :from - :to من :total',

    'dialogs' => [
        'delete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الشكوى ؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد استعادة الشكوى ؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الشكوى نهائياً ؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];
