<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;



class GameTest extends TestCase
{
    // use RefreshDatabase;
    use DatabaseTransactions;
    
//------------------FUNCIONES HELPER------------
private function getUser($name)
{
   $user = User::where('name', $name)->first();
   if (!$user) {
       $user = User::factory()->create(['name' => $name]);
   }
   return $user;
}
private function assertResponseThrowDice($response)
{
   if ($response->status() === 302) {
       $response->assertRedirect('/api/login'); 
   } else {
       $response->assertStatus(200)
           ->assertJson([
               'message' => 'Has tirado los dados!',
           ]);
   }
}
private function assertResponseDelete($response)
{
 if ($response->status() === 302) {
    $response->assertRedirect('/api/login'); 
 } else {
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tiradas del jugador/a eliminadas correctamente',
        ]);
 }
}
//-----------------FIN FUNCIONES HELPER------------
public function test_throw_dice()
{
   $user = $this->getUser('player');
   $this->actingAs($user);

   $targetUser = $this->getUser('Carla');

   $response = $this->post('/api/players/' . $targetUser->id . '/games');

   $this->assertResponseThrowDice($response);
}


public function test_delete()
{
 $user = $this->getUser('player');
 $this->actingAs($user);

 $targetUser = $this->getUser('Carla');

 // Crea un juego para el usuario objetivo
 $game = Game::create(['user_id' => $targetUser->id,'dice1' => 1,
 'dice2' => 2]);

 // Crea un token de autenticación para el usuario
 $token = $user->createToken('TestToken')->accessToken;

 // Ejecuta la acción para eliminar los juegos del usuario
 $response = $this->withToken($token)->delete('/api/players/' . $targetUser->id . '/games');

 // Verifica la respuesta
 $this->assertResponseDelete($response);

 // Verifica que el juego se haya eliminado correctamente
 $this->assertDatabaseMissing('games', ['id' => $game->id]);
}


public function test_getPlayerGames()
{
 // Encuentra un usuario existente en la base de datos de prueba
 $user = User::where('id', 1)->first();

 // Encuentra un juego existente para el usuario en la base de datos de prueba
 $game = Game::where('user_id', $user->id)->first();

 // Autentica al usuario
 $this->actingAs($user, 'api');

 // Ejecuta la acción para obtener los juegos del usuario
 $response = $this->get('/api/players/' . $user->id . '/games');

 // Verifica la respuesta
 $response->assertStatus(200)
   ->assertJson([
       'player_id' => $user->id,
       'games' => [
           [
             'id' => $game->id,
             'user_id' => $user->id,
             // Agrega aquí otros campos de juego que esperas en la respuesta
           ],
       ],
   ]);
}

    
    
}
