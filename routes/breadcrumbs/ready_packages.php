<?php

Breadcrumbs::for('dashboard.ready_packages.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('ready_packages.plural'), route('dashboard.ready_packages.index'));
});

Breadcrumbs::for('dashboard.ready_packages.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.ready_packages.index');
    $breadcrumb->push(trans('ready_packages.trashed'), route('dashboard.ready_packages.trashed'));
});

Breadcrumbs::for('dashboard.ready_packages.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.ready_packages.index');
    $breadcrumb->push(trans('ready_packages.actions.create'), route('dashboard.ready_packages.create'));
});

Breadcrumbs::for('dashboard.ready_packages.show', function ($breadcrumb, $ready_package) {
    $breadcrumb->parent('dashboard.ready_packages.index');
    $breadcrumb->push($ready_package->name, route('dashboard.ready_packages.show', $ready_package));
});

Breadcrumbs::for('dashboard.ready_packages.edit', function ($breadcrumb, $ready_package) {
    $breadcrumb->parent('dashboard.ready_packages.show', $ready_package);
    $breadcrumb->push(trans('ready_packages.actions.edit'), route('dashboard.ready_packages.edit', $ready_package));
});
