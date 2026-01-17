<?php

return [
    'singular' => 'الصنف',
    'plural' => 'الأصناف',
    'empty' => 'لا يوجد أصناف',
    'empty_hint' => 'لا يوجد أصناف حالياً.',
    'count' => 'عدد الأصناف',
    'search' => 'بحث',
    'select' => 'اختر الصنف',
    'permission' => 'إدارة الأصناف',
    'trashed' => 'الأصناف المحذوفة',
    'filter' => 'بحث وفلترة',
    'perPage' => 'عدد النتائج بالصفحة',
    'pagination_results' => 'عرض :from - :to من أصل :total نتيجة',

    'show' => [
        'title' => 'صنف: :name',
        'created_date' => 'تاريخ الإنشاء',
        'last_update' => 'آخر تحديث',
    ],

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'item_info' => 'معلومات الصنف',
        'properties' => 'التفاصيل والخصائص',
        'additional_settings' => 'الإعدادات الإضافية',
        'statistics' => 'إحصائيات',
    ],

    'types' => [
        'single' => 'صنف مفرد',
        'package' => 'طرد جاهز',
    ],

    'table' => [
        'total_weight' => 'الوزن الإجمالي (كجم)',
    ],

    'statistics' => [
        'total_weight' => 'الوزن الإجمالي',
    ],

    'units' => [
        'kg' => 'كجم',
    ],

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'إضافة صنف',
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
        'back_to_list' => 'العودة للقائمة',
    ],

    'messages' => [
        'created' => 'تم إضافة الصنف بنجاح.',
        'updated' => 'تم تعديل الصنف بنجاح.',
        'deleted' => 'تم حذف الصنف بنجاح.',
        'restored' => 'تم استعادة الصنف بنجاح.',
        'forceDeleted' => 'تم حذف الصنف نهائياً بنجاح.',
        'no_description' => 'لا يوجد وصف',
        'no_shipment' => 'بدون شحنة',
    ],

    'attributes' => [
        'name' => 'اسم الصنف',
        'description' => 'الوصف',
        'code' => 'الكود',
        'category' => 'الفئة',
        'unit' => 'الوحدة',
        'price' => 'السعر',
        'type' => 'النوع',
        'inbound_shipment_id' => 'الشحنة الواردة',
        'quantity' => 'الكمية',
        'weight' => 'الوزن (كجم)',
        'package' => 'طرد جاهز',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم الصنف',
        'description' => 'أدخل وصف الصنف',
        'code' => 'أدخل الكود',
        'type' => 'أدخل رقم النوع',
        'weight' => 'أدخل الوزن',
        'quantity' => 'أدخل الكمية',
    ],

    'hints' => [
        'type' => 'رقم يمثل تصنيف الصنف (0-255)',
        'weight' => 'الوزن بالكيلوجرام',
        'quantity' => 'الكمية المتوفرة',
        'package' => 'حدد هذا الخيار إذا كان الصنف عبارة عن طرد جاهز',
    ],

    'dialogs' => [
        'delete' => 'هل أنت متأكد من حذف هذا الصنف؟',
        'restore' => 'هل أنت متأكد من استعادة هذا الصنف؟',
        'forceDelete' => 'هل أنت متأكد من حذف هذا الصنف نهائياً؟',

        'delete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الصنف؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد استعادة الصنف؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الصنف نهائياً؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];