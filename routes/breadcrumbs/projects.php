<?php

Breadcrumbs::for('dashboard.projects.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('projects.plural'), route('dashboard.projects.index'));
});

Breadcrumbs::for('dashboard.projects.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push(trans('projects.trashed'), route('dashboard.projects.trashed'));
});

Breadcrumbs::for('dashboard.projects.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push(trans('projects.actions.create'), route('dashboard.projects.create'));
});

Breadcrumbs::for('dashboard.projects.show', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push($project->name, route('dashboard.projects.show', $project));
});

Breadcrumbs::for('dashboard.projects.edit', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.show', $project);
    $breadcrumb->push(trans('projects.actions.edit'), route('dashboard.projects.edit', $project));
});
