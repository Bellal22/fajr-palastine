<?php

Breadcrumbs::for('dashboard.supervisors.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('supervisors.plural'), route('dashboard.supervisors.index'));
});

Breadcrumbs::for('dashboard.supervisors.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.supervisors.index');
    $breadcrumb->push(trans('supervisors.trashed'), route('dashboard.supervisors.trashed'));
});

Breadcrumbs::for('dashboard.supervisors.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.supervisors.index');
    $breadcrumb->push(trans('supervisors.actions.create'), route('dashboard.supervisors.create'));
});

Breadcrumbs::for('dashboard.supervisors.show', function ($breadcrumb, $supervisor) {
    if (is_string($supervisor) || is_numeric($supervisor)) {
        $supervisor = \App\Models\User::find($supervisor) ?: $supervisor;
    }
    $breadcrumb->parent('dashboard.supervisors.index');
    $name = is_object($supervisor) ? $supervisor->name : $supervisor;
    $breadcrumb->push($name, route('dashboard.supervisors.show', $supervisor));
});

Breadcrumbs::for('dashboard.supervisors.edit', function ($breadcrumb, $supervisor) {
    if (is_string($supervisor) || is_numeric($supervisor)) {
        $supervisor = \App\Models\User::find($supervisor) ?: $supervisor;
    }
    $breadcrumb->parent('dashboard.supervisors.show', $supervisor);
    $breadcrumb->push(trans('supervisors.actions.edit'), route('dashboard.supervisors.edit', $supervisor));
});
