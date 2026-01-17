<?php

return [
    'actions' => [
        'delete' => 'حذف المحدد',
        'restore' => 'استعادة المحدد',
        'export' => 'تصدير المحدد',
        'assignBlock' => 'ربط بالمندوب',
        'assignToCurrentSupervisor' => 'ربط الفرد بك',
        'assign' => 'تعيين مسؤول',
        'deleteAreaResponsibles' => 'حذف مسؤول المنطقة',
        'assignResponsibleAndBlock' => 'تخصيص مسؤول ومندوب',
    ],

    'messages' => [
        'deleted' => 'تم حذف :type بنجاح.',
        'restored' => 'تم استعادة :type بنجاح.',
        'exported' => 'تم تصدير :type بنجاح.',
        'no_items_selected' => 'الرجاء تحديد عنصر واحد على الأقل',
        'no_people_selected' => 'يرجى تحديد أشخاص أولاً',
        'select_responsible_and_block' => 'يرجى اختيار مسؤول المنطقة والمندوب',
        'loading' => 'جارِ التحميل...',
        'no_blocks_for_responsible' => 'لا توجد مندوبين لهذا المسؤول',
        'loading_error' => 'حدث خطأ في التحميل',
        'updating' => 'جاري التحديث...',
        'processing' => 'جاري المعالجة...',
        'assigned_successfully' => 'تم التخصيص بنجاح',
        'unexpected_error' => 'حدث خطأ غير متوقع',
        'assignment_error' => 'حدث خطأ في التخصيص',
        'session_expired' => 'انتهت صلاحية الجلسة. يرجى إعادة تحميل الصفحة',
        'not_found' => 'الرابط غير موجود',
        'server_error' => 'خطأ في الخادم',
        'connection_failed' => 'فشل الاتصال بالخادم',
        'success' => 'تمت العملية بنجاح',
        'error' => 'حدث خطأ أثناء تنفيذ العملية',
    ],

    'dialogs' => [
        'delete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد حذف :type',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],

        'restore' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد استعادة :type',
            'confirm' => 'استعادة',
            'cancel' => 'إلغاء',
        ],

        'forceDelete' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد حذف :type نهائياً',
            'confirm' => 'حذف نهائي',
            'cancel' => 'إلغاء',
        ],

        'export' => [
            'title' => 'تصدير العناصر المحددة',
            'info' => 'هل أنت متأكد انك تريد تصدير :type المحددة؟',
            'confirm' => 'تصدير',
            'cancel' => 'إلغاء',
        ],

        'assign' => [
            'title' => 'تعيين مسؤول منطقة',
            'info' => 'اختر مسؤول المنطقة للعناصر المحددة',
            'confirm' => 'تعيين',
            'cancel' => 'إلغاء',
        ],

        'assignBlock' => [
            'title' => 'تعيين المندوب',
            'info' => 'سيتم تعيين المندوب المحدد للأشخاص المختارين',
            'select_block' => 'اختر المندوب',
            'block_label' => 'المندوب',
            'confirm' => 'تأكيد',
            'cancel' => 'إلغاء',
        ],

        'deleteAreaResponsibles' => [
            'title' => 'حذف المسؤولين',
            'info' => 'هل أنت متأكد من حذف مسؤولي المنطقة من العناصر المحددة؟',
            'confirm' => 'حذف',
            'cancel' => 'إلغاء',
        ],

        'assignResponsibleAndBlock' => [
            'title' => 'تخصيص مسؤول المنطقة والمندوب',
            'info' => 'اختر مسؤول المنطقة والمندوب للأشخاص المحددين',
            'area_responsible_label' => 'مسؤول المنطقة',
            'select_area_responsible' => 'اختر مسؤول المنطقة',
            'block_label' => 'المندوب',
            'select_block' => 'اختر المندوب',
            'select_responsible_first' => 'يجب اختيار مسؤول المنطقة أولاً',
            'confirm' => 'تحديث',
            'cancel' => 'إلغاء',
        ],

        'bulkDeleteResponsible' => [
            'title' => 'تأكيد حذف المسؤول',
            'title_with_count' => 'تأكيد حذف المسؤول من :count شخص',
            'info' => 'هل أنت متأكد من إلغاء ربط المسؤول والمندوب من الأشخاص المحددين؟',
            'warning' => 'سيتم إزالة مسؤول المنطقة والمندوب من جميع الأشخاص المحددين',
            'confirm' => 'تأكيد حذف المسؤول',
            'cancel' => 'إلغاء',
        ],
    ],
];