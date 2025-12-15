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
