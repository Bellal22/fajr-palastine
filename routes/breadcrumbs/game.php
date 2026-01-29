<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('game.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.home');
    $trail->push('لعبة عجلة الحظ', route('game.index'));
});
