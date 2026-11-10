<?php


Breadcrumbs::for('dashboard.projects.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push(trans('projects.plural'), route('dashboard.projects.index'));
});


Breadcrumbs::for('dashboard.projects.trashed', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push(trans('projects.trashed'), route('dashboard.projects.trashed'));
});


Breadcrumbs::for('dashboard.projects.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push(trans('projects.actions.create'), route('dashboard.projects.create'));
});


Breadcrumbs::for('dashboard.projects.show', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push($project->name, route('dashboard.projects.show', $project));
});


Breadcrumbs::for('dashboard.projects.edit', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.show', $project);
    $breadcrumb->push(trans('projects.actions.edit'), route('dashboard.projects.edit', $project));
});


// Breadcrumbs للمستفيدين
Breadcrumbs::for('dashboard.projects.beneficiaries', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.show', $project);
    $breadcrumb->push('المستفيدين', route('dashboard.projects.beneficiaries', $project));
});


Breadcrumbs::for('dashboard.projects.beneficiaries.import', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.beneficiaries', $project);
    $breadcrumb->push('استيراد المستفيدين', route('dashboard.projects.beneficiaries.import', $project));
});


Breadcrumbs::for('dashboard.projects.beneficiaries.filter-areas', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.projects.beneficiaries', $project);
    $breadcrumb->push('ترشيح حسب المناطق', route('dashboard.projects.beneficiaries.filter-areas', $project));
});
Breadcrumbs::for('dashboard.projects.deliveries', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push('تسليم الطرود والكوبونات', route('dashboard.projects.deliveries'));
});

Breadcrumbs::for('dashboard.reports.projects', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.projects.index');
    $breadcrumb->push('تقارير الكوبونات', route('dashboard.reports.projects'));
});

Breadcrumbs::for('dashboard.reports.projects.show', function ($breadcrumb, $project) {
    $breadcrumb->parent('dashboard.reports.projects');
    $breadcrumb->push($project->name, route('dashboard.reports.projects.show', $project));
});

Breadcrumbs::for('dashboard.reports.projects.period', function ($breadcrumb, $period) {
    $breadcrumb->parent('dashboard.reports.projects');
    $labels = [
        'daily' => 'اليومية',
        'weekly' => 'الأسبوعية',
        'monthly' => 'الشهرية',
        'yearly' => 'السنوية'
    ];
    $breadcrumb->push('التقارير ' . ($labels[$period] ?? ''), route('dashboard.reports.projects.period', $period));
});

Breadcrumbs::for('dashboard.custom_projects.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.home');
    $breadcrumb->push('المشاريع المخصصة', route('dashboard.custom_projects.index'));
});

Breadcrumbs::for('dashboard.custom_projects.create', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.custom_projects.index');
    $breadcrumb->push('إضافة مشروع مخصص جديد', route('dashboard.custom_projects.create'));
});

Breadcrumbs::for('dashboard.custom_projects.edit', function ($breadcrumb, $customProject) {
    $breadcrumb->parent('dashboard.custom_projects.index');
    $breadcrumb->push('تعديل وتخصيص: ' . ($customProject->name ?? ''), route('dashboard.custom_projects.edit', $customProject));
});

Breadcrumbs::for('dashboard.rrm.index', function ($breadcrumb) {
    $breadcrumb->parent('dashboard.custom_projects.index');
    $breadcrumb->push('كشوفات وتوزيعات المشاريع المخصصة', route('dashboard.rrm.index'));
});

Breadcrumbs::for('dashboard.rrm.show', function ($breadcrumb, $batch) {
    $breadcrumb->parent('dashboard.rrm.index');
    $breadcrumb->push($batch->name ?? 'كشف المستفيدين', route('dashboard.rrm.show', is_object($batch) ? $batch->id : $batch));
});

