<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/game', [GameController::class, 'index'])->name('game.index');
Route::post('/game/spin', [GameController::class, 'spin'])->name('game.spin');
