<?php

Breadcrumbs::for('dashboard.internal_packages.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('internal_packages.plural'), route('dashboard.internal_packages.index'));
});

Breadcrumbs::for('dashboard.internal_packages.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.internal_packages.index');
    $breadcrumb->push(trans('internal_packages.trashed'), route('dashboard.internal_packages.trashed'));
});

Breadcrumbs::for('dashboard.internal_packages.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.internal_packages.index');
    $breadcrumb->push(trans('internal_packages.actions.create'), route('dashboard.internal_packages.create'));
});

Breadcrumbs::for('dashboard.internal_packages.show', function ($breadcrumb, $internal_package) {
    $breadcrumb->parent('dashboard.internal_packages.index');
    $breadcrumb->push($internal_package->name, route('dashboard.internal_packages.show', $internal_package));
});

Breadcrumbs::for('dashboard.internal_packages.edit', function ($breadcrumb, $internal_package) {
    $breadcrumb->parent('dashboard.internal_packages.show', $internal_package);
    $breadcrumb->push(trans('internal_packages.actions.edit'), route('dashboard.internal_packages.edit', $internal_package));
});
