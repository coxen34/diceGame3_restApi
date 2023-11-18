<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth:api');


Route::put('/players/{id}', [UserController::class, 'update'])->name('users.update')->middleware('auth:api');

//TIRAR DADOS
Route::post('/players/{id}/games', [GameController::class, 'throwDice'])->name('games.throwDice')->middleware('auth:api');

//elimina les tirades del jugador/a
Route::delete('/players/{id}/games', [GameController::class,'delete'])->name('games.deletePlayerGames')->middleware('auth:api');

// llistat de tots els jugadors/es del sistema amb el seu percentatge mitjà d’èxits
Route::get('/players',[UserController::class,'index'])->name('users.index')->middleware('auth:api');

//LISTADO JUGADAS X JUGADOR ID
Route::get('/players/{id}/games', [GameController::class, 'getPlayerGames'])->name('games.getPlayerGames')->middleware('auth:api');

//rànquing mitjà de tots els jugadors/es del sistema.
Route::get('/players/ranking', [UserController::class, 'getPlayersRanking'])->name('players.ranking')->middleware('auth:api');

Route::get('/players/ranking/loser', [UserController::class, 'getWorstPlayer'])->name('players.ranking/loser')->middleware('auth:api');

Route::get('/players/ranking/winner', [UserController::class, 'getBestPlayer'])->name('players.ranking/winner')->middleware('auth:api');



// Route::middleware('auth:api')->group(function () {
    
// });




