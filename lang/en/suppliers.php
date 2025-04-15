<?php

return [
    'singular' => 'Supplier',
    'plural' => 'Suppliers',
    'empty' => 'There are no suppliers yet.',
    'count' => 'Suppliers Count.',
    'search' => 'Search',
    'select' => 'Select Supplier',
    'permission' => 'Manage suppliers',
    'trashed' => 'Trashed suppliers',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for supplier',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new supplier',
        'show' => 'Show supplier',
        'edit' => 'Edit supplier',
        'delete' => 'Delete supplier',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
    ],
    'messages' => [
        'created' => 'The supplier has been created successfully.',
        'updated' => 'The supplier has been updated successfully.',
        'deleted' => 'The supplier has been deleted successfully.',
        'restored' => 'The supplier has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Supplier name',
        'description' => 'Supplier description',
        'image' => 'Supplier image',
        'document' => 'Supplier document',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the supplier ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the supplier ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the supplier forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
