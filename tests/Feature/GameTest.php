<?php

namespace Tests\Feature;


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

 
 $game = Game::create(['user_id' => $targetUser->id,'dice1' => 1,
 'dice2' => 2]);


 $token = $user->createToken('TestToken')->accessToken;


 $response = $this->withToken($token)->delete('/api/players/' . $targetUser->id . '/games');

 
 $this->assertResponseDelete($response);

 
 $this->assertDatabaseMissing('games', ['id' => $game->id]);
}


public function test_getPlayerGames()
{
 
 $user = User::where('id', 1)->first();

 
 $game = Game::where('user_id', $user->id)->first();

 
 $this->actingAs($user, 'api');

 
 $response = $this->get('/api/players/' . $user->id . '/games');

 // Verifica la respuesta
 $response->assertStatus(200)
   ->assertJson([
       'player_id' => $user->id,
       'games' => [
           [
             'id' => $game->id,
             'user_id' => $user->id,
            
           ],
       ],
   ]);
}

    
    
}
