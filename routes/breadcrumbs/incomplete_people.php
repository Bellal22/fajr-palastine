<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home > Incomplete Data
Breadcrumbs::for('dashboard.incomplete_people.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.home');
    $trail->push('البيانات غير المكتملة', route('dashboard.incomplete_people.index'));
});

Breadcrumbs::for('dashboard.incomplete_people.show', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.incomplete_people.index');
    $trail->push('عرض التفاصيل', '#');
});
