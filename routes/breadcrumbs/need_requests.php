<?php

Breadcrumbs::for('dashboard.need_requests.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('need_requests.plural'), route('dashboard.need_requests.index'));
});

Breadcrumbs::for('dashboard.need_requests.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.need_requests.index');
    $breadcrumb->push(trans('need_requests.trashed'), route('dashboard.need_requests.trashed'));
});

Breadcrumbs::for('dashboard.need_requests.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.need_requests.index');
    $breadcrumb->push(trans('need_requests.actions.create'), route('dashboard.need_requests.create'));
});

Breadcrumbs::for('dashboard.need_requests.show', function ($breadcrumb, $need_request) {
    $breadcrumb->parent('dashboard.need_requests.index');
    $breadcrumb->push($need_request->project->name ?? trans('need_requests.singular'), route('dashboard.need_requests.show', $need_request));
});

Breadcrumbs::for('dashboard.need_requests.edit', function ($breadcrumb, $need_request) {
    $breadcrumb->parent('dashboard.need_requests.show', $need_request);
    $breadcrumb->push(trans('need_requests.actions.edit'), route('dashboard.need_requests.edit', $need_request));
});
