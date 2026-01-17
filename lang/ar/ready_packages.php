<?php

return [
    'singular' => 'الطرد الجاهز',
    'plural' => 'الطرود الجاهزة',
    'empty' => 'لا يوجد طرود جاهزة',
    'empty_hint' => 'لا يوجد طرود جاهزة حالياً.',
    'count' => 'عدد الطرود الجاهزة',
    'search' => 'بحث',
    'select' => 'اختر الطرد الجاهز',
    'permission' => 'إدارة الطرود الجاهزة',
    'trashed' => 'الطرود الجاهزة المحذوفة',
    'filter' => 'بحث وفلترة',
    'perPage' => 'عدد النتائج بالصفحة',
    'pagination_results' => 'عرض :from - :to من أصل :total نتيجة',

    'show' => [
        'title' => 'طرد جاهز: :name',
        'created_date' => 'تاريخ الإنشاء',
        'last_update' => 'آخر تحديث',
    ],

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'package_info' => 'معلومات الطرد',
        'properties' => 'التفاصيل والخصائص',
        'statistics' => 'إحصائيات',
        'package_contents' => 'محتويات الطرد',
    ],

    'types' => [
        'ready_package' => 'طرد جاهز',
    ],

    'table' => [
        'total_weight' => 'الوزن الإجمالي (كجم)',
        'item_name' => 'اسم الصنف',
        'description' => 'الوصف',
        'quantity' => 'الكمية',
        'weight' => 'الوزن (كجم)',
    ],

    'statistics' => [
        'total_weight' => 'الوزن الإجمالي',
    ],

    'units' => [
        'kg' => 'كجم',
    ],

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'إضافة طرد جاهز',
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
        'created' => 'تم إضافة الطرد الجاهز بنجاح.',
        'updated' => 'تم تعديل الطرد الجاهز بنجاح.',
        'deleted' => 'تم حذف الطرد الجاهز بنجاح.',
        'restored' => 'تم استعادة الطرد الجاهز بنجاح.',
        'forceDeleted' => 'تم حذف الطرد الجاهز نهائياً بنجاح.',
        'no_description' => 'لا يوجد وصف',
        'no_shipment' => 'بدون شحنة',
    ],

    'attributes' => [
        'name' => 'اسم الطرد',
        'description' => 'الوصف',
        'inbound_shipment_id' => 'الشحنة الواردة',
        'quantity' => 'الكمية',
        'weight' => 'الوزن (كجم)',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم الطرد',
        'description' => 'أدخل وصف الطرد',
        'inbound_shipment' => '-- اختر إرسالية الوارد --',
        'quantity' => 'أدخل الكمية',
        'weight' => 'الوزن بالكيلوجرام',
    ],

    'hints' => [
        'quantity' => 'عدد الطرود',
        'weight' => 'الوزن بالكيلوجرام لكل طرد',
    ],

    'dialogs' => [
        'delete' => 'هل أنت متأكد من حذف هذا الطرد الجاهز؟',
        'restore' => 'هل أنت متأكد من استعادة هذا الطرد الجاهز؟',
        'forceDelete' => 'هل أنت متأكد من حذف هذا الطرد الجاهز نهائياً؟',

        'delete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الطرد الجاهز؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد استعادة الطرد الجاهز؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف الطرد الجاهز نهائياً؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];