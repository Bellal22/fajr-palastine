<?php

Breadcrumbs::for('dashboard.items.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('items.plural'), route('dashboard.items.index'));
});

Breadcrumbs::for('dashboard.items.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.items.index');
    $breadcrumb->push(trans('items.trashed'), route('dashboard.items.trashed'));
});

Breadcrumbs::for('dashboard.items.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.items.index');
    $breadcrumb->push(trans('items.actions.create'), route('dashboard.items.create'));
});

Breadcrumbs::for('dashboard.items.show', function ($breadcrumb, $item) {
    $breadcrumb->parent('dashboard.items.index');
    $breadcrumb->push($item->name, route('dashboard.items.show', $item));
});

Breadcrumbs::for('dashboard.items.edit', function ($breadcrumb, $item) {
    $breadcrumb->parent('dashboard.items.show', $item);
    $breadcrumb->push(trans('items.actions.edit'), route('dashboard.items.edit', $item));
});
