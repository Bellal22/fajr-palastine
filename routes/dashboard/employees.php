<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'employees', 'as' => 'employees.'], function() {
    Route::get('/', 'EmployeeController@index')->name('index');
    Route::get('create', 'EmployeeController@create')->name('create');
    Route::post('/', 'EmployeeController@store')->name('store');
    Route::post('import', 'EmployeeController@import')->name('import');
    Route::get('download-template', 'EmployeeController@downloadTemplate')->name('download-template');
    Route::get('{id}', 'EmployeeController@show')->name('show');
    Route::get('{id}/edit', 'EmployeeController@edit')->name('edit');
    Route::put('{id}', 'EmployeeController@update')->name('update');
    Route::delete('{id}', 'EmployeeController@destroy')->name('destroy');
});
