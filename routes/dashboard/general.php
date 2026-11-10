<?php

Route::get('locale/{locale}', 'LocaleController@update')->name('locale')->where('locale', '(ar|en)');

Route::get('/', 'DashboardController@index')->name('home');
Route::get('statistics', 'DashboardController@statistics')->name('statistics');
Route::get('statistics/family-breakdown', 'DashboardController@familyBreakdown')->name('statistics.family_breakdown');
Route::get('statistics/family-breakdown/export', 'DashboardController@exportFamilyBreakdown')->name('statistics.family_breakdown.export');