<?php

return [
    'singular' => 'Family',
    'plural' => 'Families',
    'empty' => 'There are no families yet.',
    'count' => 'Families Count.',
    'search' => 'Search',
    'select' => 'Select Family',
    'permission' => 'Manage families',
    'trashed' => 'Trashed families',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for family',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new family',
        'show' => 'Show family',
        'edit' => 'Edit family',
        'delete' => 'Delete family',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
    ],
    'messages' => [
        'created' => 'The family has been created successfully.',
        'updated' => 'The family has been updated successfully.',
        'deleted' => 'The family has been deleted successfully.',
        'restored' => 'The family has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Family name',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the family ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the family ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the family forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
