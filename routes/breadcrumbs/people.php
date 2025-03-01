<?php

Breadcrumbs::for('dashboard.people.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('people.plural'), route('dashboard.people.index'));
});

Breadcrumbs::for('dashboard.people.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.people.index');
    $breadcrumb->push(trans('people.trashed'), route('dashboard.people.trashed'));
});

Breadcrumbs::for('dashboard.people.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.people.index');
    $breadcrumb->push(trans('people.actions.create'), route('dashboard.people.create'));
});

Breadcrumbs::for('dashboard.people.show', function ($breadcrumb, $person) {
    $breadcrumb->parent('dashboard.people.index');
    $breadcrumb->push($person->first_name, route('dashboard.people.show', $person));
});

Breadcrumbs::for('dashboard.people.edit', function ($breadcrumb, $person) {
    $breadcrumb->parent('dashboard.people.show', $person);
    $breadcrumb->push(trans('people.actions.edit'), route('dashboard.people.edit', $person));
});
