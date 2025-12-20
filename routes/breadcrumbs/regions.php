<?php

Breadcrumbs::for('dashboard.regions.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('regions.plural'), route('dashboard.regions.index'));
});

Breadcrumbs::for('dashboard.regions.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.regions.index');
    $breadcrumb->push(trans('regions.trashed'), route('dashboard.regions.trashed'));
});

Breadcrumbs::for('dashboard.regions.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.regions.index');
    $breadcrumb->push(trans('regions.actions.create'), route('dashboard.regions.create'));
});

Breadcrumbs::for('dashboard.regions.show', function ($breadcrumb, $region) {
    $breadcrumb->parent('dashboard.regions.index');
    $breadcrumb->push($region->name, route('dashboard.regions.show', $region));
});

Breadcrumbs::for('dashboard.regions.edit', function ($breadcrumb, $region) {
    $breadcrumb->parent('dashboard.regions.show', $region);
    $breadcrumb->push(trans('regions.actions.edit'), route('dashboard.regions.edit', $region));
});
