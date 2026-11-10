<?php

// Kitchen Monitors Routes.
Route::get('trashed/kitchen_monitors', 'KitchenMonitorController@trashed')
    ->name('kitchen_monitors.trashed');

Route::get('trashed/kitchen_monitors/{trashed_kitchen_monitor}', 'KitchenMonitorController@showTrashed')
    ->name('kitchen_monitors.trashed.show');

Route::post('kitchen_monitors/{trashed_kitchen_monitor}/restore', 'KitchenMonitorController@restore')
    ->name('kitchen_monitors.restore');

Route::delete('kitchen_monitors/{trashed_kitchen_monitor}/forceDelete', 'KitchenMonitorController@forceDelete')
    ->name('kitchen_monitors.forceDelete');

Route::resource('kitchen_monitors', 'KitchenMonitorController');
