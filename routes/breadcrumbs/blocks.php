<?php

Breadcrumbs::for('dashboard.blocks.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('blocks.plural'), route('dashboard.blocks.index'));
});

Breadcrumbs::for('dashboard.blocks.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.blocks.index');
    $breadcrumb->push(trans('blocks.trashed'), route('dashboard.blocks.trashed'));
});

Breadcrumbs::for('dashboard.blocks.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.blocks.index');
    $breadcrumb->push(trans('blocks.actions.create'), route('dashboard.blocks.create'));
});

Breadcrumbs::for('dashboard.blocks.show', function ($breadcrumb, $block) {
    $breadcrumb->parent('dashboard.blocks.index');
    $breadcrumb->push($block->name, route('dashboard.blocks.show', $block));
});

Breadcrumbs::for('dashboard.blocks.edit', function ($breadcrumb, $block) {
    $breadcrumb->parent('dashboard.blocks.show', $block);
    $breadcrumb->push(trans('blocks.actions.edit'), route('dashboard.blocks.edit', $block));
});
