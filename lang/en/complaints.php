<?php

return [
    'singular' => 'Complaint',
    'plural' => 'Complaint',
    'empty' => 'There are no Complaint yet.',
    'count' => 'Complaint Count.',
    'search' => 'Search',
    'select' => 'Select Complaint',
    'permission' => 'Manage Complaint',
    'trashed' => 'Trashed Complaint',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for Complaint',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new Complaint',
        'show' => 'Show Complaint',
        'edit' => 'Edit Complaint',
        'delete' => 'Delete Complaint',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
        'empty_filters' => 'ُEmpty Filters',
    ],
    'messages' => [
        'created' => 'The Complaint has been created successfully.',
        'updated' => 'The Complaint has been updated successfully.',
        'deleted' => 'The Complaint has been deleted successfully.',
        'restored' => 'The Complaint has been restored successfully.',
    ],
    'attributes' => [
        'id_num' => 'Pepole id number',
        'complaint_title' => 'Complaint name',
        'complaint_text' => 'Complaint description text',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the Complaint ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the Complaint ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the Complaint forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
