<?php

return [
    'singular' => 'Location',
    'plural' => 'Locations',
    'empty' => 'There are no locations yet.',
    'count' => 'Locations Count.',
    'search' => 'Search',
    'select' => 'Select Location',
    'permission' => 'Manage locations',
    'trashed' => 'Trashed locations',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for location',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new location',
        'show' => 'Show location',
        'edit' => 'Edit location',
        'delete' => 'Delete location',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
    ],
    'messages' => [
        'created' => 'The location has been created successfully.',
        'updated' => 'The location has been updated successfully.',
        'deleted' => 'The location has been deleted successfully.',
        'restored' => 'The location has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Location name',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the location ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the location ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the location forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
