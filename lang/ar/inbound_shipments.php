<?php

return [
    'singular' => 'إرسالية الوارد',
    'plural' => 'الإرساليات الواردة',
    'empty' => 'لا يوجد إرساليات واردة',
    'empty_hint' => 'لا يوجد إرساليات واردة حالياً.',
    'count' => 'عدد الإرساليات الواردة',
    'search' => 'بحث',
    'select' => 'اختر إرسالية الوارد',
    'permission' => 'إدارة الإرساليات الواردة',
    'trashed' => 'الإرساليات الواردة المحذوفة',
    'perPage' => 'عدد النتائج بالصفحة',
    'filter' => 'ابحث عن إرسالية وارد',
    'pagination_results' => 'عرض :from - :to من أصل :total نتيجة',

    'show' => [
        'title' => 'إرسالية وارد #:number',
    ],

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'shipment_info' => 'معلومات الإرسالية',
        'single_items' => 'بيان الأصناف',
        'ready_packages' => 'بيان الطرود الجاهزة',
        'statistics' => 'إحصائيات',
        'notes' => 'ملاحظات',
    ],

    'types' => [
        'single_item' => 'صنف مفرد',
        'ready_package' => 'طرد جاهز',
    ],

    'labels' => [
        'packages' => 'طرد',
        'items' => 'صنف',
    ],

    'statistics' => [
        'items_count' => 'عدد الأصناف',
        'total_quantity' => 'الكمية الإجمالية',
        'total_weight' => 'الوزن الإجمالي',
    ],

    'table' => [
        'unit_weight' => 'وزن الوحدة (كجم)',
        'total_weight' => 'الوزن الإجمالي (كجم)',
        'total' => 'الإجمالي',
    ],

    'units' => [
        'kg' => 'كجم',
    ],

    'actions' => [
        'list' => 'عرض الكل',
        'create' => 'إضافة إرسالية وارد',
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
        'export_pdf' => 'تصدير PDF',
    ],

    'buttons' => [
        'add_item' => 'إضافة صنف',
        'remove' => 'حذف',
        'add_package' => 'إضافة طرد',
        'export_pdf' => 'تصدير PDF',
    ],

    'messages' => [
        'created' => 'تم إضافة إرسالية الوارد بنجاح.',
        'updated' => 'تم تعديل إرسالية الوارد بنجاح.',
        'deleted' => 'تم حذف إرسالية الوارد بنجاح.',
        'restored' => 'تم استعادة إرسالية الوارد بنجاح.',
        'forceDeleted' => 'تم حذف إرسالية الوارد نهائياً بنجاح.',
        'no_supplier' => 'غير محدد',
        'no_items' => 'لا توجد أصناف في هذه الإرسالية',
        'no_packages' => 'لا توجد طرود في هذه الإرسالية',
    ],

    'alerts' => [
        'min_item' => 'يجب أن يكون هناك صنف واحد على الأقل',
        'min_package' => 'يجب أن يكون هناك طرد واحد على الأقل',
    ],

    'attributes' => [
        'supplier_id' => 'المورد',
        'shipment_number' => 'رقم الإرسالية',
        'inbound_type' => 'نوع الوارد',
        'notes' => 'ملاحظات',
        'name' => 'اسم إرسالية الوارد',
        'description' => 'الوصف',
        'type' => 'النوع',
        'weight' => 'الوزن',
        'quantity' => 'الكمية',
        'item_name' => 'اسم الصنف',
        'weight_kg' => 'الوزن (كجم)',
        'package_name' => 'اسم الطرد',
        'items_count' => 'عدد الأصناف',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'placeholders' => [
        'name' => 'ابحث عن إرسالية',
        'supplier' => '-- اختر الجهة المانحة --',
        'shipment_number' => 'أدخل رقم الإرسالية',
        'inbound_type' => '-- اختر نوع الوارد --',
        'item_name' => 'أدخل اسم الصنف',
        'description' => 'أدخل الوصف',
        'quantity' => 'أدخل الكمية',
        'weight' => 'أدخل الوزن',
        'package_name' => 'أدخل اسم الطرد',
        'notes' => 'أدخل ملاحظات إضافية',
    ],

    'dialogs' => [
        'delete' => 'هل أنت متأكد من حذف هذه الإرسالية؟',
        'restore' => 'هل أنت متأكد من استعادة هذه الإرسالية؟',
        'forceDelete' => 'هل أنت متأكد من حذف هذه الإرسالية نهائياً؟',

        'delete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف إرسالية الوارد؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],
        'restore_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد استعادة إرسالية الوارد؟',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],
        'forceDelete_detailed' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد أنك تريد حذف إرسالية الوارد نهائياً؟',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],
    ],
];