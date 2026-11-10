<?php

// Researchers Routes.
Route::get('trashed/researchers', 'ResearcherController@trashed')
    ->name('researchers.trashed');

Route::get('trashed/researchers/{trashed_researcher}', 'ResearcherController@showTrashed')
    ->name('researchers.trashed.show');

Route::post('researchers/{trashed_researcher}/restore', 'ResearcherController@restore')
    ->name('researchers.restore');

Route::delete('researchers/{trashed_researcher}/forceDelete', 'ResearcherController@forceDelete')
    ->name('researchers.forceDelete');

Route::resource('researchers', 'ResearcherController');
