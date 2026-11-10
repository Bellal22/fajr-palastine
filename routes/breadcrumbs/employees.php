<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home > Employees
Breadcrumbs::for('dashboard.employees.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.home');
    $trail->push('الموظفون', route('dashboard.employees.index'));
});

Breadcrumbs::for('dashboard.employees.create', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.employees.index');
    $trail->push('إضافة موظف', route('dashboard.employees.create'));
});

Breadcrumbs::for('dashboard.employees.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.employees.index');
    $trail->push('تعديل موظف', '#');
});

Breadcrumbs::for('dashboard.employees.show', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.employees.index');
    $trail->push('عرض التفاصيل', '#');
});
