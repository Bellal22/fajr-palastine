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
Breadcrumbs::for('errors.404', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('الصفحة غير موجودة');
});

Breadcrumbs::for('errors.403', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('غير مصرح لك بالدخول');
});

Breadcrumbs::for('errors.500', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('خطأ في الخادم');
});

Breadcrumbs::for('errors.503', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('الخدمة غير متوفرة حالياً');
});
