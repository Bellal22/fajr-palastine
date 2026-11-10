<?php

// Settings Routes.
Route::get('settings', 'SettingController@index')->name('settings.index');
Route::patch('settings', 'SettingController@update')->name('settings.update');
Route::get('backup/download', 'SettingController@downloadBackup')->name('backup.download');
Route::post('settings/test-connection', 'SettingController@testConnection')->name('settings.test_connection');
Route::post('settings/bulk-sync', 'SettingController@bulkSync')->name('settings.bulk_sync');
