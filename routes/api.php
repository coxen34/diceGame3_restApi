<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;



Route::post('/players', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {

        Route::delete('/players/{id}/games', [GameController::class, 'delete'])->name('games.deletePlayerGames');
        Route::get('/players', [UserController::class, 'index'])->name('users.index');
        Route::get('/players/ranking', [UserController::class, 'getPlayersRanking'])->name('players.ranking');
        Route::get('/players/ranking/loser', [UserController::class, 'getWorstPlayer'])->name('players.ranking/loser');
        Route::get('/players/ranking/winner', [UserController::class, 'getBestPlayer'])->name('players.ranking/winner');
    });

    Route::middleware('role:player')->group(function () {

        Route::put('/players/{id}', [UserController::class, 'update'])->name('users.update');
        Route::post('/players/{id}/games', [GameController::class, 'throwDice'])->name('games.throwDice');
        Route::get('/players/{id}/games', [GameController::class, 'getPlayerGames'])->name('games.getPlayerGames');
    });
});


