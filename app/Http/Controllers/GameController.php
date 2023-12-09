<?php

namespace App\Http\Controllers;

use App\Http\Functions\GameFunctions\ThrowDiceFunctions;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Functions\GameFunctions\ThrowDiceFunctionsFunctions;

class GameController extends Controller
{
    protected $throwDiceFunctions;

    public function __construct(ThrowDiceFunctions $throwDiceFunctions)
    {
        $this->throwDiceFunctions = $throwDiceFunctions;
        
    }

    public function throwDice($id)
{
    $user = Auth::user();

    $validationResult = $this->throwDiceFunctions->validateThrowDice($user, $id);

    if ($validationResult['error']) {
        return response()->json($validationResult, $validationResult['statusCode']);
    }

    $gameResult = $this->throwDiceFunctions->playGame($id);

    return response()->json($gameResult, 200);
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
