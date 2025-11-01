<?php

Breadcrumbs::for('dashboard.inbound_shipments.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('inbound_shipments.plural'), route('dashboard.inbound_shipments.index'));
});

Breadcrumbs::for('dashboard.inbound_shipments.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.inbound_shipments.index');
    $breadcrumb->push(trans('inbound_shipments.trashed'), route('dashboard.inbound_shipments.trashed'));
});

Breadcrumbs::for('dashboard.inbound_shipments.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.inbound_shipments.index');
    $breadcrumb->push(trans('inbound_shipments.actions.create'), route('dashboard.inbound_shipments.create'));
});

Breadcrumbs::for('dashboard.inbound_shipments.show', function ($breadcrumb, $inbound_shipment) {
    $breadcrumb->parent('dashboard.inbound_shipments.index');
    $breadcrumb->push($inbound_shipment->name, route('dashboard.inbound_shipments.show', $inbound_shipment));
});

Breadcrumbs::for('dashboard.inbound_shipments.edit', function ($breadcrumb, $inbound_shipment) {
    $breadcrumb->parent('dashboard.inbound_shipments.show', $inbound_shipment);
    $breadcrumb->push(trans('inbound_shipments.actions.edit'), route('dashboard.inbound_shipments.edit', $inbound_shipment));
});
