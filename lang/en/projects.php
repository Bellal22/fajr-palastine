<?php

return [
    'singular' => 'Project',
    'plural' => 'Projects',
    'empty' => 'There are no projects yet.',
    'count' => 'Projects Count.',
    'search' => 'Search',
    'select' => 'Select Project',
    'permission' => 'Manage projects',
    'trashed' => 'Trashed projects',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for project',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new project',
        'show' => 'Show project',
        'edit' => 'Edit project',
        'delete' => 'Delete project',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
    ],
    'messages' => [
        'created' => 'The project has been created successfully.',
        'updated' => 'The project has been updated successfully.',
        'deleted' => 'The project has been deleted successfully.',
        'restored' => 'The project has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Project name',
    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the project ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the project ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the project forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
