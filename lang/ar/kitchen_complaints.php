<?php

return [
    'plural' => 'شكاوى المطبخ',
    'singular' => 'شكوى المطبخ',
    'permission' => 'متابعة شكاوى المطبخ',
    'actions' => [
        'list' => 'قائمة شكاوى المطبخ',
        'show' => 'تفاصيل الشكوى',
        'respond' => 'الرد على الشكوى',
        'delete' => 'حذف الشكوى',
    ],
    'attributes' => [
        'camp_name' => 'اسم المخيم',
        'card_number' => 'رقم البطاقة',
        'complainant_name' => 'اسم مقدم الشكوى',
        'mobile_number' => 'رقم جوال التواصل',
        'complaint_title' => 'عنوان الشكوى',
        'complaint_text' => 'نص الشكوى',
        'status' => 'الحالة',
        'response' => 'الرد',
        'responded_at' => 'تاريخ الرد',
        'responded_by' => 'تم الرد بواسطة',
    ],
    'status' => [
        'pending' => 'قيد الانتظار',
        'in_progress' => 'قيد المعالجة',
        'resolved' => 'تم الحل',
        'rejected' => 'مرفوض',
    ],
    'messages' => [
        'responded' => 'تم إرسال الرد بنجاح.',
        'deleted' => 'تم حذف الشكوى بنجاح.',
    ],
];
