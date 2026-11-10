<?php

Breadcrumbs::for('dashboard.kitchen_cards.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('kitchen_cards.plural'), route('dashboard.kitchen_cards.index'));
});

Breadcrumbs::for('dashboard.kitchen_cards.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.kitchen_cards.index');
    $breadcrumb->push(trans('kitchen_cards.actions.create'), route('dashboard.kitchen_cards.create'));
});

Breadcrumbs::for('dashboard.kitchen_cards.show', function ($breadcrumb, $kitchenCard) {
    $breadcrumb->parent('dashboard.kitchen_cards.index');
    $breadcrumb->push($kitchenCard->camp_name . ' - ' . $kitchenCard->card_number, route('dashboard.kitchen_cards.show', $kitchenCard));
});

Breadcrumbs::for('dashboard.kitchen_cards.edit', function ($breadcrumb, $kitchenCard) {
    $breadcrumb->parent('dashboard.kitchen_cards.show', $kitchenCard);
    $breadcrumb->push(trans('kitchen_cards.actions.edit'), route('dashboard.kitchen_cards.edit', $kitchenCard));
});
