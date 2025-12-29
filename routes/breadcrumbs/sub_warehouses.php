<?php

Breadcrumbs::for('dashboard.sub_warehouses.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('sub_warehouses.plural'), route('dashboard.sub_warehouses.index'));
});

Breadcrumbs::for('dashboard.sub_warehouses.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.sub_warehouses.index');
    $breadcrumb->push(trans('sub_warehouses.trashed'), route('dashboard.sub_warehouses.trashed'));
});

Breadcrumbs::for('dashboard.sub_warehouses.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.sub_warehouses.index');
    $breadcrumb->push(trans('sub_warehouses.actions.create'), route('dashboard.sub_warehouses.create'));
});

Breadcrumbs::for('dashboard.sub_warehouses.show', function ($breadcrumb, $sub_warehouse) {
    $breadcrumb->parent('dashboard.sub_warehouses.index');
    $breadcrumb->push($sub_warehouse->name, route('dashboard.sub_warehouses.show', $sub_warehouse));
});

Breadcrumbs::for('dashboard.sub_warehouses.edit', function ($breadcrumb, $sub_warehouse) {
    $breadcrumb->parent('dashboard.sub_warehouses.show', $sub_warehouse);
    $breadcrumb->push(trans('sub_warehouses.actions.edit'), route('dashboard.sub_warehouses.edit', $sub_warehouse));
});
