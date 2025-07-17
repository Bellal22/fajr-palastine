<?php

return [
    'actions' => [
        'delete' => 'حذف المحدد',
        'restore' => 'استعادة المحدد',
        'export' => 'تصدير المحدد',
    ],
    'messages' => [
        'deleted' => 'تم حذف :type بنجاح.',
        'restored' => 'تم استعادة :type بنجاح.',
        'exported' => 'تم تصدير :type بنجاح.',
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
        'exported' => [
            'title' => 'تحذير !',
            'info' => 'هل أنت متأكد انك تريد تصدير :type نهائياً',
            'confirm' => 'تصدير',
            'cancel' => 'إلغاء',
        ],
    ],
];
