<?php

Breadcrumbs::for('dashboard.locations.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('locations.plural'), route('dashboard.locations.index'));
});

Breadcrumbs::for('dashboard.locations.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.locations.index');
    $breadcrumb->push(trans('locations.trashed'), route('dashboard.locations.trashed'));
});

Breadcrumbs::for('dashboard.locations.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.locations.index');
    $breadcrumb->push(trans('locations.actions.create'), route('dashboard.locations.create'));
});

Breadcrumbs::for('dashboard.locations.show', function ($breadcrumb, $location) {
    $breadcrumb->parent('dashboard.locations.index');
    $breadcrumb->push($location->name, route('dashboard.locations.show', $location));
});

Breadcrumbs::for('dashboard.locations.edit', function ($breadcrumb, $location) {
    $breadcrumb->parent('dashboard.locations.show', $location);
    $breadcrumb->push(trans('locations.actions.edit'), route('dashboard.locations.edit', $location));
});
