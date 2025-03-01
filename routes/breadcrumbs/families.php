<?php

Breadcrumbs::for('dashboard.families.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('families.plural'), route('dashboard.families.index'));
});

Breadcrumbs::for('dashboard.families.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.families.index');
    $breadcrumb->push(trans('families.trashed'), route('dashboard.families.trashed'));
});

Breadcrumbs::for('dashboard.families.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.families.index');
    $breadcrumb->push(trans('families.actions.create'), route('dashboard.families.create'));
});

Breadcrumbs::for('dashboard.families.show', function ($breadcrumb, $family) {
    $breadcrumb->parent('dashboard.families.index');
    $breadcrumb->push($family->name, route('dashboard.families.show', $family));
});

Breadcrumbs::for('dashboard.families.edit', function ($breadcrumb, $family) {
    $breadcrumb->parent('dashboard.families.show', $family);
    $breadcrumb->push(trans('families.actions.edit'), route('dashboard.families.edit', $family));
});
