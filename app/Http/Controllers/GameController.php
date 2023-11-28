<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    
    public function throwDice($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        if (auth()->user()->id != $id) {
            return response()->json(['error' => 'No tienes permiso para tirar dados.'], 403);
        }

        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);

        $won = ($dice1 + $dice2) === 7;

        $game = new Game();
        $game->user_id = $id;
        $game->dice1 = $dice1;
        $game->dice2 = $dice2;
        $game->won = $won;
        $game->save();

        return response()->json([
            'message' => 'Has tirado los dados!',
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $won ? "Has ganado!!" : "Has perdido, Inténtalo de nuevo!!",
        ], 200);
    }
    public function delete($id)
{
    
    $player = User::find($id);

    if ($player) {
        
        $player->games()->delete();

        return response()->json(['message' => 'Tiradas del jugador/a eliminadas correctamente']);
    } else {
        return response()->json(['error' => 'Jugador/a no encontrado'], 404);
    }
}

public function getPlayerGames($id)
{
    $player = User::find($id);
    if (auth()->user()->id != $id) {
        return response()->json(['error' => 'No tienes permiso para ver a ese usuario.'], 403);
    }

    if ($player) {
        $games = $player->games()->get();

        return response()->json([
            'player_id' => $player->id,
            'games' => $games,
        ]);
    } else {
        return response()->json(['error' => 'Jugador/a no encontrado'], 404);
    }
}

}
