<?php

Breadcrumbs::for('dashboard.neighborhoods.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('neighborhoods.plural'), route('dashboard.neighborhoods.index'));
});

Breadcrumbs::for('dashboard.neighborhoods.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.neighborhoods.index');
    $breadcrumb->push(trans('neighborhoods.trashed'), route('dashboard.neighborhoods.trashed'));
});

Breadcrumbs::for('dashboard.neighborhoods.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.neighborhoods.index');
    $breadcrumb->push(trans('neighborhoods.actions.create'), route('dashboard.neighborhoods.create'));
});

Breadcrumbs::for('dashboard.neighborhoods.show', function ($breadcrumb, $neighborhood) {
    $breadcrumb->parent('dashboard.neighborhoods.index');
    $breadcrumb->push($neighborhood->name, route('dashboard.neighborhoods.show', $neighborhood));
});

Breadcrumbs::for('dashboard.neighborhoods.edit', function ($breadcrumb, $neighborhood) {
    $breadcrumb->parent('dashboard.neighborhoods.show', $neighborhood);
    $breadcrumb->push(trans('neighborhoods.actions.edit'), route('dashboard.neighborhoods.edit', $neighborhood));
});
