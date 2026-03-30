<?php

Breadcrumbs::for('dashboard.mobile_app.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('إدارة الجوال', route('dashboard.mobile_app.index'));
});

Breadcrumbs::for('dashboard.mobile_app.users', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.mobile_app.index');
    $breadcrumb->push('مستخدمي التطبيق', route('dashboard.mobile_app.users'));
});

Breadcrumbs::for('dashboard.mobile_app.users.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.mobile_app.users');
    $breadcrumb->push('إضافة مستخدم', route('dashboard.mobile_app.users.create'));
});

Breadcrumbs::for('dashboard.mobile_app.nominate', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.mobile_app.index');
    $breadcrumb->push('ترشيح وتكليف الأسماء', route('dashboard.mobile_app.nominate'));
});

Breadcrumbs::for('dashboard.mobile_app.report', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.mobile_app.index');
    $breadcrumb->push('تقرير ترشيحات المشروع', route('dashboard.mobile_app.report'));
});

Breadcrumbs::for('dashboard.mobile_app.projects', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.mobile_app.index');
    $breadcrumb->push('إدارة المشاريع', route('dashboard.mobile_app.projects'));
});

