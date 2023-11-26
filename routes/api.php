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

    Route::put('/players/{id}', [UserController::class, 'update'])->name('users.update');

    //Tirada Daus
    Route::post('/players/{id}/games', [GameController::class, 'throwDice'])->name('games.throwDice');

    //Elimina les tirades del jugador/a
    Route::delete('/players/{id}/games', [GameController::class, 'delete'])->name('games.deletePlayerGames');

    // Llistat de tots els jugadors/es del sistema amb el seu percentatge mitjà d’èxits
    Route::get('/players', [UserController::class, 'index'])->name('users.index');

    //Llistat jugades X jugador ID
    Route::get('/players/{id}/games', [GameController::class, 'getPlayerGames'])->name('games.getPlayerGames');

    //Rànquing mitjà de tots els jugadors/es del sistema.
    Route::get('/players/ranking', [UserController::class, 'getPlayersRanking'])->name('players.ranking');

    Route::get('/players/ranking/loser', [UserController::class, 'getWorstPlayer'])->name('players.ranking/loser');

    Route::get('/players/ranking/winner', [UserController::class, 'getBestPlayer'])->name('players.ranking/winner');
});
