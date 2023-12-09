<?php

namespace App\Http\Functions\GameFunctions;

use App\Models\User;
use App\Models\Game;

class ThrowDiceFunctions {

    public function validateThrowDice($user, $id)
    {
        if (!$user) {
            return ['error' => 'Unauthorized', 'statusCode' => 401];
        }

        $targetUser = User::find($id);

        if (!$targetUser) {
            return ['error' => 'Usuario no encontrado', 'statusCode' => 404];
        }

        if (auth()->user()->id != $id) {
            return ['error' => 'No tienes permiso para tirar dados.', 'statusCode' => 403];
        }

        return ['error' => null, 'statusCode' => null];
    }

    public function playGame($id)
    {
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);

        $won = ($dice1 + $dice2) === 7;

        $game = new Game();
        $game->user_id = $id;
        $game->dice1 = $dice1;
        $game->dice2 = $dice2;
        $game->won = $won;
        $game->save();

        return [
            'message' => 'Has tirado los dados!',
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $won ? "Has ganado!!" : "Has perdido, Inténtalo de nuevo!!",
        ];
    }
}

