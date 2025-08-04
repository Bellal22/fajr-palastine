<?php

return [
    'actions' => [
        'delete' => 'Delete Selected',
        'export' => 'Export Selected',
        'assignBlock' => 'Assign Block',
        'assignToCurrentSupervisor' => 'Assign Person To You',
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
        'assign' => [
            'title' => 'Assign Block',
            'cancel' => 'Cancel',
            'confirm' => 'Confirm',
            'info' => 'The selected block will be assigned to chosen people'
        ],
        'export' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to export the :type ?',
            'confirm' => 'Export',
            'cancel' => 'Cancel',
        ],
    ],
];