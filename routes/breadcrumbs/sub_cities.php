<?php

Breadcrumbs::for('dashboard.sub_cities.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('sub_cities.plural'), route('dashboard.sub_cities.index'));
});

Breadcrumbs::for('dashboard.sub_cities.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.sub_cities.index');
    $breadcrumb->push(trans('sub_cities.trashed'), route('dashboard.sub_cities.trashed'));
});

Breadcrumbs::for('dashboard.sub_cities.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.sub_cities.index');
    $breadcrumb->push(trans('sub_cities.actions.create'), route('dashboard.sub_cities.create'));
});

Breadcrumbs::for('dashboard.sub_cities.show', function ($breadcrumb, $sub_city) {
    $breadcrumb->parent('dashboard.sub_cities.index');
    $breadcrumb->push($sub_city->name, route('dashboard.sub_cities.show', $sub_city));
});

Breadcrumbs::for('dashboard.sub_cities.edit', function ($breadcrumb, $sub_city) {
    $breadcrumb->parent('dashboard.sub_cities.show', $sub_city);
    $breadcrumb->push(trans('sub_cities.actions.edit'), route('dashboard.sub_cities.edit', $sub_city));
});
