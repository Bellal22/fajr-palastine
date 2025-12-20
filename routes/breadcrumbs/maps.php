<?php

Breadcrumbs::for('dashboard.maps.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('maps.plural'), route('dashboard.maps.index'));
});

Breadcrumbs::for('dashboard.maps.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.maps.index');
    $breadcrumb->push(trans('maps.trashed'), route('dashboard.maps.trashed'));
});

Breadcrumbs::for('dashboard.maps.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.maps.index');
    $breadcrumb->push(trans('maps.actions.create'), route('dashboard.maps.create'));
});

Breadcrumbs::for('dashboard.maps.show', function ($breadcrumb, $map) {
    $breadcrumb->parent('dashboard.maps.index');
    $breadcrumb->push($map->name, route('dashboard.maps.show', $map));
});

Breadcrumbs::for('dashboard.maps.edit', function ($breadcrumb, $map) {
    $breadcrumb->parent('dashboard.maps.show', $map);
    $breadcrumb->push(trans('maps.actions.edit'), route('dashboard.maps.edit', $map));
});
