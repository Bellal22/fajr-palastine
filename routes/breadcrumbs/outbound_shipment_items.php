<?php

Breadcrumbs::for('dashboard.outbound_shipment_items.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('outbound_shipment_items.plural'), route('dashboard.outbound_shipment_items.index'));
});

Breadcrumbs::for('dashboard.outbound_shipment_items.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.outbound_shipment_items.index');
    $breadcrumb->push(trans('outbound_shipment_items.trashed'), route('dashboard.outbound_shipment_items.trashed'));
});

Breadcrumbs::for('dashboard.outbound_shipment_items.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.outbound_shipment_items.index');
    $breadcrumb->push(trans('outbound_shipment_items.actions.create'), route('dashboard.outbound_shipment_items.create'));
});

Breadcrumbs::for('dashboard.outbound_shipment_items.show', function ($breadcrumb, $outbound_shipment_item) {
    $breadcrumb->parent('dashboard.outbound_shipment_items.index');
    $breadcrumb->push($outbound_shipment_item->name, route('dashboard.outbound_shipment_items.show', $outbound_shipment_item));
});

Breadcrumbs::for('dashboard.outbound_shipment_items.edit', function ($breadcrumb, $outbound_shipment_item) {
    $breadcrumb->parent('dashboard.outbound_shipment_items.show', $outbound_shipment_item);
    $breadcrumb->push(trans('outbound_shipment_items.actions.edit'), route('dashboard.outbound_shipment_items.edit', $outbound_shipment_item));
});
