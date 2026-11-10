<?php

Breadcrumbs::for('dashboard.researchers.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('researchers.plural'), route('dashboard.researchers.index'));
});

Breadcrumbs::for('dashboard.researchers.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.researchers.index');
    $breadcrumb->push(trans('researchers.trashed'), route('dashboard.researchers.trashed'));
});

Breadcrumbs::for('dashboard.researchers.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.researchers.index');
    $breadcrumb->push(trans('researchers.actions.create'), route('dashboard.researchers.create'));
});

Breadcrumbs::for('dashboard.researchers.show', function ($breadcrumb, $researcher) {
    if (is_string($researcher) || is_numeric($researcher)) {
        $researcher = \App\Models\User::find($researcher) ?: $researcher;
    }
    $breadcrumb->parent('dashboard.researchers.index');
    $name = is_object($researcher) ? $researcher->name : $researcher;
    $breadcrumb->push($name, route('dashboard.researchers.show', $researcher));
});

Breadcrumbs::for('dashboard.researchers.edit', function ($breadcrumb, $researcher) {
    if (is_string($researcher) || is_numeric($researcher)) {
        $researcher = \App\Models\User::find($researcher) ?: $researcher;
    }
    $breadcrumb->parent('dashboard.researchers.show', $researcher);
    $breadcrumb->push(trans('researchers.actions.edit'), route('dashboard.researchers.edit', $researcher));
});
