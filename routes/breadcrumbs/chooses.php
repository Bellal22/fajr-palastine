<?php

Breadcrumbs::for('dashboard.chooses.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('chooses.plural'), route('dashboard.chooses.index'));
});

Breadcrumbs::for('dashboard.chooses.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.chooses.index');
    $breadcrumb->push(trans('chooses.trashed'), route('dashboard.chooses.trashed'));
});

Breadcrumbs::for('dashboard.chooses.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.chooses.index');
    $breadcrumb->push(trans('chooses.actions.create'), route('dashboard.chooses.create'));
});

Breadcrumbs::for('dashboard.chooses.show', function ($breadcrumb, $choose) {
    $breadcrumb->parent('dashboard.chooses.index');
    $breadcrumb->push($choose->name, route('dashboard.chooses.show', $choose));
});

Breadcrumbs::for('dashboard.chooses.edit', function ($breadcrumb, $choose) {
    $breadcrumb->parent('dashboard.chooses.show', $choose);
    $breadcrumb->push(trans('chooses.actions.edit'), route('dashboard.chooses.edit', $choose));
});
