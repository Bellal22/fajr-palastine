<?php

Breadcrumbs::for('dashboard.coupon_types.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('coupon_types.plural'), route('dashboard.coupon_types.index'));
});

Breadcrumbs::for('dashboard.coupon_types.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.coupon_types.index');
    $breadcrumb->push(trans('coupon_types.trashed'), route('dashboard.coupon_types.trashed'));
});

Breadcrumbs::for('dashboard.coupon_types.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.coupon_types.index');
    $breadcrumb->push(trans('coupon_types.actions.create'), route('dashboard.coupon_types.create'));
});

Breadcrumbs::for('dashboard.coupon_types.show', function ($breadcrumb, $coupon_type) {
    $breadcrumb->parent('dashboard.coupon_types.index');
    $breadcrumb->push($coupon_type->name, route('dashboard.coupon_types.show', $coupon_type));
});

Breadcrumbs::for('dashboard.coupon_types.edit', function ($breadcrumb, $coupon_type) {
    $breadcrumb->parent('dashboard.coupon_types.show', $coupon_type);
    $breadcrumb->push(trans('coupon_types.actions.edit'), route('dashboard.coupon_types.edit', $coupon_type));
});
