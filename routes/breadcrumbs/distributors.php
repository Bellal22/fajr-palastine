<?php

Breadcrumbs::for('dashboard.distributors.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('distributors.plural'), route('dashboard.distributors.index'));
});

Breadcrumbs::for('dashboard.distributors.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.distributors.index');
    $breadcrumb->push(trans('distributors.trashed'), route('dashboard.distributors.trashed'));
});

Breadcrumbs::for('dashboard.distributors.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.distributors.index');
    $breadcrumb->push(trans('distributors.actions.create'), route('dashboard.distributors.create'));
});

Breadcrumbs::for('dashboard.distributors.show', function ($breadcrumb, $distributor) {
    $breadcrumb->parent('dashboard.distributors.index');
    $breadcrumb->push($distributor->name, route('dashboard.distributors.show', $distributor));
});

Breadcrumbs::for('dashboard.distributors.edit', function ($breadcrumb, $distributor) {
    $breadcrumb->parent('dashboard.distributors.show', $distributor);
    $breadcrumb->push(trans('distributors.actions.edit'), route('dashboard.distributors.edit', $distributor));
});
