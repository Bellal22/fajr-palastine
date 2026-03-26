<?php

Breadcrumbs::for('dashboard.home', function ($breadcrumb) {
    $breadcrumb->push(trans('dashboard.home'), route('dashboard.home'));
});

Breadcrumbs::for('dashboard.people.updated', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.people.index');
    $breadcrumb->push('البيانات المحدثة', route('dashboard.people.updated'));
});

Breadcrumbs::for('dashboard.people.unsynced', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.people.index');
    $breadcrumb->push('الأشخاص غير المزامنين', route('dashboard.people.unsynced'));
});
