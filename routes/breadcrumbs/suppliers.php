<?php

Breadcrumbs::for('dashboard.suppliers.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('suppliers.plural'), route('dashboard.suppliers.index'));
});

Breadcrumbs::for('dashboard.suppliers.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.suppliers.index');
    $breadcrumb->push(trans('suppliers.trashed'), route('dashboard.suppliers.trashed'));
});

Breadcrumbs::for('dashboard.suppliers.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.suppliers.index');
    $breadcrumb->push(trans('suppliers.actions.create'), route('dashboard.suppliers.create'));
});

Breadcrumbs::for('dashboard.suppliers.show', function ($breadcrumb, $supplier) {
    $breadcrumb->parent('dashboard.suppliers.index');
    $breadcrumb->push($supplier->name, route('dashboard.suppliers.show', $supplier));
});

Breadcrumbs::for('dashboard.suppliers.edit', function ($breadcrumb, $supplier) {
    $breadcrumb->parent('dashboard.suppliers.show', $supplier);
    $breadcrumb->push(trans('suppliers.actions.edit'), route('dashboard.suppliers.edit', $supplier));
});
