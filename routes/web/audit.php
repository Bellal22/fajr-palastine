<?php

use App\Http\Controllers\AuditController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/audit', [AuditController::class, 'index'])->name('admin.audit.index');
    Route::get('/admin/audit/outliers', [AuditController::class, 'outliers'])->name('admin.audit.outliers');
    Route::get('/admin/audit/history/{id_num}', [AuditController::class, 'history'])->name('admin.audit.history');
});
