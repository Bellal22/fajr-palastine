<?php

Breadcrumbs::for('dashboard.area_responsibles.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('area_responsibles.plural'), route('dashboard.area_responsibles.index'));
});

Breadcrumbs::for('dashboard.area_responsibles.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.area_responsibles.index');
    $breadcrumb->push(trans('area_responsibles.trashed'), route('dashboard.area_responsibles.trashed'));
});

Breadcrumbs::for('dashboard.area_responsibles.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.area_responsibles.index');
    $breadcrumb->push(trans('area_responsibles.actions.create'), route('dashboard.area_responsibles.create'));
});

Breadcrumbs::for('dashboard.area_responsibles.show', function ($breadcrumb, $area_responsible) {
    $breadcrumb->parent('dashboard.area_responsibles.index');
    $breadcrumb->push($area_responsible->name, route('dashboard.area_responsibles.show', $area_responsible));
});

Breadcrumbs::for('dashboard.area_responsibles.edit', function ($breadcrumb, $area_responsible) {
    $breadcrumb->parent('dashboard.area_responsibles.show', $area_responsible);
    $breadcrumb->push(trans('area_responsibles.actions.edit'), route('dashboard.area_responsibles.edit', $area_responsible));
});
