<?php

Route::middleware('auth:sanctum')->post('/editor/upload', 'MediaController@editorUpload')->name('editor.upload');