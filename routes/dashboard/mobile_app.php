<?php

use App\Http\Controllers\Dashboard\MobileAppController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'mobile-app',
    'as' => 'mobile_app.',
    'middleware' => function ($request, $next) {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
        return $next($request);
    }
], function () {
    Route::get('/', [MobileAppController::class, 'index'])->name('index');
    Route::get('/users', [MobileAppController::class, 'users'])->name('users');
    Route::get('/users/create', [MobileAppController::class, 'createUser'])->name('users.create');
    Route::post('/users/store', [MobileAppController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/toggle', [MobileAppController::class, 'toggleUserAccess'])->name('toggle_user');
    Route::get('/projects', [MobileAppController::class, 'projects'])->name('projects');
    Route::get('/nominate', [MobileAppController::class, 'nominate'])->name('nominate');
    Route::post('/nominate/store', [MobileAppController::class, 'storeNominations'])->name('nominate.store');
    Route::post('/nominate/unassign/{id}', [MobileAppController::class, 'unassign'])->name('nominate.unassign');
    Route::get('/nominate/export', [MobileAppController::class, 'exportNominations'])->name('nominate.export');
    Route::get('/report', [MobileAppController::class, 'projectReport'])->name('report');
    Route::post('/report/delete-batch', [MobileAppController::class, 'deleteBatch'])->name('report.delete_batch');
    Route::post('/report/update-batch', [MobileAppController::class, 'updateBatch'])->name('report.update_batch');
});
