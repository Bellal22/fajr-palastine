<?php

return [
    'actions' => [
        'delete' => 'Delete Selected',
        'export' => 'Export Selected',
    ],
    'messages' => [
        'deleted' => 'The :type has been selected successfully.',
        'exported' => 'The :type has been selected successfully.',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the :type ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'export' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to export the :type ?',
            'confirm' => 'Export',
            'cancel' => 'Cancel',
        ],
    ],
];
