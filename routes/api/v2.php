<?php

use App\Http\Controllers\Api\V2\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::post('login', [ApiController::class, 'Login']);

    Route::middleware('mobile.api')->group(function () {
        Route::post('logout', [ApiController::class, 'logout']);
        Route::get('index', [ApiController::class, 'index']);
        Route::post('get_new_persone_aid', [ApiController::class, 'get_new_persone_aid']);
        Route::post('get_new_persone', [ApiController::class, 'get_new_persone']);
        Route::post('get_update_persone', [ApiController::class, 'get_update_persone']);
        Route::post('get_persone', [ApiController::class, 'get_persone']);
        Route::post('get_new_aid', [ApiController::class, 'get_new_aid']);
        Route::get('constant_var', [ApiController::class, 'constant_var']);
        Route::get('get_blocks', [ApiController::class, 'get_blocks']);
        Route::post('create', [ApiController::class, 'create']);
        Route::post('update/{id}', [ApiController::class, 'update']);
        Route::post('get_aid_manage', [ApiController::class, 'get_aid_manage']);
        Route::match(['get', 'post'], 'get_aid_manage_by_id/{id}', [ApiController::class, 'get_aid_manage_by_id']);
        Route::post('set_aid_manage_receive', [ApiController::class, 'set_aid_manage_receive']);
    });
});
