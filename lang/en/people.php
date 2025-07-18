<?php

return [
    'singular' => 'Person',
    'plural' => 'People',
    'empty' => 'There are no people yet.',
    'count' => 'People Count.',
    'search' => 'Search',
    'select' => 'Select Person',
    'permission' => 'Manage people',
    'trashed' => 'Trashed people',
    'perPage' => 'Results Per Page',
    'filter' => 'Search for person',
    'actions' => [
        'list' => 'List All',
        'create' => 'Create a new person',
        'show' => 'Show person',
        'edit' => 'Edit person',
        'delete' => 'Delete person',
        'restore' => 'Restore',
        'forceDelete' => 'Delete Forever',
        'options' => 'Options',
        'save' => 'Save',
        'filter' => 'Filter',
        'empty_filters' => 'ُEmpty Filters',
    ],
    'messages' => [
        'created' => 'The person has been created successfully.',
        'updated' => 'The person has been updated successfully.',
        'deleted' => 'The person has been deleted successfully.',
        'restored' => 'The person has been restored successfully.',
    ],
    'attributes' => [
        'name' => 'Person name',
        'id_num' => 'ID Number',
        'first_name' => 'First name',
        'father_name' => 'Father name',
        'grandfather_name' => 'Grandfather name',
        'family_name' => 'Family name',
        'passkey'=>'Password',
        'dob' => 'Date Of Birthday',
        'phone' => 'Phone',
        'gender'=>'Gender',
        'employment_status' => 'Employment status',
        'social_status' => 'Social status',
        'city' => 'City',
        'current_city' => 'Current city',
        'neighborhood' => 'Neighborhood',
        'landmark' => 'Landmark',
        'housing_type' => 'Housing type',
        'housing_damage_status' => 'Housing damage status',
        'relatives_count' => 'Family member counts',
        'relationship'=> 'Relationship',
        'has_condition' => 'Has condition',
        'condition_description' => 'Condition description',
        'person_status' => 'Status'

    ],
    'dialogs' => [
        'delete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the person ?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'restore' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to restore the person ?',
            'confirm' => 'Restore',
            'cancel' => 'Cancel',
        ],
        'forceDelete' => [
            'title' => 'Warning !',
            'info' => 'Are you sure you want to delete the person forever ?',
            'confirm' => 'Delete Forever',
            'cancel' => 'Cancel',
        ],
    ],
];
