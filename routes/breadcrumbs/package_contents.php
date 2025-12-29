<?php

Breadcrumbs::for('dashboard.package_contents.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('package_contents.plural'), route('dashboard.package_contents.index'));
});

Breadcrumbs::for('dashboard.package_contents.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.package_contents.index');
    $breadcrumb->push(trans('package_contents.trashed'), route('dashboard.package_contents.trashed'));
});

Breadcrumbs::for('dashboard.package_contents.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.package_contents.index');
    $breadcrumb->push(trans('package_contents.actions.create'), route('dashboard.package_contents.create'));
});

Breadcrumbs::for('dashboard.package_contents.show', function ($breadcrumb, $package_content) {
    $breadcrumb->parent('dashboard.package_contents.index');
    $breadcrumb->push($package_content->name, route('dashboard.package_contents.show', $package_content));
});

Breadcrumbs::for('dashboard.package_contents.edit', function ($breadcrumb, $package_content) {
    $breadcrumb->parent('dashboard.package_contents.show', $package_content);
    $breadcrumb->push(trans('package_contents.actions.edit'), route('dashboard.package_contents.edit', $package_content));
});
