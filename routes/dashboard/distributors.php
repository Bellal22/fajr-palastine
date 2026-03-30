<?php

// Distributors Routes.
Route::get('trashed/distributors', 'DistributorController@trashed')
    ->name('distributors.trashed');

Route::get('trashed/distributors/{trashed_distributor}', 'DistributorController@showTrashed')
    ->name('distributors.trashed.show');

Route::post('distributors/{trashed_distributor}/restore', 'DistributorController@restore')
    ->name('distributors.restore');

Route::delete('distributors/{trashed_distributor}/forceDelete', 'DistributorController@forceDelete')
    ->name('distributors.forceDelete');

Route::resource('distributors', 'DistributorController');
