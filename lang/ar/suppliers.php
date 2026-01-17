<?php

return [
    'singular' => 'المورد',
    'plural' => 'الموردين',
    'empty' => 'لا يوجد موردين',
    'empty_hint' => 'لا يوجد موردين حالياً.',
    'count' => 'عدد الموردين',
    'search' => 'بحث',
    'select' => 'اختر المورد',
    'permission' => 'إدارة الموردين',
    'trashed' => 'الموردين المحذوفة',
    'perPage' => 'عدد النتائج بالصفحة',
    'filter' => 'ابحث عن مورد',
    'pagination_results' => 'عرض :from - :to من أصل :total نتيجة',

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'attachments' => 'المرفقات',
        'quick_info' => 'معلومات سريعة',
        'statistics' => 'إحصائيات',
    ],

    'types' => [
        'donor' => 'جهة مانحة',
        'operator' => 'جهة مشغلة',
    ],

    'status' => [
        'has_image' => 'متوفرة',
        'no_image' => 'غير متوفرة',
        'has_document' => 'متوفر',
        'no_document' => 'غير متوفر',
    ],

    'show' => [
        'added' => 'تمت الإضافة',
        'created_date' => 'تاريخ الإنشاء',
        'last_update' => 'آخر تحديث',
    ],

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'إضافة مورد',
        'show' => 'عرض التفاصيل',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'restore' => 'استعادة',
        'forceDelete' => 'حذف نهائي',
        'save' => 'حفظ',
        'filter' => 'بحث',
        'reset' => 'إعادة تعيين',
        'options' => 'خيارات',
        'actions' => 'الإجراءات',
        'view_document' => 'عرض المستند',
        'download_document' => 'تحميل المستند',
    ],

    'messages' => [
        'created' => 'تم إضافة المورد بنجاح.',
        'updated' => 'تم تعديل المورد بنجاح.',
        'deleted' => 'تم حذف المورد بنجاح.',
        'restored' => 'تم استعادة المورد بنجاح.',
        'forceDeleted' => 'تم حذف المورد نهائياً بنجاح.',
        'no_description' => 'لا يوجد وصف',
        'no_image' => 'لا توجد صورة',
        'no_document' => 'لا يوجد مستند',
    ],

    'attributes' => [
        'name' => 'اسم المورد',
        'description' => 'الوصف',
        'type' => 'النوع',
        'image' => 'الصورة',
        'document' => 'المستند',
        'created_at' => 'تاريخ الإضافة',
        'updated_at' => 'تاريخ التحديث',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم المورد',
        'description' => 'أدخل وصف المورد',
        'select_type' => '-- اختر النوع --',
    ],

    'dialogs' => [
        'delete' => 'هل أنت متأكد من حذف هذا المورد؟',
        'restore' => 'هل أنت متأكد من استعادة هذا المورد؟',
        'forceDelete' => 'هل أنت متأكد من حذف هذا المورد نهائياً؟',

        'delete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف المورد؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد استعادة المورد؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف المورد نهائياً؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];