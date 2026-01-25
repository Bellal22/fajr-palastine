<?php

return [
    'singular' => 'المدينة',
    'plural' => 'المدن',
    'empty' => 'لا يوجد مدن حتى الان',
    'empty_hint' => 'لا توجد مدن حالياً. يمكنك إضافة مدينة جديدة من الزر أعلاه.',
    'count' => 'عدد المدن',
    'search' => 'بحث',
    'select' => 'اختر المدينة',
    'permission' => 'ادارة المدن',
    'trashed' => 'المدن المحذوفة',
    'perPage' => 'عدد النتائج بالصفحة',
    'filter' => 'ابحث عن مدينة',
    'city_details' => 'تفاصيل المدينة',
    'pagination_info' => 'عرض :from إلى :to من أصل :total',

    'sections' => [
        'search_settings' => 'إعدادات البحث',
        'basic_info' => 'المعلومات الأساسية',
        'city_details' => 'تفاصيل المدينة',
    ],

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'اضافة مدينة',
        'show' => 'عرض المدينة',
        'edit' => 'تعديل المدينة',
        'delete' => 'حذف المدينة',
        'restore' => 'استعادة',
        'forceDelete' => 'حذف نهائي',
        'options' => 'خيارات',
        'save' => 'حفظ',
        'filter' => 'بحث',
        'reset' => 'تفريغ الحقول',
        'clear_filter' => 'مسح الفلتر',
        'actions' => 'الإجراءات',
    ],

    'messages' => [
        'created' => 'تم اضافة المدينة بنجاح.',
        'updated' => 'تم تعديل المدينة بنجاح.',
        'deleted' => 'تم حذف المدينة بنجاح.',
        'restored' => 'تم استعادة المدينة بنجاح.',
        'forceDeleted' => 'تم حذف المدينة نهائياً.',
    ],

    'attributes' => [
        'name' => 'اسم المدينة',
        'created_at' => 'تاريخ الإضافة',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم المدينة للبحث...',
    ],

    'filter_placeholders' => [
        'name' => 'ابحث باسم المدينة...',
    ],

    'dialogs' => [
        'delete' => [
            'title' => 'تحذير !',
            'info' => 'هل انت متأكد انك تريد حذف المدينة ؟',
            'confirm' => 'حذف',
            'cancel' => 'الغاء',
        ],
        'restore' => [
            'title' => 'تحذير !',
            'info' => 'هل انت متأكد انك تريد استعادة المدينة ؟',
            'confirm' => 'استعادة',
            'cancel' => 'الغاء',
        ],
        'forceDelete' => [
            'title' => 'تحذير !',
            'info' => 'هل انت متأكد انك تريد حذف المدينة نهائياً ؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'الغاء',
        ],
    ],
];
