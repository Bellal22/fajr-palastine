<?php

Breadcrumbs::for('dashboard.kitchen_monitors.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('متابعي المطبخ', route('dashboard.kitchen_monitors.index'));
});

Breadcrumbs::for('dashboard.kitchen_monitors.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.kitchen_monitors.index');
    $breadcrumb->push('متابعي المطبخ المحذوفين', route('dashboard.kitchen_monitors.trashed'));
});

Breadcrumbs::for('dashboard.kitchen_monitors.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.kitchen_monitors.index');
    $breadcrumb->push('إضافة متابع مطبخ', route('dashboard.kitchen_monitors.create'));
});

Breadcrumbs::for('dashboard.kitchen_monitors.show', function ($breadcrumb, $kitchenMonitor) {
    if (is_string($kitchenMonitor) || is_numeric($kitchenMonitor)) {
        $kitchenMonitor = \App\Models\User::find($kitchenMonitor) ?: $kitchenMonitor;
    }
    $breadcrumb->parent('dashboard.kitchen_monitors.index');
    $name = is_object($kitchenMonitor) ? $kitchenMonitor->name : $kitchenMonitor;
    $breadcrumb->push($name, route('dashboard.kitchen_monitors.show', $kitchenMonitor));
});

Breadcrumbs::for('dashboard.kitchen_monitors.edit', function ($breadcrumb, $kitchenMonitor) {
    if (is_string($kitchenMonitor) || is_numeric($kitchenMonitor)) {
        $kitchenMonitor = \App\Models\User::find($kitchenMonitor) ?: $kitchenMonitor;
    }
    $breadcrumb->parent('dashboard.kitchen_monitors.show', $kitchenMonitor);
    $breadcrumb->push('تعديل البيانات', route('dashboard.kitchen_monitors.edit', $kitchenMonitor));
});
