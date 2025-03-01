<?php

return [
    'singular' => 'Neighborhood',
    'plural' => 'Neighborhoods',
    'empty' => 'There are no neighborhoods yet.',
    'count' => 'Neighborhoods Count.',
    'search' => 'Search',
    'select' => 'Select Neighborhood',
    'permission' => 'Manage neighborhoods',
    'trashed' => 'Trashed neighborhoods',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for neighborhood',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new neighborhood',
        'show' => 'Show neighborhood',
        'edit' => 'Edit neighborhood',
        'delete' => 'Delete neighborhood',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
    ],
    'messages' => [
        'created' => 'The neighborhood has been created successfully.',
        'updated' => 'The neighborhood has been updated successfully.',
        'deleted' => 'The neighborhood has been deleted successfully.',
        'restored' => 'The neighborhood has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Neighborhood name',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the neighborhood ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the neighborhood ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the neighborhood forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
