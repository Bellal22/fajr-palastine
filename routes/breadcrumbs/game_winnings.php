<?php

Breadcrumbs::for('dashboard.game_winnings.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('game_winnings.plural'), route('dashboard.game_winnings.index'));
});

Breadcrumbs::for('dashboard.game_winnings.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.game_winnings.index');
    $breadcrumb->push(trans('game_winnings.trashed'), route('dashboard.game_winnings.trashed'));
});

Breadcrumbs::for('dashboard.game_winnings.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.game_winnings.index');
    $breadcrumb->push(trans('game_winnings.actions.create'), route('dashboard.game_winnings.create'));
});

Breadcrumbs::for('dashboard.game_winnings.show', function ($breadcrumb, $game_winning) {
    $breadcrumb->parent('dashboard.game_winnings.index');
    $breadcrumb->push($game_winning->name, route('dashboard.game_winnings.show', $game_winning));
});

Breadcrumbs::for('dashboard.game_winnings.edit', function ($breadcrumb, $game_winning) {
    $breadcrumb->parent('dashboard.game_winnings.show', $game_winning);
    $breadcrumb->push(trans('game_winnings.actions.edit'), route('dashboard.game_winnings.edit', $game_winning));
});
Breadcrumbs::for('dashboard.game_winnings.verify', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.game_winnings.index');
    $breadcrumb->push(trans('game_winnings.actions.verify'), route('dashboard.game_winnings.verify'));
});
