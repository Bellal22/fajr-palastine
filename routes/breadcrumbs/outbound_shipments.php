<?php

Breadcrumbs::for('dashboard.outbound_shipments.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('outbound_shipments.plural'), route('dashboard.outbound_shipments.index'));
});

Breadcrumbs::for('dashboard.outbound_shipments.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.outbound_shipments.index');
    $breadcrumb->push(trans('outbound_shipments.trashed'), route('dashboard.outbound_shipments.trashed'));
});

Breadcrumbs::for('dashboard.outbound_shipments.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.outbound_shipments.index');
    $breadcrumb->push(trans('outbound_shipments.actions.create'), route('dashboard.outbound_shipments.create'));
});

Breadcrumbs::for('dashboard.outbound_shipments.show', function ($breadcrumb, $outbound_shipment) {
    $breadcrumb->parent('dashboard.outbound_shipments.index');
    $breadcrumb->push($outbound_shipment->shipment_number ?? '#' . $outbound_shipment->id, route('dashboard.outbound_shipments.show', $outbound_shipment));
});

Breadcrumbs::for('dashboard.outbound_shipments.edit', function ($breadcrumb, $outbound_shipment) {
    $breadcrumb->parent('dashboard.outbound_shipments.show', $outbound_shipment);
    $breadcrumb->push(trans('outbound_shipments.actions.edit'), route('dashboard.outbound_shipments.edit', $outbound_shipment));
});

Breadcrumbs::for('dashboard.outbound_shipments.trashed.show', function ($breadcrumb, $outbound_shipment) {
    $breadcrumb->parent('dashboard.outbound_shipments.trashed');
    $breadcrumb->push($outbound_shipment->shipment_number ?? '#' . $outbound_shipment->id, route('dashboard.outbound_shipments.trashed.show', $outbound_shipment));
});
