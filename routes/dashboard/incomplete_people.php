<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'incomplete_people', 'as' => 'incomplete_people.'], function() {
    Route::get('/', 'IncompletePersonController@index')->name('index');
    Route::get('download-template', 'IncompletePersonController@downloadTemplate')->name('download-template');
    Route::post('import', 'IncompletePersonController@import')->name('import');
    Route::get('export-skipped', 'IncompletePersonController@exportSkipped')->name('export-skipped');
    Route::get('exec-move', 'IncompletePersonController@bulkAction')->name('bulk-action');
    Route::post('move-from-persons', 'IncompletePersonController@moveFromPersons')->name('move-from-persons');
    Route::delete('{id}', 'IncompletePersonController@destroy')->name('destroy');
    Route::get('{id}', 'IncompletePersonController@show')->name('show');
    Route::get('{id}/edit', 'IncompletePersonController@edit')->name('edit');
    Route::put('{id}', 'IncompletePersonController@update')->name('update');
});
