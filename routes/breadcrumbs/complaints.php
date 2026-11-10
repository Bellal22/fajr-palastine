<?php

Breadcrumbs::for('dashboard.complaints.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('complaints.plural'), route('dashboard.complaints.index'));
});

Breadcrumbs::for('dashboard.complaints.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaints.index');
    $breadcrumb->push(trans('complaints.trashed'), route('dashboard.complaints.trashed'));
});

Breadcrumbs::for('dashboard.complaints.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaints.index');
    $breadcrumb->push(trans('complaints.actions.create'), route('dashboard.complaints.create'));
});

Breadcrumbs::for('dashboard.complaints.show', function ($breadcrumb, $complaint) {
    $breadcrumb->parent('dashboard.complaints.index');
    $breadcrumb->push($complaint->complaint_title, route('dashboard.complaints.show', $complaint));
});

Breadcrumbs::for('dashboard.complaints.edit', function ($breadcrumb, $complaint) {
    $breadcrumb->parent('dashboard.complaints.show', $complaint);
    $breadcrumb->push(trans('complaints.actions.edit'), route('dashboard.complaints.edit', $complaint));
});

Breadcrumbs::for('dashboard.complaint_response_templates.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaints.index');
    $breadcrumb->push('الردود الجاهزة', route('dashboard.complaint_response_templates.index'));
});

Breadcrumbs::for('dashboard.complaint_response_templates.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaint_response_templates.index');
    $breadcrumb->push('إضافة رد جاهز', route('dashboard.complaint_response_templates.create'));
});

Breadcrumbs::for('dashboard.complaint_response_templates.edit', function ($breadcrumb, $template = null) {
    $breadcrumb->parent('dashboard.complaint_response_templates.index');
    $breadcrumb->push('تعديل الرد الجاهز', $template ? route('dashboard.complaint_response_templates.edit', $template) : '#');
});

Breadcrumbs::for('dashboard.complaint_types.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaints.index');
    $breadcrumb->push('أنواع الشكاوى', route('dashboard.complaint_types.index'));
});

Breadcrumbs::for('dashboard.complaint_types.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.complaint_types.index');
    $breadcrumb->push('إضافة نوع جديد', route('dashboard.complaint_types.create'));
});

Breadcrumbs::for('dashboard.complaint_types.edit', function ($breadcrumb, $type = null) {
    $breadcrumb->parent('dashboard.complaint_types.index');
    $breadcrumb->push('تعديل نوع الشكوى', $type ? route('dashboard.complaint_types.edit', $type) : '#');
});
