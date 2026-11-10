<?php

Breadcrumbs::for('dashboard.kitchen_complaints.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('kitchen_complaints.plural'), route('dashboard.kitchen_complaints.index'));
});

Breadcrumbs::for('dashboard.kitchen_complaints.show', function ($breadcrumb, $kitchenComplaint) {
    $breadcrumb->parent('dashboard.kitchen_complaints.index');
    $breadcrumb->push($kitchenComplaint->complaint_title, route('dashboard.kitchen_complaints.show', $kitchenComplaint));
});
